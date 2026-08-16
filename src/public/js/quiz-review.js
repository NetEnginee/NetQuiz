/**
 * NetQuiz Quiz Review Module (Clean State Architecture)
 */
(function () {
  "use strict";

  class NetQuizReviewer {
    constructor(config) {
      this.explanations = config.explanations || [];
      this.currentSlide = 0;

      this.initDom();
      this.bindEvents();
    }

    initDom() {
      this.blocks = Array.from(document.querySelectorAll(".question-block"));
      this.totalSlides = this.blocks.length;
      this.btnPrev = document.getElementById("btn-prev");
      this.btnNext = document.getElementById("btn-next");
      this.pageButtons = Array.from(document.querySelectorAll(".page-number"));
      this.modal = document.getElementById("explanation-modal");
      this.textEl = document.getElementById("explanation-text");

      if (window.lucide) {
        window.lucide.createIcons();
      }

      this.updateSlider();
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

    showExplanation(index) {
      const text =
        this.explanations[index] || "Tidak ada penjelasan untuk soal ini.";
      if (!this.modal || !this.textEl) return;

      this.textEl.textContent = text;
      this.modal.style.display = "flex";

      void this.modal.offsetWidth;
      this.modal.style.opacity = "1";
      if (this.modal.firstElementChild) {
        this.modal.firstElementChild.style.transform = "scale(1)";
      }

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    closeExplanation() {
      if (!this.modal) return;
      this.modal.style.opacity = "0";
      if (this.modal.firstElementChild) {
        this.modal.firstElementChild.style.transform = "scale(0.95)";
      }
      setTimeout(() => {
        this.modal.style.display = "none";
      }, 200);
    }

    bindEvents() {
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

      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") this.closeExplanation();
      });

      if (this.modal) {
        this.modal.addEventListener("click", (e) => {
          if (e.target === this.modal) this.closeExplanation();
        });
      }
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    const config = window.NetQuizData || {};
    const reviewer = new NetQuizReviewer(config);
    window.showExplanation = (idx) => reviewer.showExplanation(idx);
    window.closeExplanation = () => reviewer.closeExplanation();
  });
})();
