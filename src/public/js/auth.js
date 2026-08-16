/**
 * NetQuiz Authentication Gateway - Interactivity Script (Vercel Geist Toaster)
 * Clean, Fast, Accessible & Anti-Slop Client Logic
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize Lucide Icons
  if (window.lucide) {
    window.lucide.createIcons();
  }

  // Password Show/Hide Toggle
  const passwordToggles = document.querySelectorAll(".password-toggle");
  passwordToggles.forEach((toggle) => {
    toggle.addEventListener("click", () => {
      const targetId = toggle.getAttribute("data-target");
      const passwordInput = document.getElementById(targetId);
      if (!passwordInput) return;

      const showIcon = toggle.querySelector(".toggle-icon-show");
      const hideIcon = toggle.querySelector(".toggle-icon-hide");

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        if (showIcon) showIcon.classList.add("hidden");
        if (hideIcon) hideIcon.classList.remove("hidden");
        toggle.setAttribute("aria-label", "Sembunyikan password");
      } else {
        passwordInput.type = "password";
        if (showIcon) showIcon.classList.remove("hidden");
        if (hideIcon) hideIcon.classList.add("hidden");
        toggle.setAttribute("aria-label", "Tampilkan password");
      }
    });
  });

  // DOM Elements
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const loginForm = document.getElementById("login-form");
  const loginEmailInput = document.getElementById("login-email");
  const loginPasswordInput = document.getElementById("login-password");
  const geistToaster = document.getElementById("geist-toaster");

  // Error Display Helpers
  function showError(inputEl, errorElId, message) {
    if (!inputEl) return;
    inputEl.classList.add("invalid");
    const errorEl = document.getElementById(errorElId);
    if (errorEl) {
      errorEl.textContent = message;
    }
  }

  function clearError(inputEl, errorElId) {
    if (!inputEl) return;
    inputEl.classList.remove("invalid");
    const errorEl = document.getElementById(errorElId);
    if (errorEl) {
      errorEl.textContent = "";
    }
  }

  // Floating Geist Toast System (Vercel & Sonner Standard)
  function showGeistToast(message, type = "error") {
    if (!geistToaster) return;

    const toast = document.createElement("div");
    toast.className = "geist-toast";

    const iconName = type === "success" ? "check-circle-2" : "alert-circle";
    const iconClass = type === "success" ? "geist-toast-icon success" : "geist-toast-icon error";

    toast.innerHTML = `
      <i data-lucide="${iconName}" class="${iconClass}"></i>
      <span class="geist-toast-message">${message}</span>
    `;

    geistToaster.appendChild(toast);

    if (window.lucide) {
      window.lucide.createIcons();
    }

    // Auto Dismiss Toast after 2.5s
    setTimeout(() => {
      toast.classList.add("removing");
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 200);
    }, 2500);
  }

  // Input Live Validation
  if (loginEmailInput) {
    loginEmailInput.addEventListener("blur", () => validateLoginEmail());
    loginEmailInput.addEventListener("input", () => {
      if (loginEmailInput.classList.contains("invalid")) validateLoginEmail();
    });
  }

  if (loginPasswordInput) {
    loginPasswordInput.addEventListener("blur", () => validateLoginPassword());
    loginPasswordInput.addEventListener("input", () => {
      if (loginPasswordInput.classList.contains("invalid")) validateLoginPassword();
    });
  }

  function validateLoginEmail() {
    if (!loginEmailInput) return true;
    const val = loginEmailInput.value.trim();
    if (!val) {
      showError(loginEmailInput, "login-email-error", "Alamat email wajib diisi.");
      return false;
    } else if (!emailRegex.test(val)) {
      showError(loginEmailInput, "login-email-error", "Format email tidak valid.");
      return false;
    }
    clearError(loginEmailInput, "login-email-error");
    return true;
  }

  function validateLoginPassword() {
    if (!loginPasswordInput) return true;
    const val = loginPasswordInput.value;
    if (!val) {
      showError(loginPasswordInput, "login-password-error", "Password wajib diisi.");
      return false;
    } else if (val.length < 8) {
      showError(loginPasswordInput, "login-password-error", "Password minimal 8 karakter.");
      return false;
    }
    clearError(loginPasswordInput, "login-password-error");
    return true;
  }

  // Button Loading Helper
  function setBtnLoading(btnEl, isLoading, defaultText = "Masuk ke Platform") {
    if (!btnEl) return;
    const btnTextEl = btnEl.querySelector(".button-text");
    const btnArrowEl = btnEl.querySelector(".button-arrow");
    const spinnerEl = btnEl.querySelector(".loading-spinner");

    if (isLoading) {
      btnEl.disabled = true;
      if (btnTextEl) btnTextEl.textContent = "Memproses...";
      if (btnArrowEl) btnArrowEl.classList.add("hidden");
      if (spinnerEl) spinnerEl.classList.remove("hidden");
    } else {
      btnEl.disabled = false;
      if (btnTextEl) btnTextEl.textContent = defaultText;
      if (btnArrowEl) btnArrowEl.classList.remove("hidden");
      if (spinnerEl) spinnerEl.classList.add("hidden");
    }
  }

  // Form Submit Handler
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const isEmailValid = validateLoginEmail();
      const isPasswordValid = validateLoginPassword();

      if (!isEmailValid || !isPasswordValid) {
        return;
      }

      const submitBtn = document.getElementById("btn-login-submit");
      setBtnLoading(submitBtn, true);

      const formData = {
        email: loginEmailInput ? loginEmailInput.value.trim() : "",
        password: loginPasswordInput ? loginPasswordInput.value : "",
        csrf_token: window.CSRF_TOKEN || "",
      };

      // Backend Authentication Request
      fetch(window.BASE_URL + "/api/login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify(formData),
      })
        .then((response) => {
          if (!response.ok && response.status !== 400 && response.status !== 401 && response.status !== 429) {
            throw new Error("Gagal terhubung ke server otentikasi.");
          }
          return response.json();
        })
        .then((data) => {
          if (data.status === "success" || data.success === true) {
            showGeistToast(data.message || "Login berhasil! Mengalihkan...", "success");
            setTimeout(() => {
              window.location.href = data.redirect || window.BASE_URL + "/";
            }, 650);
          } else {
            setBtnLoading(submitBtn, false);
            showGeistToast(data.message || "Gagal masuk. Periksa kembali email & password Anda.");
            if (data.errors) {
              if (data.errors.email) showError(loginEmailInput, "login-email-error", data.errors.email);
              if (data.errors.password) showError(loginPasswordInput, "login-password-error", data.errors.password);
            }
          }
        })
        .catch((error) => {
          setBtnLoading(submitBtn, false);
          showGeistToast(error.message || "Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.");
        });
    });
  }
});
