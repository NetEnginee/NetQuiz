/**
 * NetQuiz Quiz Player Module (Geist State Architecture)
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

      this.btnPause = document.getElementById("btn-pause-quiz");
      this.pauseDialog = document.getElementById("pause-dialog");
      this.btnCancelPause = document.getElementById("btn-cancel-pause");
      this.btnConfirmPause = document.getElementById("btn-confirm-pause");

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    initState() {
      // 1. Timer Initialization
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
      this.updateAnsweredStatus();
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
          this.timerPill.style.backgroundColor = "#EF4444";
          this.timerPill.style.color = "#FFFFFF";
        } else {
          this.timerPill.style.backgroundColor = "#18181B";
          this.timerPill.style.color = "#FFFFFF";
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
        <div style="background: #18181B; border: 1px solid #333; border-radius: 12px; padding: 2rem; max-width: 420px; text-align: center;">
            <div style="background: #EF4444; border-radius: 50%; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                <i data-lucide="clock" style="width: 28px; height: 28px; color: #fff;"></i>
            </div>
            <h2 style="font-size:1.35rem; font-weight:800; margin-bottom:0.4rem; color:#FFFFFF;">Waktu Ujian Habis</h2>
            <p style="font-size:0.85rem; color:#A1A1AA; font-weight:500; margin: 0;">Jawaban Anda sedang dikumpulkan otomatis oleh sistem...</p>
        </div>
      `;
      document.body.appendChild(overlay);
      if (window.lucide) window.lucide.createIcons();

      if (this.quizForm) {
        this.isSubmitting = true;
        setTimeout(() => {
          this.quizForm.submit();
        }, 1500);
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

      if (this.btnPrev) this.btnPrev.disabled = this.currentSlide === 0;

      // Handle Next / Submit Button display
      const isLastSlide = this.currentSlide === this.totalSlides - 1;
      if (this.btnNext && this.btnSubmit) {
        if (isLastSlide) {
          this.btnNext.style.display = "none";
          this.btnSubmit.style.display = "inline-flex";
        } else {
          this.btnNext.style.display = "inline-flex";
          this.btnSubmit.style.display = "none";
        }
      }

      this.pageButtons.forEach((btn, index) => {
        const isCurrent = index === this.currentSlide;
        const isAnswered = btn.classList.contains("answered");

        if (isCurrent) {
          btn.style.outline = "2px solid #18181B";
          btn.style.outlineOffset = "2px";
        } else {
          btn.style.outline = "none";
        }

        if (isAnswered) {
          btn.style.backgroundColor = "#18181B";
          btn.style.borderColor = "#18181B";
          btn.style.color = "#FFFFFF";
        } else {
          btn.style.backgroundColor = "#FFFFFF";
          btn.style.borderColor = "#E5E7EB";
          btn.style.color = "#18181B";
        }
      });
    }

    updateAnsweredStatus() {
      this.blocks.forEach((block, index) => {
        const checked = block.querySelector('input[type="radio"]:checked');
        const pageBtn = document.querySelector(
          `.page-number[data-index="${index}"]`,
        );
        if (pageBtn) {
          if (checked) {
            pageBtn.classList.add("answered");
          } else {
            pageBtn.classList.remove("answered");
          }
        }
      });
    }

    bindEvents() {
      // Pagination numbers click
      this.pageButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
          const target = parseInt(
            btn.getAttribute("data-index") || btn.getAttribute("data-slide"),
            10,
          );
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

      // Pause Modal Handling
      if (this.btnPause && this.pauseDialog) {
        this.btnPause.addEventListener("click", () => {
          this.pauseDialog.style.display = "flex";
        });
      }

      if (this.btnCancelPause && this.pauseDialog) {
        this.btnCancelPause.addEventListener("click", () => {
          this.pauseDialog.style.display = "none";
        });
      }

      if (this.btnConfirmPause && this.quizForm) {
        this.btnConfirmPause.addEventListener("click", () => {
          this.isSubmitting = true;
          localStorage.removeItem(this.storageKey);

          // Submit to pause route
          this.quizForm.action = `${window.BASE_URL}/quiz/pause/${this.quizId}`;
          this.quizForm.submit();
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
    const config = window.QUIZ_PLAYER_CONFIG || window.NetQuizData || {};
    window.quizPlayer = new NetQuizPlayer(config);
  });
})();
