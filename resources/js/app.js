import "./bootstrap";
import Alpine from "alpinejs";
import * as pdfjsLib from "pdfjs-dist";

pdfjsLib.GlobalWorkerOptions.workerSrc = `https://unpkg.com/pdfjs-dist@5.5.207/build/pdf.worker.min.mjs`;

window.Alpine = Alpine;
Alpine.start();

/**
 * Global State
 */
let currentGroupId = null;
let rotationTimer = null;
const ROTATION_TIME = 10000;
let timeLeft = ROTATION_TIME;

// PDF state
let currentPDF = null;
window.currentPaperId = null;

const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

/**
 * 1. DOMContentLoaded — scroll observer + flash dismiss
 */
document.addEventListener("DOMContentLoaded", () => {
    const elements = document.querySelectorAll(".fade-up");
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15 },
    );
    elements.forEach((el) => observer.observe(el));

    ["flash-success", "flash-error"].forEach((id) => {
        const el = document.getElementById(id);
        if (!el) return;
        setTimeout(() => {
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 500);
        }, 4500);
    });
});

/**
 * 2. Flash dismiss manual
 */
window.dismissFlash = function (id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.opacity = "0";
    setTimeout(() => el.remove(), 500);
};

/**
 * 3. Open View Modal
 */
window.openViewModal = function (
    id,
    title,
    topic,
    fileType,
    published,
    viewUrl,
    downloadUrl,
    owner,
) {
    window.currentPaperId = id;

    // Set header info
    document.getElementById("viewPaperTitle").innerText = title;
    document.getElementById("viewPaperTopic").innerText = topic || "";
    document.getElementById("viewPaperIcon").innerText =
        fileType === "pdf" ? "📄" : "📝";
    document.getElementById("viewPaperDownloadLink").href = downloadUrl;

    const statusLabel = document.getElementById("viewPaperStatus");
    statusLabel.innerText = published ? "Published" : "Draft";
    statusLabel.className = published
        ? "text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700"
        : "text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500";

    // Reset comments panel
    const commentsList = document.getElementById("commentsList");
    if (commentsList) {
        commentsList.innerHTML =
            '<p class="text-xs text-gray-400 text-center mt-4">Loading comments...</p>';
    }

    const countBadge = document.getElementById("commentCount");
    if (countBadge) countBadge.textContent = "0";

    // Show modal
    document.getElementById("viewPaperModal").classList.remove("hidden");
    document.body.style.overflow = "hidden";

    // Reset viewer
    const viewer = document.getElementById("pdfViewer");
    const loading = document.getElementById("viewPaperLoading");
    const fallback = document.getElementById("viewPaperFallback");

    viewer.innerHTML = "";
    loading.classList.remove("hidden");
    fallback.classList.add("hidden");

    if (fileType === "pdf") {
        pdfjsLib
            .getDocument(viewUrl)
            .promise.then((pdf) => {
                currentPDF = pdf;
                loading.classList.add("hidden");
                renderAllPages(pdf).then(() => fetchComments(id));
            })
            .catch((err) => {
                console.error(err);
                loading.classList.add("hidden");
                fallback.classList.remove("hidden");
                fetchComments(id);
            });
    } else if (["doc", "docx"].includes(fileType)) {
        loading.classList.add("hidden");
        const frame = document.createElement("iframe");
        frame.src = `https://docs.google.com/gviewer?embedded=true&url=${encodeURIComponent(viewUrl)}`;
        frame.className = "w-full h-full border-0";
        viewer.appendChild(frame);
        fetchComments(id);
    } else {
        loading.classList.add("hidden");
        fallback.classList.remove("hidden");
        const fallbackDownload = document.getElementById(
            "viewPaperFallbackDownload",
        );
        if (fallbackDownload) fallbackDownload.href = downloadUrl;
        fetchComments(id);
    }
};

/**
 * 4. Close View Modal
 */
window.closeViewModal = function () {
    window.currentPaperId = null;

    const modal = document.getElementById("viewPaperModal");
    if (modal) modal.classList.add("hidden");
    document.body.style.overflow = "";

    const viewer = document.getElementById("pdfViewer");
    if (viewer) viewer.innerHTML = "";

    const commentsList = document.getElementById("commentsList");
    if (commentsList) commentsList.innerHTML = "";

    currentPDF = null;
};

/**
 * 5. Render all PDF pages
 */
async function renderAllPages(pdf) {
    const viewer = document.getElementById("pdfViewer");

    for (let i = 1; i <= pdf.numPages; i++) {
        try {
            const page = await pdf.getPage(i);
            const viewport = page.getViewport({ scale: 1.5 });

            const wrapper = document.createElement("div");
            wrapper.className =
                "page-wrapper relative shadow-lg bg-white mb-6 mx-auto border border-gray-200";
            wrapper.style.width = viewport.width + "px";
            wrapper.style.height = viewport.height + "px";
            wrapper.dataset.page = i;

            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            canvas.style.display = "block";

            wrapper.appendChild(canvas);
            viewer.appendChild(wrapper);

            await page.render({ canvasContext: context, viewport }).promise;
        } catch (e) {
            console.error("Error rendering page:", i, e);
        }
    }
}

/**
 * 6. Delete Modal — confirm string rotation
 */
async function refreshString() {
    if (!currentGroupId) return;
    try {
        const response = await fetch(
            `/groups/${currentGroupId}/confirm-delete`,
            {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            },
        );
        const data = await response.json();
        const display = document.getElementById("confirmStringDisplay");
        if (display) {
            display.classList.add("opacity-0");
            setTimeout(() => {
                display.textContent = data.confirm_string;
                display.classList.remove("opacity-0");
                timeLeft = ROTATION_TIME;
            }, 200);
        }
    } catch (e) {
        console.error("String refresh failed:", e);
    }
}

function startProgressBar() {
    const progressBar = document.getElementById("stringProgress");
    const tickRate = 100;
    clearInterval(rotationTimer);
    rotationTimer = setInterval(() => {
        if (!currentGroupId) return;
        timeLeft -= tickRate;
        const percentage = Math.max(0, (timeLeft / ROTATION_TIME) * 100);
        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
            if (timeLeft < 5000) {
                progressBar.classList.add("bg-red-500");
                progressBar.classList.remove("bg-indigo-500");
            } else {
                progressBar.classList.add("bg-indigo-500");
                progressBar.classList.remove("bg-red-500");
            }
        }
        if (timeLeft <= 0) {
            timeLeft = ROTATION_TIME;
            refreshString();
        }
    }, tickRate);
}

window.openDeleteModal = function (groupId, groupName) {
    currentGroupId = groupId;
    clearErrors();
    clearInterval(rotationTimer);
    rotationTimer = null;
    const groupNameDisplay = document.getElementById("modalGroupName");
    if (groupNameDisplay) groupNameDisplay.textContent = groupName;
    document.getElementById("confirmStringInput").value = "";
    document.getElementById("deletePassword").value = "";
    document.getElementById("deleteModal").classList.remove("hidden");
    document.body.style.overflow = "hidden";
    timeLeft = ROTATION_TIME;
    refreshString();
    startProgressBar();
};

window.closeDeleteModal = function () {
    const modal = document.getElementById("deleteModal");
    if (modal) modal.classList.add("hidden");
    document.body.style.overflow = "";
    clearInterval(rotationTimer);
    currentGroupId = null;
    clearErrors();
};

window.submitDelete = function () {
    clearErrors();
    const confirmString = document
        .getElementById("confirmStringInput")
        .value.trim();
    const password = document.getElementById("deletePassword").value;
    const btn = document.getElementById("deleteBtn");

    if (!confirmString) {
        showFieldError("confirm", "Please enter the confirmation code.");
        shakeInput("confirmStringInput");
        return;
    }
    if (!password) {
        showFieldError("password", "Please enter your password.");
        shakeInput("deletePassword");
        return;
    }

    btn.textContent = "Deleting...";
    btn.disabled = true;

    fetch(`/groups/${currentGroupId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": getCsrfToken(),
            Accept: "application/json",
        },
        body: JSON.stringify({
            confirm_string: confirmString,
            password,
            _method: "DELETE",
        }),
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                showGlobalSuccess(data.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                if (data.error === "password") {
                    showFieldError("password", data.message);
                    shakeInput("deletePassword");
                } else if (data.error === "confirm_string") {
                    showFieldError("confirm", data.message);
                    shakeInput("confirmStringInput");
                } else {
                    showGlobalError(data.message || "Request failed.");
                }
                resetBtn();
            }
        })
        .catch(() => {
            showGlobalError("Network error. Please try again.");
            resetBtn();
        });
};

window.togglePassword = function () {
    const input = document.getElementById("deletePassword");
    const icon = document.getElementById("eyeIcon");
    if (!input) return;

    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";

    if (icon) {
        icon.innerHTML = isPassword
            ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.83M9.363 5.365A9.466 9.466 0 0112 5c4.478 0 8.268 2.943 9.543 7a9.957 9.957 0 01-1.563 2.942M6.673 6.668A9.955 9.955 0 002.457 12c1.274 4.057 5.065 7 9.543 7a9.454 9.454 0 004.942-1.358" />`
            : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
    }
};

window.handleFileUpload = function (event) {
    const file = event.target.files[0];
    if (file) console.log("Selected file:", file.name);
};

/**
 * UI Helpers
 */
function showFieldError(field, message) {
    const errorContainerId =
        field === "confirm" ? "confirmError" : "passwordError";
    const errorTextId =
        field === "confirm" ? "confirmErrorText" : "passwordErrorText";
    const inputId =
        field === "confirm" ? "confirmStringInput" : "deletePassword";
    document.getElementById(errorContainerId)?.classList.remove("hidden");
    const text = document.getElementById(errorTextId);
    if (text) text.textContent = message;
    document.getElementById(inputId)?.classList.add("border-red-400");
}

function showGlobalError(message) {
    document.getElementById("globalError")?.classList.remove("hidden");
    const text = document.getElementById("globalErrorText");
    if (text) text.textContent = message;
}

function showGlobalSuccess(message) {
    document.getElementById("globalSuccess")?.classList.remove("hidden");
    const text = document.getElementById("globalSuccessText");
    if (text) text.textContent = message;
}

function clearErrors() {
    ["confirmError", "passwordError", "globalError", "globalSuccess"].forEach(
        (id) => {
            document.getElementById(id)?.classList.add("hidden");
        },
    );
    document
        .getElementById("confirmStringInput")
        ?.classList.remove("border-red-400");
    document
        .getElementById("deletePassword")
        ?.classList.remove("border-red-400");
}

function shakeInput(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add("animate-shake");
        setTimeout(() => el.classList.remove("animate-shake"), 500);
    }
}

function resetBtn() {
    const btn = document.getElementById("deleteBtn");
    if (btn) {
        btn.textContent = "Delete Group";
        btn.disabled = false;
    }
}

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        window.closeDeleteModal?.();
        window.closeViewModal?.();
    }
});

// ─────────────────────────────────────────────
// COMMENTS
// ─────────────────────────────────────────────

/**
 * Fetch all comments for a paper and render them.
 */
async function fetchComments(paperId) {
    const commentsList = document.getElementById("commentsList");

    try {
        const res = await fetch(`/papers/${paperId}/comments`, {
            headers: { Accept: "application/json" },
        });

        if (!res.ok) {
            if (commentsList) {
                commentsList.innerHTML =
                    '<p class="text-xs text-red-400 text-center mt-4">Failed to load comments.</p>';
            }
            return;
        }

        const data = await res.json();

        if (!Array.isArray(data)) {
            if (commentsList) {
                commentsList.innerHTML =
                    '<p class="text-xs text-gray-400 text-center mt-4">No comments yet.</p>';
            }
            return;
        }

        renderComments(data, paperId);
    } catch (e) {
        console.error("Failed to fetch comments:", e);
        if (commentsList) {
            commentsList.innerHTML =
                '<p class="text-xs text-red-400 text-center mt-4">Error loading comments.</p>';
        }
    }
}

/**
 * Render a full list of comments, replacing whatever is in the panel.
 */
function renderComments(comments, paperId) {
    const commentsList = document.getElementById("commentsList");
    if (!commentsList) return;

    commentsList.innerHTML = "";

    if (comments.length === 0) {
        commentsList.innerHTML =
            '<p class="text-xs text-gray-400 text-center mt-4">No comments yet. Be the first!</p>';

        const countBadge = document.getElementById("commentCount");
        if (countBadge) countBadge.textContent = "0";
        return;
    }

    comments.forEach((c) => renderSingleComment(c, paperId, false));

    // Update count badge to reflect all loaded comments
    const countBadge = document.getElementById("commentCount");
    if (countBadge) countBadge.textContent = comments.length;
}

/**
 * Append a single comment to the list.
 *
 * @param {object}  comment   - Comment data. Supports both nested (comment.user.first_name)
 *                              and flat (comment.first_name) shapes returned by the API.
 * @param {number}  paperId   - The paper this comment belongs to.
 * @param {boolean} [incrementCount=true] - Whether to bump the count badge.
 *                              Pass false when doing an initial bulk render.
 */
function renderSingleComment(comment, paperId, incrementCount = true) {
    const commentsList = document.getElementById("commentsList");
    if (!commentsList) return;

    // Remove the "no comments" placeholder if present
    const placeholder = commentsList.querySelector(
        "p.text-gray-400, p.text-red-400",
    );
    if (placeholder) placeholder.remove();

    // ── Safely resolve display name ──────────────────────────────────────
    // The GET /comments endpoint may return { user: { first_name, last_name }, user_id, ... }
    // The POST /comments endpoint may return a flat { first_name, last_name, user_id, ... }
    const firstName =
        comment.user?.first_name ?? comment.first_name ?? "Unknown";
    const lastName = comment.user?.last_name ?? comment.last_name ?? "";
    const initial = (firstName[0] ?? "?").toUpperCase();

    // ── Safely resolve user_id for delete permission check ───────────────
    const commentUserId = comment.user_id ?? comment.user?.id ?? null;

    const canDelete =
        (commentUserId !== null && commentUserId === window.__authUserId) ||
        window.__groupOwnerId === window.__authUserId;

    // ── Format date ──────────────────────────────────────────────────────
    const createdAt = comment.created_at
        ? new Date(comment.created_at).toLocaleDateString(undefined, {
              year: "numeric",
              month: "short",
              day: "numeric",
          })
        : "";

    // ── Build DOM element ────────────────────────────────────────────────
    const item = document.createElement("div");
    item.id = `comment-${comment.id}`;
    item.className =
        "p-3 bg-gray-50 rounded-lg border border-gray-100 text-sm space-y-1";

    item.innerHTML = `
        <div class="flex items-start justify-between gap-2">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center
                            text-xs font-bold text-indigo-600 shrink-0">
                    ${initial}
                </div>
                <span class="font-semibold text-gray-700 text-xs">
                    ${escapeHtml(firstName)} ${escapeHtml(lastName)}
                </span>
            </div>
            ${
                canDelete
                    ? `<button
                            onclick="window.deleteComment(${comment.id}, ${paperId})"
                            title="Delete comment"
                            class="text-gray-300 hover:text-red-500 transition text-base
                                   shrink-0 leading-none font-bold">
                           &times;
                       </button>`
                    : ""
            }
        </div>
        <p class="text-gray-800 break-all pl-8">${escapeHtml(comment.comment)}</p>
        <p class="text-xs text-gray-400 pl-8">${createdAt}</p>
    `;

    commentsList.appendChild(item);

    // ── Optionally bump the count badge ──────────────────────────────────
    if (incrementCount) {
        const countBadge = document.getElementById("commentCount");
        if (countBadge) {
            countBadge.textContent =
                parseInt(countBadge.textContent || "0", 10) + 1;
        }
    }
}

/**
 * Post a new comment.
 */
window.submitComment = async function () {
    const paperId = window.currentPaperId;
    if (!paperId) return;

    const input = document.getElementById("commentInput");
    const btn = document.getElementById("commentSubmitBtn");
    if (!input || !btn) return;

    const comment = input.value.trim();
    if (!comment) {
        input.focus();
        return;
    }

    btn.textContent = "Posting...";
    btn.disabled = true;

    try {
        const res = await fetch(`/papers/${paperId}/comments`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
                Accept: "application/json",
            },
            body: JSON.stringify({ comment }),
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            console.error("Failed to post comment:", err);
            return;
        }

        const data = await res.json();

        // The response may or may not include first_name/last_name at top level.
        // Merge in the authenticated user's name as a fallback so the UI never
        // shows "Unknown" right after posting.
        const enriched = {
            ...data,
            first_name:
                data.first_name ??
                data.user?.first_name ??
                window.__authFirstName ??
                "You",
            last_name:
                data.last_name ??
                data.user?.last_name ??
                window.__authLastName ??
                "",
            user_id: data.user_id ?? data.user?.id ?? window.__authUserId,
        };

        input.value = "";
        renderSingleComment(enriched, paperId, true);

        // Scroll comments list to bottom so the new comment is visible
        const commentsList = document.getElementById("commentsList");
        if (commentsList) {
            commentsList.scrollTop = commentsList.scrollHeight;
        }
    } catch (e) {
        console.error("Failed to post comment:", e);
    } finally {
        btn.textContent = "Post";
        btn.disabled = false;
    }
};

/**
 * Delete a comment.
 */
window.deleteComment = async function (commentId, paperId) {
    if (!confirm("Delete this comment?")) return;

    try {
        const res = await fetch(`/papers/${paperId}/comments/${commentId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": getCsrfToken(),
                Accept: "application/json",
            },
        });

        if (!res.ok) {
            console.error("Failed to delete comment, status:", res.status);
            return;
        }

        document.getElementById(`comment-${commentId}`)?.remove();

        // Decrement badge (floor at 0)
        const countBadge = document.getElementById("commentCount");
        if (countBadge) {
            const current = parseInt(countBadge.textContent || "1", 10);
            countBadge.textContent = Math.max(0, current - 1);
        }

        // If no comments remain, show placeholder
        const commentsList = document.getElementById("commentsList");
        if (commentsList && commentsList.children.length === 0) {
            commentsList.innerHTML =
                '<p class="text-xs text-gray-400 text-center mt-4">No comments yet. Be the first!</p>';
        }
    } catch (e) {
        console.error("Failed to delete comment:", e);
    }
};

/**
 * Escape user-supplied text before injecting into innerHTML.
 */
function escapeHtml(str) {
    if (str == null) return "";
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

const input = document.getElementById("institution");
const datalist = document.getElementById("institution-list");

input.addEventListener("input", async (e) => {
    const query = e.target.value;

    // Only search once the user types at least 3 characters
    if (query.length < 3) return;

    try {
        // Fetching from a free global university API
        const response = await fetch(
            `http://universities.hipolabs.com/search?name=${query}`,
        );
        const data = await response.json();

        // Clear previous suggestions
        datalist.innerHTML = "";

        // Filter for unique names and add to datalist
        const uniqueNames = [...new Set(data.map((item) => item.name))];
        uniqueNames.slice(0, 15).forEach((name) => {
            const option = document.createElement("option");
            option.value = name;
            datalist.appendChild(option);
        });
    } catch (error) {
        console.error("Error fetching institutions:", error);
    }
});
