/**
 * JavaScript Controller for RouterOS Quiz Settings Page
 * Features: Tab Navigation, Client-side Form Validation, AJAX Request Handler, Dynamic DOM updates.
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initial profile inputs tracking & submission locks
  const trackingUsernameInput = document.getElementById("username");
  const trackingEmailInput = document.getElementById("email");

  let initialUsername = trackingUsernameInput
    ? trackingUsernameInput.value.trim()
    : "";
  let initialEmail = trackingEmailInput ? trackingEmailInput.value.trim() : "";
  let isSubmittingProfile = false;
  let isSubmittingPassword = false;

  // ----------------------------------------------------
  // 1. Sidebar Tab Switching Logic
  // ----------------------------------------------------
  const tabButtons = document.querySelectorAll(".sidebar-nav-item");
  const settingsCards = document.querySelectorAll(".settings-card");

  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const targetId = button.getAttribute("data-target");

      // Deactivate all buttons
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      // Activate current button
      button.classList.add("active");

      // Hide all cards
      settingsCards.forEach((card) => card.classList.remove("active"));
      // Show targeted card
      const targetCard = document.getElementById(targetId);
      if (targetCard) {
        targetCard.classList.add("active");
      }
    });
  });

  // ----------------------------------------------------
  // 1.5. Interactive Avatar Upload & Local Storage Logic
  // ----------------------------------------------------
  const avatarTriggerBtn = document.getElementById("avatar-trigger-btn");
  const avatarFileInput = document.getElementById("avatar-file-input");
  const avatarPreviewImg = document.getElementById("avatar-preview-img");
  const avatarInitials = document.querySelector(
    ".profile-avatar-large .avatar-initials",
  );

  // Load custom profile avatar from localStorage on load if set
  const savedAvatar = localStorage.getItem("user_avatar");
  if (savedAvatar) {
    if (avatarPreviewImg && avatarInitials) {
      avatarPreviewImg.src = savedAvatar;
      avatarPreviewImg.classList.remove("hidden");
      avatarInitials.classList.add("hidden");
    }
  }

  if (avatarTriggerBtn && avatarFileInput) {
    avatarTriggerBtn.addEventListener("click", () => {
      avatarFileInput.click();
    });

    avatarFileInput.addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (file) {
        // Ensure file size is valid (limit to 100KB)
        if (file.size > 100 * 1024) {
          const profileForm = document.getElementById("profile-settings-form");
          if (profileForm) {
            showBannerAlert(
              profileForm,
              "danger",
              "Ukuran file foto profil maksimal adalah 100KB.",
            );
          }
          return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
          const dataUrl = event.target.result;

          // Save to localStorage
          localStorage.setItem("user_avatar", dataUrl);

          // Update settings sidebar avatar preview
          if (avatarPreviewImg && avatarInitials) {
            avatarPreviewImg.src = dataUrl;
            avatarPreviewImg.classList.remove("hidden");
            avatarInitials.classList.add("hidden");
          }

          // Update header avatar preview
          const headerAvatarImg = document.querySelector(".header-avatar-img");
          const headerAvatarInitials = document.querySelector(
            ".header-avatar-initials",
          );
          if (headerAvatarImg && headerAvatarInitials) {
            headerAvatarImg.src = dataUrl;
            headerAvatarImg.classList.remove("hidden");
            headerAvatarInitials.classList.add("hidden");
          }

          // Success Alert
          const profileForm = document.getElementById("profile-settings-form");
          if (profileForm) {
            showBannerAlert(
              profileForm,
              "success",
              "Foto profil Anda berhasil diperbarui!",
            );
          }
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // ----------------------------------------------------
  // 2. Password Visibility Toggle
  // ----------------------------------------------------
  const passwordToggles = document.querySelectorAll(".password-toggle");
  passwordToggles.forEach((toggle) => {
    toggle.addEventListener("click", () => {
      const targetId = toggle.getAttribute("data-target");
      const inputField = document.getElementById(targetId);
      const eyeIconShow = toggle.querySelector(".toggle-icon-show");
      const eyeIconHide = toggle.querySelector(".toggle-icon-hide");

      if (inputField) {
        if (inputField.type === "password") {
          inputField.type = "text";
          if (eyeIconShow) eyeIconShow.classList.add("hidden");
          if (eyeIconHide) eyeIconHide.classList.remove("hidden");
        } else {
          inputField.type = "password";
          if (eyeIconShow) eyeIconShow.classList.remove("hidden");
          if (eyeIconHide) eyeIconHide.classList.add("hidden");
        }
      }
    });
  });

  // Helper functions for displaying errors
  const showError = (inputId, errorId, message) => {
    const input = document.getElementById(inputId);
    const errorSpan = document.getElementById(errorId);
    if (input) input.classList.add("invalid");
    if (errorSpan) {
      errorSpan.textContent = message;
      errorSpan.classList.add("visible");
    }
  };

  const clearErrors = (form) => {
    form
      .querySelectorAll(".form-input")
      .forEach((input) => input.classList.remove("invalid"));
    form.querySelectorAll(".error-msg").forEach((span) => {
      span.textContent = "";
      span.classList.remove("visible");
    });
    const alert = form.querySelector(".alert-banner");
    if (alert) {
      alert.classList.add("hidden");
      alert.className = "alert-banner hidden";
    }
  };

  const showBannerAlert = (form, type, message) => {
    const alert = form.querySelector(".alert-banner");
    const textSpan = alert.querySelector("span");
    const iconContainer = alert.querySelector(".alert-icon");

    if (alert && textSpan) {
      // Reset classes
      alert.className = `alert-banner ${type}`;
      textSpan.textContent = message;

      // Set alert icon
      if (iconContainer) {
        if (type === "success") {
          iconContainer.setAttribute("data-lucide", "check-circle-2");
        } else {
          iconContainer.setAttribute("data-lucide", "alert-circle");
        }
        // Refresh Lucide icon rendering
        if (window.lucide) {
          window.lucide.createIcons();
        }
      }
      alert.classList.remove("hidden");
      alert.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }
  };

  // ----------------------------------------------------
  // 3. Profile Information Form Handler
  // ----------------------------------------------------
  const profileForm = document.getElementById("profile-settings-form");
  if (profileForm) {
    profileForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Click injection / rapid click prevention
      if (isSubmittingProfile) return;

      const usernameInput = document.getElementById("username");
      const emailInput = document.getElementById("email");
      const username = usernameInput.value.trim();
      const email = emailInput.value.trim();

      // Check if there are no changes at all
      if (username === initialUsername && email === initialEmail) {
        showBannerAlert(
          profileForm,
          "danger",
          "Belum ada perubahan data yang dilakukan.",
        );
        return;
      }

      isSubmittingProfile = true;
      clearErrors(profileForm);

      const submitBtn = profileForm.querySelector('button[type="submit"]');
      const btnText = submitBtn.querySelector(".btn-text");
      const spinner = submitBtn.querySelector(".loading-spinner");
      let hasError = false;

      // Client Validation
      if (!username) {
        showError(
          "username",
          "username-error",
          "Nama lengkap tidak boleh kosong.",
        );
        hasError = true;
      }

      if (!email) {
        showError("email", "email-error", "Email tidak boleh kosong.");
        hasError = true;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError("email", "email-error", "Format email tidak valid.");
        hasError = true;
      }

      if (hasError) {
        isSubmittingProfile = false;
        return;
      }

      // Disable buttons and show spinner
      submitBtn.disabled = true;
      if (btnText) btnText.textContent = "Menyimpan...";
      if (spinner) spinner.classList.remove("hidden");

      try {
        const response = await fetch(
          `${window.BASE_URL}/api/settings/profile`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ username, email, csrf_token: window.CSRF_TOKEN || "" }),
          },
        );

        const result = await response.json();

        if (response.ok && result.status === "success") {
          showBannerAlert(profileForm, "success", result.message);

          // Update initial state tracking
          initialUsername = username;
          initialEmail = email;

          // Dynamic updates to avatars and headers
          const initials = username.charAt(0).toUpperCase();

          // Update large avatar initials on settings sidebar
          const largeAvatarInitials = document.querySelector(
            ".profile-avatar-large .avatar-initials",
          );
          if (largeAvatarInitials) {
            largeAvatarInitials.textContent = initials;
          }

          // Update sidebar name and email
          const sidebarName = document.querySelector(".profile-name-title");
          const sidebarEmail = document.querySelector(
            ".profile-email-subtitle",
          );
          if (sidebarName) sidebarName.textContent = username;
          if (sidebarEmail) sidebarEmail.textContent = email;

          // Update header avatar & dropdown name and email
          const headerAvatarInitials = document.querySelector(
            ".header-avatar-initials",
          );
          const headerNameSpan = document.querySelector(
            "#profile-dropdown-trigger span",
          );
          const dropdownHeaderName = document.querySelector(
            "#profile-dropdown-menu div span:first-child",
          );
          const dropdownHeaderEmail = document.querySelector(
            "#profile-dropdown-menu div span:last-child",
          );

          if (headerAvatarInitials) headerAvatarInitials.textContent = initials;
          if (headerNameSpan) headerNameSpan.innerHTML = username;
          if (dropdownHeaderName) dropdownHeaderName.textContent = username;
          if (dropdownHeaderEmail) dropdownHeaderEmail.textContent = email;
        } else {
          // Handle Validation or Database errors returned from backend
          if (result.errors) {
            Object.keys(result.errors).forEach((key) => {
              if (key === "general") {
                showBannerAlert(profileForm, "danger", result.errors[key]);
              } else {
                showError(key, `${key}-error`, result.errors[key]);
              }
            });
          } else {
            showBannerAlert(
              profileForm,
              "danger",
              result.message || "Terjadi kesalahan sistem.",
            );
          }
        }
      } catch (err) {
        showBannerAlert(
          profileForm,
          "danger",
          "Gagal menghubungi server. Periksa koneksi internet Anda.",
        );
      } finally {
        // Restore button state and unlock
        submitBtn.disabled = false;
        if (btnText) btnText.textContent = "Simpan Perubahan";
        if (spinner) spinner.classList.add("hidden");
        isSubmittingProfile = false;
      }
    });
  }

  // ----------------------------------------------------
  // 4. Password Settings Form Handler
  // ----------------------------------------------------
  const passwordForm = document.getElementById("password-settings-form");
  if (passwordForm) {
    passwordForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Click injection / rapid click prevention
      if (isSubmittingPassword) return;
      isSubmittingPassword = true;

      clearErrors(passwordForm);

      const currentPassInput = document.getElementById("current_password");
      const newPassInput = document.getElementById("new_password");
      const confirmPassInput = document.getElementById("confirm_password");
      const submitBtn = passwordForm.querySelector('button[type="submit"]');
      const btnText = submitBtn.querySelector(".btn-text");
      const spinner = submitBtn.querySelector(".loading-spinner");

      const current_password = currentPassInput.value;
      const new_password = newPassInput.value;
      const confirm_password = confirmPassInput.value;
      let hasError = false;

      // Client Validation
      if (!current_password) {
        showError(
          "current_password",
          "current_password-error",
          "Password saat ini wajib diisi.",
        );
        hasError = true;
      }

      if (!new_password) {
        showError(
          "new_password",
          "new_password-error",
          "Password baru wajib diisi.",
        );
        hasError = true;
      } else if (new_password.length < 8) {
        showError(
          "new_password",
          "new_password-error",
          "Password baru minimal harus 8 karakter.",
        );
        hasError = true;
      }

      if (new_password !== confirm_password) {
        showError(
          "confirm_password",
          "confirm_password-error",
          "Konfirmasi password baru tidak cocok.",
        );
        hasError = true;
      }

      if (hasError) {
        isSubmittingPassword = false;
        return;
      }

      // Disable buttons and show spinner
      submitBtn.disabled = true;
      if (btnText) btnText.textContent = "Mengubah Password...";
      if (spinner) spinner.classList.remove("hidden");

      try {
        const response = await fetch(
          `${window.BASE_URL}/api/settings/password`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              current_password,
              new_password,
              confirm_password,
              csrf_token: window.CSRF_TOKEN || "",
            }),
          },
        );

        const result = await response.json();

        if (response.ok && result.status === "success") {
          showBannerAlert(passwordForm, "success", result.message);
          passwordForm.reset();
        } else {
          if (result.errors) {
            Object.keys(result.errors).forEach((key) => {
              if (key === "general") {
                showBannerAlert(passwordForm, "danger", result.errors[key]);
              } else {
                showError(key, `${key}-error`, result.errors[key]);
              }
            });
          } else {
            showBannerAlert(
              passwordForm,
              "danger",
              result.message || "Terjadi kesalahan sistem.",
            );
          }
        }
      } catch (err) {
        showBannerAlert(
          passwordForm,
          "danger",
          "Gagal menghubungi server. Periksa koneksi internet Anda.",
        );
      } finally {
        // Restore button state and unlock
        submitBtn.disabled = false;
        if (btnText) btnText.textContent = "Perbarui Password";
        if (spinner) spinner.classList.add("hidden");
        isSubmittingPassword = false;
      }
    });
  }

  // Initialize Lucide Icons on load
  if (window.lucide) {
    window.lucide.createIcons();
  }
});
