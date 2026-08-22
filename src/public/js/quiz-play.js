/**
 * NetQuiz Quiz Player Module (Geist Architecture)
 * Handles: Step Carousel, Question Grid Palette, Realtime Timer, Pause/Resume, and Modals.
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
        ".quiz-timer-pill, #quiz-timer-desktop",
      );
      this.quizForm = document.getElementById("quiz-form");
      this.timeLeftInput = document.getElementById("time_left");
      this.blocks = Array.from(document.querySelectorAll(".question-block"));
      this.totalSlides = this.blocks.length;

      this.btnPrev = document.getElementById("btn-prev");
      this.btnNext = document.getElementById("btn-next");
      this.btnSubmitCarousel = document.getElementById("btn-submit-carousel");
      this.paletteButtons = Array.from(
        document.querySelectorAll(".palette-btn"),
      );
      this.answeredCounter = document.getElementById("answered-counter-text");

      // Pause Modal elements
      this.pauseTriggers = document.querySelectorAll(".btn-pause-trigger");
      this.pauseDialog = document.getElementById("pause-dialog");
      this.btnCancelPause = document.getElementById("btn-cancel-pause");
      this.btnConfirmPause = document.getElementById("btn-confirm-pause");

      // Submit Confirmation Modal elements
      this.submitModalTriggers = document.querySelectorAll(
        ".btn-open-submit-modal",
      );
      this.submitConfirmModal = document.getElementById("submit-confirm-modal");
      this.btnCancelSubmitModal = document.getElementById(
        "btn-cancel-submit-modal",
      );
      this.btnFinalSubmit = document.getElementById("btn-final-submit");
      this.modalAnsweredCount = document.getElementById(
        "modal-answered-count",
      );
      this.modalUnansweredCount = document.getElementById(
        "modal-unanswered-count",
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
            Math.floor((parseInt(targetTimestamp, 10) - Date.now()) / 1000),
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
      const minutes = Math.floor(this.timeLeft / 60);
      const seconds = this.timeLeft % 60;
      const formatted = `${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;

      this.timerTexts.forEach((el) => {
        el.textContent = formatted;
      });

      if (this.timeLeftInput) {
        this.timeLeftInput.value = this.timeLeft;
      }

      this.timerPills.forEach((pill) => {
        if (this.timeLeft <= 60 && this.timeLeft > 0) {
          pill.style.backgroundColor = "#EF4444";
          pill.style.color = "#FFFFFF";
        } else {
          pill.style.backgroundColor = "#18181B";
          pill.style.color = "#FFFFFF";
        }
      });
    }

    handleTimeExpired() {
      localStorage.removeItem(this.storageKey);

      const overlay = document.createElement("div");
      overlay.style.position = "fixed";
      overlay.style.inset = "0";
      overlay.style.backgroundColor = "rgba(0, 0, 0, 0.85)";
      overlay.style.backdropFilter = "blur(8px)";
      overlay.style.color = "#ffffff";
      overlay.style.display = "flex";
      overlay.style.flexDirection = "column";
      overlay.style.justifyContent = "center";
      overlay.style.alignItems = "center";
      overlay.style.zIndex = "99999";
      overlay.style.fontFamily = "var(--font-heading, sans-serif)";

      overlay.innerHTML = `
        <div style="background: #18181B; border: 1px solid #333; border-radius: 12px; padding: 2.5rem 2rem; max-width: 440px; width: 90%; text-align: center;">
            <div style="background: #EF4444; border-radius: 50%; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                <i data-lucide="clock" style="width: 28px; height: 28px; color: #fff;"></i>
            </div>
            <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 0.5rem; color: #FFFFFF;">Waktu Ujian Telah Habis</h2>
            <p style="font-size: 0.875rem; color: #A1A1AA; font-weight: 500; margin: 0;">Sistem sedang mengumpulkan seluruh jawaban Anda...</p>
        </div>
      `;
      document.body.appendChild(overlay);
      if (window.lucide) window.lucide.createIcons();

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

      if (this.btnPrev) {
        this.btnPrev.disabled = this.currentSlide === 0;
        this.btnPrev.style.opacity = this.currentSlide === 0 ? "0.45" : "1";
        this.btnPrev.style.cursor =
          this.currentSlide === 0 ? "not-allowed" : "pointer";
      }

      // Handle Next / Submit button visibility in carousel footer
      const isLastSlide = this.currentSlide === this.totalSlides - 1;
      if (this.btnNext && this.btnSubmitCarousel) {
        if (isLastSlide) {
          this.btnNext.style.display = "none";
          this.btnSubmitCarousel.style.display = "inline-flex";
        } else {
          this.btnNext.style.display = "inline-flex";
          this.btnSubmitCarousel.style.display = "none";
        }
      }

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
          `.palette-btn[data-index="${index}"]`,
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

      // 2. Next / Prev navigation
      if (this.btnNext) {
        this.btnNext.addEventListener("click", () => {
          if (this.currentSlide < this.totalSlides - 1) {
            this.currentSlide++;
            this.updateSlider();
            window.scrollTo({ top: 0, behavior: "smooth" });
          }
        });
      }

      if (this.btnPrev) {
        this.btnPrev.addEventListener("click", () => {
          if (this.currentSlide > 0) {
            this.currentSlide--;
            this.updateSlider();
            window.scrollTo({ top: 0, behavior: "smooth" });
          }
        });
      }

      // 3. Pause Modal Dialog
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

          // Submit form to pause route
          this.quizForm.action = `${window.BASE_URL}/quiz/pause/${this.quizId}`;
          this.quizForm.submit();
        });
      }

      // 4. Submit Confirmation Modal
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

      // Escape key modal close
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
          if (this.pauseDialog && this.pauseDialog.style.display === "flex") {
            this.pauseDialog.style.display = "none";
          }
          if (
            this.submitConfirmModal &&
            this.submitConfirmModal.style.display === "flex"
          ) {
            this.submitConfirmModal.style.display = "none";
          }
        }
      });

      if (this.btnFinalSubmit && this.quizForm) {
        this.btnFinalSubmit.addEventListener("click", () => {
          this.isSubmitting = true;
          localStorage.removeItem(this.storageKey);
          this.quizForm.action = `${window.BASE_URL}/quiz/submit/${this.quizId}`;
          this.quizForm.submit();
        });
      }

      // 5. Native Form Submit cleanup
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
    const config = window.QUIZ_PLAYER_CONFIG || {};
    window.quizPlayer = new NetQuizPlayer(config);
  });
})();
