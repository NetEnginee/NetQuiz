/**
 * NetQuiz Quiz Review Module - Vercel Dark & Pixel Engine
 * Handles: Question Carousel, Color-Coded Palette Navigation, Dynamic Sidebar Explanation.
 */
(function () {
  "use strict";

  class NetQuizReviewer {
    constructor() {
      this.currentSlide = 0;

      this.initDom();
      this.bindEvents();
      this.updateView();
    }

    initDom() {
      this.blocks = Array.from(
        document.querySelectorAll(".review-question-block")
      );
      this.totalSlides = this.blocks.length;

      this.paletteButtons = Array.from(
        document.querySelectorAll(".review-palette-btn")
      );
      this.explanationItems = Array.from(
        document.querySelectorAll(".review-explanation-item")
      );
      this.explanationQBadge = document.getElementById("explanation-q-badge");

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    updateView() {
      // 1. Update question blocks visibility
      this.blocks.forEach((block, index) => {
        if (index === this.currentSlide) {
          block.style.display = "block";
        } else {
          block.style.display = "none";
        }
      });

      // 2. Update dynamic sidebar explanation item & badge
      if (this.explanationItems && this.explanationItems.length > 0) {
        this.explanationItems.forEach((item, index) => {
          if (index === this.currentSlide) {
            item.style.display = "block";
          } else {
            item.style.display = "none";
          }
        });
      }
      if (this.explanationQBadge) {
        this.explanationQBadge.textContent = `Soal #${this.currentSlide + 1}`;
      }

      // 3. Update palette buttons styling and active marker
      this.paletteButtons.forEach((btn, index) => {
        if (index === this.currentSlide) {
          btn.classList.add("current");
          btn.style.outline = "2px solid #ffffff";
          btn.style.outlineOffset = "2px";
        } else {
          btn.classList.remove("current");
          btn.style.outline = "none";
        }
      });
    }

    bindEvents() {
      // 1. Palette buttons click
      this.paletteButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
          const target = parseInt(btn.getAttribute("data-index"), 10);
          if (!isNaN(target) && target >= 0 && target < this.totalSlides) {
            this.currentSlide = target;
            this.updateView();
            if (window.playPixelSound) window.playPixelSound("click");
          }
        });
      });

      // 2. Keyboard arrow shortcuts
      document.addEventListener("keydown", (e) => {
        if (e.key === "ArrowRight" && !e.target.matches("input, textarea")) {
          if (this.currentSlide < this.totalSlides - 1) {
            this.currentSlide++;
            this.updateView();
            if (window.playPixelSound) window.playPixelSound("click");
          }
        } else if (e.key === "ArrowLeft" && !e.target.matches("input, textarea")) {
          if (this.currentSlide > 0) {
            this.currentSlide--;
            this.updateView();
            if (window.playPixelSound) window.playPixelSound("click");
          }
        }
      });
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    window.quizReviewer = new NetQuizReviewer();
  });
})();
