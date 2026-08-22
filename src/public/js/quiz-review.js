/**
 * NetQuiz Quiz Review Module (Geist State Architecture)
 */
(function () {
  "use strict";

  class NetQuizReviewer {
    constructor() {
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

      if (window.lucide) {
        window.lucide.createIcons();
      }

      this.updateSlider();
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
      if (this.btnNext)
        this.btnNext.disabled = this.currentSlide === this.totalSlides - 1;

      this.pageButtons.forEach((btn, index) => {
        if (index === this.currentSlide) {
          btn.style.outline = "2px solid #18181B";
          btn.style.outlineOffset = "2px";
        } else {
          btn.style.outline = "none";
        }
      });
    }

    bindEvents() {
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
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    window.quizReviewer = new NetQuizReviewer();
  });
})();
