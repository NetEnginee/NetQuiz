/**
 * NetQuiz Authentication Gateway - Interactivity Engine & Canvas FX
 * Standards: Modern Vanilla ES6+, Web Audio API Synthesizer, Canvas Data Stream, Geist Toaster
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize Lucide Icons
  if (window.lucide) {
    window.lucide.createIcons();
  }

  // --- 1. Web Audio API 8-Bit Retro Sound Synthesizer ---
  let soundEnabled = localStorage.getItem("netquiz_sound") !== "false";
  let audioCtx = null;

  function getAudioContext() {
    if (!audioCtx) {
      const AudioContextClass = window.AudioContext || window.webkitAudioContext;
      if (AudioContextClass) {
        audioCtx = new AudioContextClass();
      }
    }
    if (audioCtx && audioCtx.state === "suspended") {
      audioCtx.resume();
    }
    return audioCtx;
  }

  function playPixelSound(type) {
    if (!soundEnabled) return;
    try {
      const ctx = getAudioContext();
      if (!ctx) return;

      const now = ctx.currentTime;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);

      if (type === "click") {
        osc.type = "sawtooth";
        osc.frequency.setValueAtTime(320, now);
        osc.frequency.exponentialRampToValueAtTime(140, now + 0.04);
        gain.gain.setValueAtTime(0.04, now);
        gain.gain.linearRampToValueAtTime(0, now + 0.04);
        osc.start(now);
        osc.stop(now + 0.04);
      } else if (type === "blip") {
        osc.type = "square";
        osc.frequency.setValueAtTime(440, now);
        osc.frequency.exponentialRampToValueAtTime(880, now + 0.08);
        gain.gain.setValueAtTime(0.05, now);
        gain.gain.linearRampToValueAtTime(0, now + 0.08);
        osc.start(now);
        osc.stop(now + 0.08);
      } else if (type === "coin") {
        // High-pitch 8-bit coin pickup chimes (B5 -> E6)
        osc.type = "square";
        osc.frequency.setValueAtTime(987.77, now);
        osc.frequency.setValueAtTime(1318.51, now + 0.08);
        gain.gain.setValueAtTime(0.08, now);
        gain.gain.linearRampToValueAtTime(0, now + 0.28);
        osc.start(now);
        osc.stop(now + 0.28);
      } else if (type === "error") {
        // Low error buzz
        osc.type = "sawtooth";
        osc.frequency.setValueAtTime(180, now);
        osc.frequency.setValueAtTime(110, now + 0.15);
        gain.gain.setValueAtTime(0.08, now);
        gain.gain.linearRampToValueAtTime(0, now + 0.2);
        osc.start(now);
        osc.stop(now + 0.2);
      }
    } catch (e) {
      console.warn("Audio Context Notice:", e);
    }
  }

  // Sound Toggle Control
  const soundToggleBtn = document.getElementById("soundToggleBtn");
  const soundIcon = document.getElementById("soundIcon");
  const soundLabel = document.getElementById("soundLabel");

  function updateSoundButtonUI() {
    if (soundToggleBtn && soundIcon && soundLabel) {
      if (soundEnabled) {
        soundIcon.textContent = "🔊";
        soundLabel.textContent = "Sound: ON";
      } else {
        soundIcon.textContent = "🔇";
        soundLabel.textContent = "Sound: OFF";
      }
    }
  }

  updateSoundButtonUI();

  if (soundToggleBtn) {
    soundToggleBtn.addEventListener("click", () => {
      soundEnabled = !soundEnabled;
      localStorage.setItem("netquiz_sound", soundEnabled ? "true" : "false");
      updateSoundButtonUI();
      if (soundEnabled) {
        playPixelSound("blip");
      }
    });
  }

  // --- 2. Animated Canvas Network Data Stream Background ---
  const canvas = document.getElementById("networkCanvas");
  if (canvas) {
    const ctx = canvas.getContext("2d");
    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    window.addEventListener("resize", () => {
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;
    });

    class DataPacket {
      constructor() {
        this.reset();
      }

      reset() {
        const gridSize = 48;
        this.x = Math.floor(Math.random() * (width / gridSize)) * gridSize;
        this.y = Math.floor(Math.random() * (height / gridSize)) * gridSize;
        this.horizontal = Math.random() > 0.5;
        this.speed = (Math.random() * 1.5 + 1) * (Math.random() > 0.5 ? 1 : -1);
        this.size = 3;
        this.color = Math.random() > 0.5 ? "#0070f3" : "#50e3c2";
        this.life = 0;
        this.maxLife = Math.floor(Math.random() * 140 + 80);
      }

      update() {
        if (this.horizontal) {
          this.x += this.speed;
        } else {
          this.y += this.speed;
        }
        this.life++;
        if (
          this.life > this.maxLife ||
          this.x < 0 ||
          this.x > width ||
          this.y < 0 ||
          this.y > height
        ) {
          this.reset();
        }
      }

      draw() {
        ctx.fillStyle = this.color;
        for (let i = 0; i < 3; i++) {
          const offset = i * 4;
          const px = this.horizontal
            ? this.x - offset * Math.sign(this.speed)
            : this.x;
          const py = this.horizontal
            ? this.y
            : this.y - offset * Math.sign(this.speed);
          ctx.globalAlpha =
            (1 - i * 0.3) * (1 - this.life / this.maxLife) * 0.85;
          ctx.fillRect(px, py, this.size, this.size);
        }
        ctx.globalAlpha = 1;
      }
    }

    const packetCount = Math.min(30, Math.floor(window.innerWidth / 40));
    const packets = Array.from({ length: packetCount }, () => new DataPacket());

    function animateCanvas() {
      ctx.clearRect(0, 0, width, height);
      packets.forEach((p) => {
        p.update();
        p.draw();
      });
      requestAnimationFrame(animateCanvas);
    }

    animateCanvas();
  }

  // --- 3. Password Visibility Toggle ---
  const passwordToggles = document.querySelectorAll(".password-toggle-btn");
  passwordToggles.forEach((toggle) => {
    toggle.addEventListener("click", () => {
      playPixelSound("click");
      const targetId = toggle.getAttribute("data-target");
      const passwordInput = document.getElementById(targetId);
      if (!passwordInput) return;

      const showIcon = toggle.querySelector(".icon-show");
      const hideIcon = toggle.querySelector(".icon-hide");

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        if (showIcon) showIcon.classList.add("hidden");
        if (hideIcon) hideIcon.classList.remove("hidden");
        toggle.setAttribute("aria-label", "Sembunyikan kata sandi");
      } else {
        passwordInput.type = "password";
        if (showIcon) showIcon.classList.remove("hidden");
        if (hideIcon) hideIcon.classList.add("hidden");
        toggle.setAttribute("aria-label", "Tampilkan kata sandi");
      }
    });
  });

  // --- 4. Floating Geist Toast System ---
  const geistToaster = document.getElementById("geist-toaster");

  function showGeistToast(message, type = "error") {
    if (!geistToaster) return;

    const toast = document.createElement("div");
    toast.className = "geist-toast";

    const iconName = type === "success" ? "check-circle-2" : "alert-circle";
    const iconClass =
      type === "success" ? "geist-toast-icon success" : "geist-toast-icon error";

    toast.innerHTML = `
      <i data-lucide="${iconName}" class="${iconClass}"></i>
      <span class="geist-toast-message">${message}</span>
    `;

    geistToaster.appendChild(toast);

    if (window.lucide) {
      window.lucide.createIcons();
    }

    // Auto dismiss after 2.8s
    setTimeout(() => {
      toast.classList.add("removing");
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 220);
    }, 2800);
  }

  // --- 5. Form Validation & Backend Request ---
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const loginForm = document.getElementById("login-form");
  const loginEmailInput = document.getElementById("login-email");
  const loginPasswordInput = document.getElementById("login-password");
  const submitBtn = document.getElementById("btn-login-submit");

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

  function validateEmail() {
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

  function validatePassword() {
    if (!loginPasswordInput) return true;
    const val = loginPasswordInput.value;
    if (!val) {
      showError(loginPasswordInput, "login-password-error", "Kata sandi wajib diisi.");
      return false;
    } else if (val.length < 8) {
      showError(
        loginPasswordInput,
        "login-password-error",
        "Kata sandi minimal 8 karakter."
      );
      return false;
    }
    clearError(loginPasswordInput, "login-password-error");
    return true;
  }

  if (loginEmailInput) {
    loginEmailInput.addEventListener("blur", validateEmail);
    loginEmailInput.addEventListener("input", () => {
      if (loginEmailInput.classList.contains("invalid")) validateEmail();
    });
  }

  if (loginPasswordInput) {
    loginPasswordInput.addEventListener("blur", validatePassword);
    loginPasswordInput.addEventListener("input", () => {
      if (loginPasswordInput.classList.contains("invalid")) validatePassword();
    });
  }

  function setBtnLoading(isLoading) {
    if (!submitBtn) return;
    const btnContent = submitBtn.querySelector(".btn-content");
    const btnSpinner = submitBtn.querySelector(".btn-spinner");

    if (isLoading) {
      submitBtn.disabled = true;
      if (btnContent) btnContent.classList.add("hidden");
      if (btnSpinner) btnSpinner.classList.remove("hidden");
    } else {
      submitBtn.disabled = false;
      if (btnContent) btnContent.classList.remove("hidden");
      if (btnSpinner) btnSpinner.classList.add("hidden");
    }
  }

  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      playPixelSound("click");

      const isEmailValid = validateEmail();
      const isPasswordValid = validatePassword();

      if (!isEmailValid || !isPasswordValid) {
        playPixelSound("error");
        return;
      }

      setBtnLoading(true);

      const payload = {
        email: loginEmailInput ? loginEmailInput.value.trim() : "",
        password: loginPasswordInput ? loginPasswordInput.value : "",
        csrf_token: window.CSRF_TOKEN || "",
      };

      try {
        const response = await fetch(window.BASE_URL + "/api/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(payload),
        });

        const data = await response.json();

        if (response.ok && (data.status === "success" || data.success === true)) {
          playPixelSound("coin");
          showGeistToast(data.message || "Autentikasi berhasil! Mengalihkan...", "success");
          setTimeout(() => {
            window.location.href = data.redirect || window.BASE_URL + "/";
          }, 650);
        } else {
          playPixelSound("error");
          setBtnLoading(false);

          const generalMessage =
            data.message ||
            (data.errors && data.errors.general) ||
            "Gagal masuk. Periksa kembali email & kata sandi Anda.";

          showGeistToast(generalMessage, "error");

          if (data.errors) {
            if (data.errors.email) {
              showError(loginEmailInput, "login-email-error", data.errors.email);
            }
            if (data.errors.password) {
              showError(
                loginPasswordInput,
                "login-password-error",
                data.errors.password
              );
            }
          }
        }
      } catch (err) {
        playPixelSound("error");
        setBtnLoading(false);
        showGeistToast(
          "Terjadi kendala jaringan atau kesalahan server. Silakan coba kembali.",
          "error"
        );
      }
    });
  }
});
