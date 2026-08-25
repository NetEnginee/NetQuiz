/**
 * NetQuiz Quiz Catalog Realtime Filter Engine
 * Handles: Realtime difficulty filtering, zero page reloads, dynamic counters, and empty state toggling.
 */
(function () {
  "use strict";

  class NetQuizCatalogFilter {
    constructor() {
      this.initDom();
      if (!this.filterTabs || this.filterTabs.length === 0) return;

      this.bindEvents();
      this.initFromUrl();
    }

    initDom() {
      this.filterTabs = Array.from(
        document.querySelectorAll(".quiz-segment-tab[data-difficulty]")
      );
      this.cards = Array.from(document.querySelectorAll(".quiz-card-box"));
      this.sections = Array.from(
        document.querySelectorAll(".quiz-category-section")
      );
      this.totalCountEl = document.getElementById("total-quizzes-count");
      this.emptyStateEl = document.getElementById("quiz-filter-empty-state");
      this.sectionsContainer = document.getElementById("quiz-sections-container");
      this.resetBtn = document.getElementById("btn-reset-quiz-filter");
    }

    initFromUrl() {
      const urlParams = new URLSearchParams(window.location.search);
      const diffParam = urlParams.get("difficulty") || "all";
      this.applyFilter(diffParam, false);
    }

    bindEvents() {
      // 1. Tab clicks
      this.filterTabs.forEach((tab) => {
        tab.addEventListener("click", (e) => {
          e.preventDefault();
          const difficulty = tab.getAttribute("data-difficulty") || "all";
          this.applyFilter(difficulty, true);
          if (window.playPixelSound) window.playPixelSound("click");
        });
      });

      // 2. Reset empty state button
      if (this.resetBtn) {
        this.resetBtn.addEventListener("click", (e) => {
          e.preventDefault();
          this.applyFilter("all", true);
          if (window.playPixelSound) window.playPixelSound("click");
        });
      }
    }

    applyFilter(difficulty, updateUrl = true) {
      const targetDiff = (difficulty || "all").trim();
      const targetLower = targetDiff.toLowerCase();

      // 1. Update Active Tab State
      this.filterTabs.forEach((tab) => {
        const tabDiff = (
          tab.getAttribute("data-difficulty") || ""
        ).toLowerCase();
        if (tabDiff === targetLower) {
          tab.classList.add("active");
        } else {
          tab.classList.remove("active");
        }
      });

      // 2. Update Browser URL (Zero Reload)
      if (updateUrl && window.history && window.history.replaceState) {
        const currentUrl = new URL(window.location.href);
        if (targetLower === "all") {
          currentUrl.searchParams.delete("difficulty");
        } else {
          currentUrl.searchParams.set("difficulty", targetDiff);
        }
        window.history.replaceState(null, "", currentUrl.toString());
      }

      // 3. Filter Cards & Sections
      let totalVisible = 0;

      this.sections.forEach((sec) => {
        const secCards = Array.from(sec.querySelectorAll(".quiz-card-box"));
        let secVisible = 0;

        secCards.forEach((card) => {
          const cardDiff = (
            card.getAttribute("data-difficulty") || ""
          ).toLowerCase();
          const matches = targetLower === "all" || cardDiff === targetLower;

          if (matches) {
            card.style.display = "";
            secVisible++;
            totalVisible++;
          } else {
            card.style.display = "none";
          }
        });

        // Update Category Section Visibility & Badge
        const catBadge = sec.querySelector(".category-count-badge");
        if (catBadge) {
          catBadge.textContent = `${secVisible} Paket Kuis`;
        }

        if (secVisible > 0) {
          sec.style.display = "";
        } else {
          sec.style.display = "none";
        }
      });

      // 4. Update Total Counter
      if (this.totalCountEl) {
        this.totalCountEl.textContent = totalVisible;
      }

      // 5. Handle Global Empty State
      if (totalVisible === 0) {
        if (this.emptyStateEl) this.emptyStateEl.style.display = "block";
        if (this.sectionsContainer) this.sectionsContainer.style.display = "none";
      } else {
        if (this.emptyStateEl) this.emptyStateEl.style.display = "none";
        if (this.sectionsContainer) this.sectionsContainer.style.display = "";
      }
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    window.netQuizCatalogFilter = new NetQuizCatalogFilter();
  });
})();
