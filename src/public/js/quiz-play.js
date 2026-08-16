/**
 * NetQuiz Quiz Player Module (Clean State Architecture)
 */
(function () {
  "use strict";

  class NetQuizPlayer {
    constructor(config) {
      this.quizId = parseInt(config.quizId, 10) || 0;
      this.userId = parseInt(config.userId, 10) || 0;
      this.durationSeconds = parseInt(config.durationSeconds, 10) || 0;
      this.initialTimeLeft =
        parseInt(config.timeLeft, 10) || this.durationSeconds;
      this.isResumed = !!config.isResumed;

      this.storageKey = `quiz_timer_${this.userId}_${this.quizId}`;
      this.currentSlide = 0;
      this.isSubmitting = false;
      this.timerInterval = null;

      this.initDom();
      this.initState();
      this.bindEvents();
    }

    initDom() {
      this.timerText = document.getElementById("timer-text");
      this.timerPill = document.getElementById("quiz-timer");
      this.quizForm = document.getElementById("quiz-form");
      this.timeLeftInput = document.getElementById("time_left");
      this.blocks = Array.from(document.querySelectorAll(".question-block"));
      this.totalSlides = this.blocks.length;
      this.btnPrev = document.getElementById("btn-prev");
      this.btnNext = document.getElementById("btn-next");
      this.btnSubmit = document.getElementById("btn-submit-quiz");
      this.pageButtons = Array.from(document.querySelectorAll(".page-number"));

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    initState() {
      // 1. Timer Init
      if (this.durationSeconds > 0 && this.timerText) {
        let targetTimestamp = localStorage.getItem(this.storageKey);

        if (targetTimestamp) {
          this.timeLeft = Math.max(
            0,
            Math.floor((parseInt(targetTimestamp, 10) - Date.now()) / 1000),
          );
          if (this.timeLeft <= 0 || this.timeLeft > this.initialTimeLeft) {
            this.timeLeft = this.initialTimeLeft;
            targetTimestamp = Date.now() + this.timeLeft * 1000;
            localStorage.setItem(this.storageKey, targetTimestamp);
          }
        } else {
          this.timeLeft = this.initialTimeLeft;
          targetTimestamp = Date.now() + this.timeLeft * 1000;
          localStorage.setItem(this.storageKey, targetTimestamp);
        }

        this.updateTimerDisplay();
        this.startTimer();
      }

      this.updateSlider();
      this.checkSubmitReadiness();
    }

    startTimer() {
      this.timerInterval = setInterval(() => {
        this.timeLeft--;

        if (this.timeLeft <= 0) {
          clearInterval(this.timerInterval);
          this.timeLeft = 0;
          this.updateTimerDisplay();
          this.handleTimeExpired();
        } else {
          this.updateTimerDisplay();
        }
      }, 1000);
    }

    updateTimerDisplay() {
      if (!this.timerText) return;

      const minutes = Math.floor(this.timeLeft / 60);
      const seconds = this.timeLeft % 60;
      this.timerText.textContent = `${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;

      if (this.timeLeftInput) {
        this.timeLeftInput.value = this.timeLeft;
      }

      if (this.timerPill) {
        if (this.timeLeft <= 60 && this.timeLeft > 0) {
          this.timerPill.classList.add("warning");
        } else {
          this.timerPill.classList.remove("warning");
        }
      }
    }

    handleTimeExpired() {
      localStorage.removeItem(this.storageKey);

      const overlay = document.createElement("div");
      overlay.style.position = "fixed";
      overlay.style.top = "0";
      overlay.style.left = "0";
      overlay.style.width = "100vw";
      overlay.style.height = "100vh";
      overlay.style.backgroundColor = "rgba(15, 23, 42, 0.95)";
      overlay.style.backdropFilter = "blur(10px)";
      overlay.style.color = "#ffffff";
      overlay.style.display = "flex";
      overlay.style.flexDirection = "column";
      overlay.style.justifyContent = "center";
      overlay.style.alignItems = "center";
      overlay.style.zIndex = "99999";
      overlay.style.fontFamily = "'Plus Jakarta Sans', sans-serif";

      overlay.innerHTML = `
                <div style="background: #ef4444; border-radius: 50%; width: 72px; height: 72px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                    <i data-lucide="clock" style="width: 36px; height: 36px; color: #fff;"></i>
                </div>
                <h2 style="font-size:2rem; font-weight:800; margin-bottom:0.5rem; color:#f8fafc;">Waktu Ujian Telah Habis</h2>
                <p style="font-size:1rem; color:#94a3b8; font-weight:500;">Jawaban Anda sedang dikumpulkan otomatis oleh sistem...</p>
            `;
      document.body.appendChild(overlay);
      if (window.lucide) window.lucide.createIcons();

      if (this.quizForm) {
        this.quizForm
          .querySelectorAll("input[required]")
          .forEach((input) => input.removeAttribute("required"));
        this.isSubmitting = true;
        setTimeout(() => {
          this.quizForm.submit();
        }, 1800);
      }
    }

    updateSlider() {
      this.blocks.forEach((block, index) => {
        if (index === this.currentSlide) {
          block.classList.add("active");
        } else {
          block.classList.remove("active");
        }
      });

      if (this.btnPrev) this.btnPrev.disabled = this.currentSlide === 0;
      if (this.btnNext)
        this.btnNext.disabled = this.currentSlide === this.totalSlides - 1;

      this.pageButtons.forEach((btn, index) => {
        if (index === this.currentSlide) {
          btn.classList.add("active");
        } else {
          btn.classList.remove("active");
        }
      });
    }

    checkSubmitReadiness() {
      const answeredCount = document.querySelectorAll(
        '.options-list input[type="radio"]:checked',
      ).length;

      if (this.btnSubmit) {
        if (answeredCount === this.totalSlides) {
          this.btnSubmit.disabled = false;
          this.btnSubmit.style.opacity = "1";
          this.btnSubmit.style.cursor = "pointer";
        } else {
          this.btnSubmit.disabled = true;
          this.btnSubmit.style.opacity = "0.5";
          this.btnSubmit.style.cursor = "not-allowed";
        }
      }

      this.blocks.forEach((block, index) => {
        const radioChecked = block.querySelector(
          '.options-list input[type="radio"]:checked',
        );
        const pageBtn = document.querySelector(
          `.page-number[data-slide="${index}"]`,
        );
        if (pageBtn) {
          if (radioChecked) {
            pageBtn.classList.add("answered");
          } else {
            pageBtn.classList.remove("answered");
          }
        }
      });
    }

    bindEvents() {
      // Option click visual update
      document.querySelectorAll(".option-label").forEach((label) => {
        label.addEventListener("click", () => {
          const block = label.closest(".question-block");
          if (block) {
            block
              .querySelectorAll(".option-label")
              .forEach((el) => el.classList.remove("selected"));
            label.classList.add("selected");
          }
        });
      });

      // Radio input change
      document
        .querySelectorAll('.options-list input[type="radio"]')
        .forEach((radio) => {
          radio.addEventListener("change", () => this.checkSubmitReadiness());
        });

      // Pagination buttons
      this.pageButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
          const target = parseInt(btn.getAttribute("data-slide"), 10);
          if (!isNaN(target) && target >= 0 && target < this.totalSlides) {
            this.currentSlide = target;
            this.updateSlider();
          }
        });
      });

      if (this.btnNext) {
        this.btnNext.addEventListener("click", () => {
          if (this.currentSlide < this.totalSlides - 1) {
            this.currentSlide++;
            this.updateSlider();
          }
        });
      }

      if (this.btnPrev) {
        this.btnPrev.addEventListener("click", () => {
          if (this.currentSlide > 0) {
            this.currentSlide--;
            this.updateSlider();
          }
        });
      }

      if (this.quizForm) {
        this.quizForm.addEventListener("submit", () => {
          this.isSubmitting = true;
          localStorage.removeItem(this.storageKey);
        });
      }

      window.addEventListener("pagehide", () => {
        if (this.isSubmitting) {
          localStorage.removeItem(this.storageKey);
        }
      });
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    const config = window.NetQuizData || {};
    window.quizPlayer = new NetQuizPlayer(config);
  });
})();
