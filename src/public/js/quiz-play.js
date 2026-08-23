/**
 * NetQuiz Quiz Player Module - Vercel Dark & Pixel Engine
 * Handles: Option Selection, Palette Navigation, Timer Countdown, Pause/Resume, Modal Overlays, and Audio Synth FX.
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
      this.timerTexts = document.querySelectorAll(".timer-display-text");
      this.timerPills = document.querySelectorAll(
        ".quiz-timer-pill, #quiz-timer-desktop"
      );
      this.quizForm = document.getElementById("quiz-form");
      this.timeLeftInput = document.getElementById("time_left");
      this.blocks = Array.from(document.querySelectorAll(".question-block"));
      this.totalSlides = this.blocks.length;

      this.paletteButtons = Array.from(
        document.querySelectorAll(".palette-btn")
      );
      this.answeredCounter = document.getElementById("answered-counter-text");

      // Pause Modal elements
      this.pauseTriggers = document.querySelectorAll(".btn-pause-trigger");
      this.pauseDialog = document.getElementById("pause-dialog");
      this.btnCancelPause = document.getElementById("btn-cancel-pause");
      this.btnConfirmPause = document.getElementById("btn-confirm-pause");

      // Submit Confirmation Modal elements
      this.submitModalTriggers = document.querySelectorAll(
        ".btn-open-submit-modal"
      );
      this.submitConfirmModal = document.getElementById("submit-confirm-modal");
      this.btnCancelSubmitModal = document.getElementById(
        "btn-cancel-submit-modal"
      );
      this.btnFinalSubmit = document.getElementById("btn-final-submit");
      this.modalAnsweredCount = document.getElementById(
        "modal-answered-count"
      );
      this.modalUnansweredCount = document.getElementById(
        "modal-unanswered-count"
      );

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    initState() {
      // 1. Timer Countdown Initialization
      if (this.durationSeconds > 0) {
        let targetTimestamp = localStorage.getItem(this.storageKey);

        if (this.isResumed || !targetTimestamp) {
          this.timeLeft = this.initialTimeLeft;
          targetTimestamp = Date.now() + this.timeLeft * 1000;
          localStorage.setItem(this.storageKey, targetTimestamp);
        } else {
          this.timeLeft = Math.max(
            0,
            Math.floor((parseInt(targetTimestamp, 10) - Date.now()) / 1000)
          );
          if (this.timeLeft <= 0 || this.timeLeft > this.initialTimeLeft) {
            this.timeLeft = this.initialTimeLeft;
            targetTimestamp = Date.now() + this.timeLeft * 1000;
            localStorage.setItem(this.storageKey, targetTimestamp);
          }
        }

        this.updateTimerDisplay();
        this.startTimer();
      }

      this.updateSlider();
      this.updateAnsweredCounter();
    }

    startTimer() {
      if (this.timerInterval) clearInterval(this.timerInterval);

      this.timerInterval = setInterval(() => {
        if (this.timeLeft <= 0) {
          clearInterval(this.timerInterval);
          this.onTimerExpired();
          return;
        }

        this.timeLeft--;
        if (this.timeLeftInput) {
          this.timeLeftInput.value = this.timeLeft;
        }
        this.updateTimerDisplay();
      }, 1000);
    }

    updateTimerDisplay() {
      const minutes = Math.floor(this.timeLeft / 60);
      const seconds = this.timeLeft % 60;
      const formatted = `${String(minutes).padStart(2, "0")}:${String(
        seconds
      ).padStart(2, "0")}`;

      this.timerTexts.forEach((el) => {
        el.textContent = formatted;
      });

      // Warning pulse when less than 60 seconds remain
      if (this.timeLeft <= 60) {
        this.timerPills.forEach((el) => {
          el.classList.add("warning");
        });
      } else {
        this.timerPills.forEach((el) => {
          el.classList.remove("warning");
        });
      }
    }

    onTimerExpired() {
      if (this.isSubmitting) return;

      localStorage.removeItem(this.storageKey);
      if (window.playPixelSound) {
        window.playPixelSound("badge");
      }

      // Show auto-submit notification
      const overlay = document.createElement("div");
      overlay.className = "dialog-overlay";
      overlay.innerHTML = `
        <div class="modal-dark-card text-center">
          <h3 class="modal-title-text text-red-400">⏱ Waktu Ujian Telah Habis!</h3>
          <p class="modal-desc-text">Jawaban Anda sedang dikumpulkan secara otomatis ke server...</p>
        </div>
      `;
      document.body.appendChild(overlay);

      if (this.quizForm) {
        this.isSubmitting = true;
        setTimeout(() => {
          this.quizForm.submit();
        }, 1200);
      }
    }

    updateSlider() {
      this.blocks.forEach((block, index) => {
        if (index === this.currentSlide) {
          block.style.display = "block";
        } else {
          block.style.display = "none";
        }
      });

      // Update palette active states
      this.paletteButtons.forEach((btn, index) => {
        if (index === this.currentSlide) {
          btn.classList.add("current");
        } else {
          btn.classList.remove("current");
        }
      });
    }

    updateAnsweredCounter() {
      let answeredCount = 0;
      this.blocks.forEach((block, index) => {
        const checked = block.querySelector('input[type="radio"]:checked');
        const pBtn = document.querySelector(
          `.palette-btn[data-index="${index}"]`
        );
        if (checked) {
          answeredCount++;
          if (pBtn) pBtn.classList.add("answered");
        } else {
          if (pBtn) pBtn.classList.remove("answered");
        }
      });

      if (this.answeredCounter) {
        this.answeredCounter.textContent = `${answeredCount} / ${this.totalSlides} Terjawab`;
      }
      if (this.modalAnsweredCount) {
        this.modalAnsweredCount.textContent = `${answeredCount} Soal`;
      }
      if (this.modalUnansweredCount) {
        const unanswered = this.totalSlides - answeredCount;
        this.modalUnansweredCount.textContent = `${unanswered} Soal`;
      }
    }

    bindEvents() {
      // 1. Palette numbers click navigation
      this.paletteButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
          const target = parseInt(btn.getAttribute("data-index"), 10);
          if (!isNaN(target) && target >= 0 && target < this.totalSlides) {
            this.currentSlide = target;
            this.updateSlider();
            window.scrollTo({ top: 0, behavior: "smooth" });
          }
        });
      });

      // 2. Pause Modal Dialog
      this.pauseTriggers.forEach((trigger) => {
        trigger.addEventListener("click", () => {
          if (this.pauseDialog) this.pauseDialog.style.display = "flex";
        });
      });

      if (this.btnCancelPause && this.pauseDialog) {
        this.btnCancelPause.addEventListener("click", () => {
          this.pauseDialog.style.display = "none";
        });
      }

      if (this.pauseDialog) {
        this.pauseDialog.addEventListener("click", (e) => {
          if (e.target === this.pauseDialog) {
            this.pauseDialog.style.display = "none";
          }
        });
      }

      if (this.btnConfirmPause && this.quizForm) {
        this.btnConfirmPause.addEventListener("click", () => {
          this.isSubmitting = true;
          localStorage.removeItem(this.storageKey);

          // Submit via pause action URL
          const action = `${window.BASE_URL || ""}/quiz/pause/${this.quizId}`;
          this.quizForm.setAttribute("action", action);
          this.quizForm.submit();
        });
      }

      // 3. Submit Confirmation Modal Dialog
      this.submitModalTriggers.forEach((trigger) => {
        trigger.addEventListener("click", () => {
          this.updateAnsweredCounter();
          if (this.submitConfirmModal) {
            this.submitConfirmModal.style.display = "flex";
          }
        });
      });

      if (this.btnCancelSubmitModal && this.submitConfirmModal) {
        this.btnCancelSubmitModal.addEventListener("click", () => {
          this.submitConfirmModal.style.display = "none";
        });
      }

      if (this.submitConfirmModal) {
        this.submitConfirmModal.addEventListener("click", (e) => {
          if (e.target === this.submitConfirmModal) {
            this.submitConfirmModal.style.display = "none";
          }
        });
      }

      if (this.btnFinalSubmit && this.quizForm) {
        this.btnFinalSubmit.addEventListener("click", () => {
          this.isSubmitting = true;
          localStorage.removeItem(this.storageKey);
          this.quizForm.submit();
        });
      }

      // 4. Keyboard arrow navigation shortcuts
      document.addEventListener("keydown", (e) => {
        if (e.key === "ArrowRight" && !e.target.matches("input, textarea")) {
          if (this.currentSlide < this.totalSlides - 1) {
            this.currentSlide++;
            this.updateSlider();
            if (window.playPixelSound) window.playPixelSound("click");
          }
        } else if (e.key === "ArrowLeft" && !e.target.matches("input, textarea")) {
          if (this.currentSlide > 0) {
            this.currentSlide--;
            this.updateSlider();
            if (window.playPixelSound) window.playPixelSound("click");
          }
        } else if (e.key === "Escape") {
          if (this.pauseDialog) this.pauseDialog.style.display = "none";
          if (this.submitConfirmModal)
            this.submitConfirmModal.style.display = "none";
        }
      });
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    if (window.QUIZ_PLAYER_CONFIG) {
      window.quizPlayer = new NetQuizPlayer(window.QUIZ_PLAYER_CONFIG);
    }
  });
})();
