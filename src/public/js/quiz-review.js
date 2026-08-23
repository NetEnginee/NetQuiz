/**
 * NetQuiz Quiz Review Module - Vercel Dark & Pixel Engine
 * Handles: Question Carousel, Filter Tabs (All / Correct / Wrong), Color-Coded Palette Navigation.
 */
(function () {
  "use strict";

  class NetQuizReviewer {
    constructor() {
      this.currentSlide = 0;
      this.activeFilter = "all"; // 'all' | 'correct' | 'wrong'

      this.initDom();
      this.bindEvents();
      this.updateView();
    }

    initDom() {
      this.blocks = Array.from(
        document.querySelectorAll(".review-question-block")
      );
      this.totalSlides = this.blocks.length;

      this.btnPrev = document.getElementById("btn-prev-review");
      this.btnNext = document.getElementById("btn-next-review");
      this.paletteButtons = Array.from(
        document.querySelectorAll(".review-palette-btn")
      );
      this.filterButtons = Array.from(
        document.querySelectorAll(".review-filter-btn")
      );

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    getFilteredIndices() {
      const indices = [];
      this.blocks.forEach((block, index) => {
        const isCorrect = block.getAttribute("data-correct") === "1";
        if (this.activeFilter === "all") {
          indices.push(index);
        } else if (this.activeFilter === "correct" && isCorrect) {
          indices.push(index);
        } else if (this.activeFilter === "wrong" && !isCorrect) {
          indices.push(index);
        }
      });
      return indices;
    }

    updateView() {
      const filtered = this.getFilteredIndices();

      // If current slide is not in filtered list, switch to first available matching slide
      if (filtered.length > 0 && !filtered.includes(this.currentSlide)) {
        this.currentSlide = filtered[0];
      }

      // 1. Update question blocks visibility
      this.blocks.forEach((block, index) => {
        if (index === this.currentSlide) {
          block.style.display = "block";
        } else {
          block.style.display = "none";
        }
      });

      // 2. Update palette buttons styling and active marker
      this.paletteButtons.forEach((btn, index) => {
        const isMatching = filtered.includes(index);

        if (this.activeFilter === "all") {
          btn.style.opacity = "1";
          btn.style.pointerEvents = "auto";
        } else if (isMatching) {
          btn.style.opacity = "1";
          btn.style.pointerEvents = "auto";
        } else {
          btn.style.opacity = "0.25";
          btn.style.pointerEvents = "auto";
        }

        if (index === this.currentSlide) {
          btn.classList.add("current");
          btn.style.outline = "2px solid #ffffff";
          btn.style.outlineOffset = "2px";
        } else {
          btn.classList.remove("current");
          btn.style.outline = "none";
        }
      });

      // 3. Update Prev / Next Buttons
      const currentIndexInFiltered = filtered.indexOf(this.currentSlide);

      if (this.btnPrev) {
        const hasPrev = currentIndexInFiltered > 0;
        this.btnPrev.disabled = !hasPrev;
        this.btnPrev.style.opacity = hasPrev ? "1" : "0.4";
        this.btnPrev.style.cursor = hasPrev ? "pointer" : "not-allowed";
      }

      if (this.btnNext) {
        const hasNext =
          currentIndexInFiltered >= 0 &&
          currentIndexInFiltered < filtered.length - 1;
        this.btnNext.disabled = !hasNext;
        this.btnNext.style.opacity = hasNext ? "1" : "0.4";
        this.btnNext.style.cursor = hasNext ? "pointer" : "not-allowed";
      }

      // 4. Update Filter tab active classes
      this.filterButtons.forEach((btn) => {
        const filterType = btn.getAttribute("data-filter");
        if (filterType === this.activeFilter) {
          btn.classList.add("active");
        } else {
          btn.classList.remove("active");
        }
      });
    }

    bindEvents() {
      // 1. Filter tabs click
      this.filterButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
          const filter = btn.getAttribute("data-filter");
          if (filter && filter !== this.activeFilter) {
            this.activeFilter = filter;
            this.updateView();
            if (window.playPixelSound) window.playPixelSound("click");
          }
        });
      });

      // 2. Palette buttons click
      this.paletteButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
          const target = parseInt(btn.getAttribute("data-index"), 10);
          if (!isNaN(target) && target >= 0 && target < this.totalSlides) {
            const filtered = this.getFilteredIndices();
            if (!filtered.includes(target)) {
              this.activeFilter = "all";
            }
            this.currentSlide = target;
            this.updateView();
            window.scrollTo({ top: 0, behavior: "smooth" });
            if (window.playPixelSound) window.playPixelSound("click");
          }
        });
      });

      // 3. Next / Prev navigation within active filter
      if (this.btnNext) {
        this.btnNext.addEventListener("click", () => {
          const filtered = this.getFilteredIndices();
          const currentIndexInFiltered = filtered.indexOf(this.currentSlide);
          if (
            currentIndexInFiltered >= 0 &&
            currentIndexInFiltered < filtered.length - 1
          ) {
            this.currentSlide = filtered[currentIndexInFiltered + 1];
            this.updateView();
            window.scrollTo({ top: 0, behavior: "smooth" });
            if (window.playPixelSound) window.playPixelSound("click");
          }
        });
      }

      if (this.btnPrev) {
        this.btnPrev.addEventListener("click", () => {
          const filtered = this.getFilteredIndices();
          const currentIndexInFiltered = filtered.indexOf(this.currentSlide);
          if (currentIndexInFiltered > 0) {
            this.currentSlide = filtered[currentIndexInFiltered - 1];
            this.updateView();
            window.scrollTo({ top: 0, behavior: "smooth" });
            if (window.playPixelSound) window.playPixelSound("click");
          }
        });
      }

      // Keyboard arrow shortcuts
      document.addEventListener("keydown", (e) => {
        if (e.key === "ArrowRight" && !e.target.matches("input, textarea")) {
          const filtered = this.getFilteredIndices();
          const currentIndexInFiltered = filtered.indexOf(this.currentSlide);
          if (
            currentIndexInFiltered >= 0 &&
            currentIndexInFiltered < filtered.length - 1
          ) {
            this.currentSlide = filtered[currentIndexInFiltered + 1];
            this.updateView();
            if (window.playPixelSound) window.playPixelSound("click");
          }
        } else if (e.key === "ArrowLeft" && !e.target.matches("input, textarea")) {
          const filtered = this.getFilteredIndices();
          const currentIndexInFiltered = filtered.indexOf(this.currentSlide);
          if (currentIndexInFiltered > 0) {
            this.currentSlide = filtered[currentIndexInFiltered - 1];
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
