import "./bootstrap";
import Alpine from "alpinejs";
import * as pdfjsLib from "pdfjs-dist";

pdfjsLib.GlobalWorkerOptions.workerSrc = `https://unpkg.com/pdfjs-dist@5.5.207/build/pdf.worker.min.mjs`;

window.Alpine = Alpine;
Alpine.start();

let currentGroupId = null;
let rotationTimer = null;
const ROTATION_TIME = 10000;
let timeLeft = ROTATION_TIME;

const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
document.addEventListener("DOMContentLoaded", () => {
    // Fade up observer
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

    //  Flash message auto dismiss
    ["flash-success", "flash-error"].forEach((id) => {
        const el = document.getElementById(id);
        if (!el) return;

        setTimeout(() => {
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 500);
        }, 4500);
    });
});

window.dismissFlash = function (id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.opacity = "0";
    setTimeout(() => el.remove(), 500);
};

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
            password: password,
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
    if (input) input.type = input.type === "password" ? "text" : "password";
};

window.handleFileUpload = function (event) {
    const file = event.target.files[0];
    if (file) {
        console.log("Selected file:", file.name);
    }
};

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
    if (e.key === "Escape") window.closeDeleteModal();
});

window.togglePassword = function () {
    const input = document.getElementById("deletePassword");
    const icon = document.getElementById("eyeIcon");
    if (!input) return;

    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";

    icon.innerHTML = isPassword
        ? // EYE-SLASH (currently visible, click to hide)
          `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.83M9.363 5.365A9.466 9.466 0 0112 5c4.478 0 8.268 2.943 9.543 7a9.957 9.957 0 01-1.563 2.942M6.673 6.668A9.955 9.955 0 002.457 12c1.274 4.057 5.065 7 9.543 7a9.454 9.454 0 004.942-1.358" />`
        : // EYE (currently hidden, click to show)
          `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
};

let currentPDF = null;
let annotationCount = 0; // Added to track comment numbers

window.openViewModal = function (
    id,
    title,
    topic,
    fileType,
    published,
    viewUrl,
    downloadUrl,
    isOwner,
) {
    const modal = document.getElementById("viewPaperModal");
    modal.classList.remove("hidden");

    document.getElementById("viewPaperTitle").innerText = title;
    document.getElementById("viewPaperTopic").innerText = topic;
    document.getElementById("viewPaperIcon").innerText =
        fileType === "pdf" ? "📄" : "📝";
    document.getElementById("viewPaperDownloadLink").href = downloadUrl;

    const statusLabel = document.getElementById("viewPaperStatus");
    statusLabel.innerText = published ? "Published" : "Draft";
    statusLabel.className = published
        ? "text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700"
        : "text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500";

    const viewer = document.getElementById("pdfViewer");
    const loading = document.getElementById("viewPaperLoading");
    const fallback = document.getElementById("viewPaperFallback");
    const commentsList = document.getElementById("commentsList");

    viewer.innerHTML = "";
    if (commentsList) commentsList.innerHTML = ""; // Clear sidebar on open
    annotationCount = 0; // Reset counter
    loading.classList.remove("hidden");
    fallback.classList.add("hidden");

    if (fileType === "pdf") {
        pdfjsLib
            .getDocument(viewUrl)
            .promise.then((pdf) => {
                currentPDF = pdf;
                loading.classList.add("hidden");
                renderAllPages(pdf);
            })
            .catch((err) => {
                console.error(err);
                loading.classList.add("hidden");
                fallback.classList.remove("hidden");
            });
    } else {
        loading.classList.add("hidden");
        fallback.classList.remove("hidden");
        document.getElementById("viewPaperFallbackDownload").href = downloadUrl;
    }
};

async function renderAllPages(pdf) {
    const viewer = document.getElementById("pdfViewer");
    for (let i = 1; i <= pdf.numPages; i++) {
        try {
            const page = await pdf.getPage(i);
            const viewport = page.getViewport({ scale: 1.5 });

            // Create relative wrapper for markers
            const wrapper = document.createElement("div");
            wrapper.className =
                "page-wrapper relative shadow-lg bg-white mb-6 mx-auto border border-gray-200";
            wrapper.style.width = viewport.width + "px";
            wrapper.style.height = viewport.height + "px";

            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            wrapper.appendChild(canvas);
            viewer.appendChild(wrapper);

            // Click listener for markers
            wrapper.addEventListener("click", (e) => {
                const rect = wrapper.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                addCommentAtPosition(wrapper, x, y);
            });

            await page.render({ canvasContext: context, viewport: viewport })
                .promise;
        } catch (e) {
            console.error("Error rendering page:", i, e);
        }
    }
}

window.closeViewModal = function () {
    const modal = document.getElementById("viewPaperModal");
    if (modal) modal.classList.add("hidden");

    const viewer = document.getElementById("pdfViewer");
    if (viewer) viewer.innerHTML = "";

    const commentsList = document.getElementById("commentsList");
    if (commentsList) commentsList.innerHTML = "";

    currentPDF = null;
};
