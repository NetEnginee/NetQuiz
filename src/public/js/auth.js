/**
 * Authentication Pages Interactivity
 * Author: Senior Frontend Developer & UI/UX Specialist
 * Core Functions: Tabs switcher, password toggle, client-side validation, AJAX submit with loading state.
 */

document.addEventListener("DOMContentLoaded", () => {
  // --- DOM Elements ---
  const tabContainer = document.querySelector(".tab-container");
  const tabLogin = document.getElementById("tab-login");
  const tabSignup = document.getElementById("tab-signup");
  const loginForm = document.getElementById("login-form");
  const signupForm = document.getElementById("signup-form");
  const cardTitle = document.getElementById("auth-title");
  const cardSubtitle = document.getElementById("auth-subtitle");
  const toggleLinkText = document.getElementById("toggle-link-text");
  const generalAlert = document.getElementById("general-alert");
  const generalAlertText = document.getElementById("general-alert-text");

  // Initialize Lucide Icons
  if (window.lucide) {
    window.lucide.createIcons();
  }

  // --- Tab Switching Logic ---
  function setAuthMode(mode) {
    // Clear general alert
    hideAlert();

    // Reset validation status
    clearAllErrors();

    if (mode === "signup") {
      // Update tabs
      if (tabContainer) tabContainer.classList.add("signup-active");
      if (tabLogin) {
        tabLogin.classList.remove("active");
        tabLogin.setAttribute("aria-selected", "false");
      }
      if (tabSignup) {
        tabSignup.classList.add("active");
        tabSignup.setAttribute("aria-selected", "true");
      }

      // Switch Forms
      if (loginForm) {
        loginForm.classList.add("hidden");
        loginForm.classList.remove("active");
      }
      if (signupForm) {
        signupForm.classList.remove("hidden");
        signupForm.classList.add("active");
      }

      // Update Headings
      if (cardTitle) cardTitle.textContent = "Mulai Belajar Sekarang";
      if (cardSubtitle)
        cardSubtitle.textContent =
          "Daftarkan akun gratis untuk mengakses semua fitur RouterOS Quiz.";

      // Update Footer Toggle
      if (toggleLinkText) {
        toggleLinkText.innerHTML =
          'Sudah punya akun? <a href="#" id="link-to-login" class="accent-link">Masuk di sini</a>';
        const linkToLogin = document.getElementById("link-to-login");
        if (linkToLogin) {
          linkToLogin.addEventListener("click", (e) => {
            e.preventDefault();
            setAuthMode("login");
          });
        }
      }

      // Update browser state
      if (window.history.pushState) {
        window.history.pushState(null, "", window.BASE_URL + "/signup");
      }
    } else {
      // Update tabs
      if (tabContainer) tabContainer.classList.remove("signup-active");
      if (tabLogin) {
        tabLogin.classList.add("active");
        tabLogin.setAttribute("aria-selected", "true");
      }
      if (tabSignup) {
        tabSignup.classList.remove("active");
        tabSignup.setAttribute("aria-selected", "false");
      }

      // Switch Forms
      if (signupForm) {
        signupForm.classList.add("hidden");
        signupForm.classList.remove("active");
      }
      if (loginForm) {
        loginForm.classList.remove("hidden");
        loginForm.classList.add("active");
      }

      // Update Headings
      if (cardTitle) cardTitle.textContent = "Selamat Datang Kembali";
      if (cardSubtitle)
        cardSubtitle.textContent =
          "Masukkan email dan password Anda untuk masuk ke dashboard quiz.";

      // Update Footer Toggle
      if (toggleLinkText) {
        toggleLinkText.innerHTML =
          'Belum punya akun? <a href="#" id="link-to-signup" class="accent-link">Daftar sekarang</a>';
        const linkToSignup = document.getElementById("link-to-signup");
        if (linkToSignup) {
          linkToSignup.addEventListener("click", (e) => {
            e.preventDefault();
            setAuthMode("signup");
          });
        }
      }

      // Update browser state
      if (window.history.pushState) {
        window.history.pushState(null, "", window.BASE_URL + "/login");
      }
    }
  }

  // Attach Click Events to Tabs
  if (tabLogin) tabLogin.addEventListener("click", () => setAuthMode("login"));
  if (tabSignup)
    tabSignup.addEventListener("click", () => setAuthMode("signup"));

  // Initialize initial mode set by controller
  if (window.INITIAL_MODE === "signup") {
    setAuthMode("signup");
  } else {
    setAuthMode("login");
  }

  // --- Password Show/Hide Toggle ---
  const passwordToggles = document.querySelectorAll(".password-toggle");
  passwordToggles.forEach((toggle) => {
    toggle.addEventListener("click", () => {
      const targetId = toggle.getAttribute("data-target");
      const passwordInput = document.getElementById(targetId);
      const showIcon = toggle.querySelector(".toggle-icon-show");
      const hideIcon = toggle.querySelector(".toggle-icon-hide");

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        showIcon.classList.add("hidden");
        hideIcon.classList.remove("hidden");
        toggle.setAttribute("aria-label", "Sembunyikan password");
      } else {
        passwordInput.type = "password";
        showIcon.classList.remove("hidden");
        hideIcon.classList.add("hidden");
        toggle.setAttribute("aria-label", "Tampilkan password");
      }
    });
  });

  // --- Client Side Validation ---
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // Helper functions for showing/clearing input errors
  function showError(inputEl, errorElId, message) {
    inputEl.classList.add("invalid");
    const errorEl = document.getElementById(errorElId);
    if (errorEl) {
      errorEl.textContent = message;
    }
  }

  function clearError(inputEl, errorElId) {
    inputEl.classList.remove("invalid");
    const errorEl = document.getElementById(errorElId);
    if (errorEl) {
      errorEl.textContent = "";
    }
  }

  function clearAllErrors() {
    document.querySelectorAll(".form-input").forEach((input) => {
      input.classList.remove("invalid");
    });
    document.querySelectorAll(".error-msg").forEach((msg) => {
      msg.textContent = "";
    });
  }

  // Live validation triggers (on blur and input change)
  // 1. Login Form Validation
  const loginEmailInput = document.getElementById("login-email");
  const loginPasswordInput = document.getElementById("login-password");

  loginEmailInput.addEventListener("blur", () => validateLoginEmail());
  loginEmailInput.addEventListener("input", () => {
    if (loginEmailInput.classList.contains("invalid")) validateLoginEmail();
  });

  loginPasswordInput.addEventListener("blur", () => validateLoginPassword());
  loginPasswordInput.addEventListener("input", () => {
    if (loginPasswordInput.classList.contains("invalid"))
      validateLoginPassword();
  });

  function validateLoginEmail() {
    const val = loginEmailInput.value.trim();
    if (!val) {
      showError(loginEmailInput, "login-email-error", "Email wajib diisi.");
      return false;
    } else if (!emailRegex.test(val)) {
      showError(
        loginEmailInput,
        "login-email-error",
        "Format email tidak valid.",
      );
      return false;
    }
    clearError(loginEmailInput, "login-email-error");
    return true;
  }

  function validateLoginPassword() {
    const val = loginPasswordInput.value;
    if (!val) {
      showError(
        loginPasswordInput,
        "login-password-error",
        "Password wajib diisi.",
      );
      return false;
    } else if (val.length < 8) {
      showError(
        loginPasswordInput,
        "login-password-error",
        "Password minimal harus 8 karakter.",
      );
      return false;
    }
    clearError(loginPasswordInput, "login-password-error");
    return true;
  }

  // 2. Sign Up Form Validation
  const signupNameInput = document.getElementById("signup-name");
  const signupEmailInput = document.getElementById("signup-email");
  const signupPasswordInput = document.getElementById("signup-password");
  const signupConfirmInput = document.getElementById("signup-confirm-password");
  const signupTermsInput = document.getElementById("signup-terms");

  if (signupNameInput) {
    signupNameInput.addEventListener("blur", () => validateSignupName());
    signupNameInput.addEventListener("input", () => {
      if (signupNameInput.classList.contains("invalid")) validateSignupName();
    });
  }

  if (signupEmailInput) {
    signupEmailInput.addEventListener("blur", () => validateSignupEmail());
    signupEmailInput.addEventListener("input", () => {
      if (signupEmailInput.classList.contains("invalid")) validateSignupEmail();
    });
  }

  if (signupPasswordInput) {
    signupPasswordInput.addEventListener("blur", () => {
      validateSignupPassword();
      if (signupConfirmInput && signupConfirmInput.value)
        validateSignupConfirm();
    });
    signupPasswordInput.addEventListener("input", () => {
      if (signupPasswordInput.classList.contains("invalid"))
        validateSignupPassword();
      if (signupConfirmInput && signupConfirmInput.value)
        validateSignupConfirm();
    });
  }

  if (signupConfirmInput) {
    signupConfirmInput.addEventListener("blur", () => validateSignupConfirm());
    signupConfirmInput.addEventListener("input", () => {
      validateSignupConfirm();
    });
  }

  if (signupTermsInput) {
    signupTermsInput.addEventListener("change", () => validateSignupTerms());
  }

  function validateSignupName() {
    const val = signupNameInput.value.trim();
    if (!val) {
      showError(
        signupNameInput,
        "signup-name-error",
        "Nama lengkap wajib diisi.",
      );
      return false;
    }
    clearError(signupNameInput, "signup-name-error");
    return true;
  }

  function validateSignupEmail() {
    const val = signupEmailInput.value.trim();
    if (!val) {
      showError(signupEmailInput, "signup-email-error", "Email wajib diisi.");
      return false;
    } else if (!emailRegex.test(val)) {
      showError(
        signupEmailInput,
        "signup-email-error",
        "Format email tidak valid.",
      );
      return false;
    }
    clearError(signupEmailInput, "signup-email-error");
    return true;
  }

  function validateSignupPassword() {
    const val = signupPasswordInput.value;
    if (!val) {
      showError(
        signupPasswordInput,
        "signup-password-error",
        "Password wajib diisi.",
      );
      return false;
    } else if (val.length < 8) {
      showError(
        signupPasswordInput,
        "signup-password-error",
        "Password minimal harus 8 karakter.",
      );
      return false;
    }
    clearError(signupPasswordInput, "signup-password-error");
    return true;
  }

  function validateSignupConfirm() {
    const password = signupPasswordInput.value;
    const confirm = signupConfirmInput.value;
    if (!confirm) {
      showError(
        signupConfirmInput,
        "signup-confirm-password-error",
        "Ulangi password wajib diisi.",
      );
      return false;
    } else if (password !== confirm) {
      showError(
        signupConfirmInput,
        "signup-confirm-password-error",
        "Konfirmasi password tidak cocok.",
      );
      return false;
    }
    clearError(signupConfirmInput, "signup-confirm-password-error");
    return true;
  }

  function validateSignupTerms() {
    if (!signupTermsInput.checked) {
      const errorEl = document.getElementById("signup-terms-error");
      if (errorEl)
        errorEl.textContent = "Anda harus menyetujui Ketentuan Layanan.";
      return false;
    }
    const errorEl = document.getElementById("signup-terms-error");
    if (errorEl) errorEl.textContent = "";
    return true;
  }

  // --- Alert Banner Helpers ---
  function showAlert(message, type = "error") {
    generalAlertText.textContent = message;
    generalAlert.className = "alert-banner"; // Reset classes

    const iconEl = generalAlert.querySelector(".alert-icon");
    if (iconEl) {
      if (type === "success") {
        generalAlert.classList.add("success");
        iconEl.setAttribute("data-lucide", "check-circle-2");
      } else {
        iconEl.setAttribute("data-lucide", "alert-circle");
      }
      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    generalAlert.classList.remove("hidden");
    // Scroll to top of card so alert is visible
    document
      .querySelector(".auth-card")
      .scrollIntoView({ behavior: "smooth", block: "nearest" });
  }

  function hideAlert() {
    generalAlert.classList.add("hidden");
    generalAlertText.textContent = "";
    generalAlert.className = "alert-banner hidden";
  }

  // --- Form Submission Handling ---

  // Toggle Loading State helper
  function setBtnLoading(btnEl, isLoading, defaultText) {
    const btnTextEl = btnEl.querySelector(".btn-text");
    const spinnerEl = btnEl.querySelector(".loading-spinner");

    if (isLoading) {
      btnEl.disabled = true;
      btnTextEl.textContent = "Memproses...";
      spinnerEl.classList.remove("hidden");
    } else {
      btnEl.disabled = false;
      btnTextEl.textContent = defaultText;
      spinnerEl.classList.add("hidden");
    }
  }

  // 1. Submit Login Form
  loginForm.addEventListener("submit", (e) => {
    e.preventDefault();
    hideAlert();

    const isEmailValid = validateLoginEmail();
    const isPasswordValid = validateLoginPassword();

    if (!isEmailValid || !isPasswordValid) {
      return;
    }

    const submitBtn = document.getElementById("btn-login-submit");
    setBtnLoading(submitBtn, true, "Masuk ke Dashboard");

    const formData = {
      email: loginEmailInput.value.trim(),
      password: loginPasswordInput.value,
      csrf_token: window.CSRF_TOKEN || "",
    };

    // Call backend API
    fetch(window.BASE_URL + "/api/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formData),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Koneksi server terganggu.");
        }
        return response.json();
      })
      .then((data) => {
        if (data.status === "success") {
          // Flash alert and redirect
          showAlert(data.message, "success");
          // Redirect user to dashboard/home
          setTimeout(() => {
            window.location.href = data.redirect || window.BASE_URL + "/";
          }, 800);
        } else {
          setBtnLoading(submitBtn, false, "Masuk ke Dashboard");
          // Display specific validation errors
          if (data.errors) {
            if (data.errors.email)
              showError(
                loginEmailInput,
                "login-email-error",
                data.errors.email,
              );
            if (data.errors.password)
              showError(
                loginPasswordInput,
                "login-password-error",
                data.errors.password,
              );
            if (data.errors.general) showAlert(data.errors.general);
          } else {
            showAlert("Gagal melakukan login. Silakan coba kembali.");
          }
        }
      })
      .catch((err) => {
        setBtnLoading(submitBtn, false, "Masuk ke Dashboard");
        showAlert(
          err.message || "Terjadi kesalahan sistem. Silakan coba sesaat lagi.",
        );
      });
  });

  // 2. Submit Sign Up Form
  if (signupForm) {
    signupForm.addEventListener("submit", (e) => {
      e.preventDefault();
      hideAlert();

      const isNameValid = validateSignupName();
      const isEmailValid = validateSignupEmail();
      const isPasswordValid = validateSignupPassword();
      const isConfirmValid = validateSignupConfirm();
      const isTermsValid = validateSignupTerms();

      if (
        !isNameValid ||
        !isEmailValid ||
        !isPasswordValid ||
        !isConfirmValid ||
        !isTermsValid
      ) {
        return;
      }

      const submitBtn = document.getElementById("btn-signup-submit");
      setBtnLoading(submitBtn, true, "Buat Akun Gratis");

      const formData = {
        name: signupNameInput.value.trim(),
        email: signupEmailInput.value.trim(),
        password: signupPasswordInput.value,
        confirm_password: signupConfirmInput.value,
      };

      // Call backend API
      fetch(window.BASE_URL + "/api/signup", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Koneksi server terganggu.");
          }
          return response.json();
        })
        .then((data) => {
          if (data.status === "success") {
            showAlert(data.message, "success");
            setTimeout(() => {
              window.location.href = data.redirect || window.BASE_URL + "/";
            }, 800);
          } else {
            setBtnLoading(submitBtn, false, "Buat Akun Gratis");
            // Display specific validation errors
            if (data.errors) {
              if (data.errors.name)
                showError(
                  signupNameInput,
                  "signup-name-error",
                  data.errors.name,
                );
              if (data.errors.email)
                showError(
                  signupEmailInput,
                  "signup-email-error",
                  data.errors.email,
                );
              if (data.errors.password)
                showError(
                  signupPasswordInput,
                  "signup-password-error",
                  data.errors.password,
                );
              if (data.errors.confirm_password)
                showError(
                  signupConfirmInput,
                  "signup-confirm-password-error",
                  data.errors.confirm_password,
                );
              if (data.errors.general) showAlert(data.errors.general);
            } else {
              showAlert("Gagal melakukan pendaftaran. Silakan coba kembali.");
            }
          }
        })
        .catch((err) => {
          setBtnLoading(submitBtn, false, "Buat Akun Gratis");
          showAlert(
            err.message ||
              "Terjadi kesalahan sistem. Silakan coba sesaat lagi.",
          );
        });
    });
  }
});
