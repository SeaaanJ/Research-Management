import "./bootstrap";
import Alpine from "alpinejs";

window.Alpine = Alpine;
Alpine.start();

/**
 * Global State & Constants
 */
let currentGroupId = null;
let rotationTimer = null;
const ROTATION_TIME = 20000; // 20 seconds
let timeLeft = ROTATION_TIME;

/**
 * Helper: Get CSRF token from the meta tag
 * (Blade syntax like {{ csrf_token() }} does NOT work in .js files)
 */
const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

/**
 * 1. Scroll Animation Observer
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
});

/**
 * 2. Delete Modal Logic
 */

// Fetches a new confirmation string from the server
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
                timeLeft = ROTATION_TIME; // Reset the internal timer
            }, 200);
        }
    } catch (e) {
        console.error("String refresh failed:", e);
    }
}

// Handles the visual progress bar
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
            // Switch color to red when running low on time
            if (timeLeft < 5000) {
                progressBar.classList.add("bg-red-500");
                progressBar.classList.remove("bg-indigo-500");
            } else {
                progressBar.classList.add("bg-indigo-500");
                progressBar.classList.remove("bg-red-500");
            }
        }

        if (timeLeft <= 0) refreshString();
    }, tickRate);
}

/**
 * Modal Control Functions (Attached to window for HTML access)
 */
window.openDeleteModal = function (groupId, groupName) {
    currentGroupId = groupId;
    clearErrors();

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

    // Basic client-side validation
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

    const container = document.getElementById(errorContainerId);
    const text = document.getElementById(errorTextId);
    const input = document.getElementById(inputId);

    if (container) container.classList.remove("hidden");
    if (text) text.textContent = message;
    if (input) input.classList.add("border-red-400");
}

function showGlobalError(message) {
    const container = document.getElementById("globalError");
    const text = document.getElementById("globalErrorText");
    if (container) container.classList.remove("hidden");
    if (text) text.textContent = message;
}

function showGlobalSuccess(message) {
    const container = document.getElementById("globalSuccess");
    const text = document.getElementById("globalSuccessText");
    if (container) container.classList.remove("hidden");
    if (text) text.textContent = message;
}

function clearErrors() {
    ["confirmError", "passwordError", "globalError", "globalSuccess"].forEach(
        (id) => {
            const el = document.getElementById(id);
            if (el) el.classList.add("hidden");
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

// Global Listeners
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") window.closeDeleteModal();
});

window.handleFileUpload = function (event) {
    const file = event.target.files[0];
    if (file) {
        console.log("Selected file:", file.name);
        // You could add logic here to validate size or show a preview
    }
};
