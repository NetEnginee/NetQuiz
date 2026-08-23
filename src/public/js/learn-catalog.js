/**
 * NetQuiz Learning Materials Catalog & Reader Engine
 * Handles: Category Tab filtering, Dynamic Terminal Wrappers, Copy CLI commands, and Retro Pixel Audio FX.
 */
(function () {
  "use strict";

  class NetQuizLearnEngine {
    constructor() {
      this.activeCategory = "all";

      this.initDom();
      this.enhanceTerminalBlocks();
      this.bindEvents();
    }

    initDom() {
      this.filterTabs = Array.from(
        document.querySelectorAll(".learn-segment-tab")
      );
      this.cards = Array.from(document.querySelectorAll(".learn-material-card"));
      this.sections = Array.from(
        document.querySelectorAll(".learn-category-section")
      );
      this.copyButtons = Array.from(document.querySelectorAll(".btn-copy-code"));

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    /**
     * Wrap raw pre/code blocks in article body with retro terminal header & copy buttons
     */
    enhanceTerminalBlocks() {
      const contentBody = document.querySelector(".material-content-body");
      if (!contentBody) return;

      const preBlocks = Array.from(contentBody.querySelectorAll("pre"));
      preBlocks.forEach((pre, idx) => {
        // Skip if already wrapped
        if (pre.closest(".terminal-block-wrap")) return;

        const codeEl = pre.querySelector("code") || pre;
        const codeText = codeEl.textContent;

        const wrapper = document.createElement("div");
        wrapper.className = "terminal-block-wrap";
        wrapper.innerHTML = `
          <div class="terminal-block-header">
            <div class="terminal-dots-group">
              <span class="terminal-dot dot-red"></span>
              <span class="terminal-dot dot-yellow"></span>
              <span class="terminal-dot dot-green"></span>
            </div>
            <span class="terminal-title-label font-mono">RouterOS Terminal</span>
            <button type="button" class="btn-copy-code font-mono" data-code-id="term-code-${idx}">
              <span>Copy</span>
            </button>
          </div>
          <div class="terminal-block-body font-mono" id="term-code-${idx}">
            <pre><code>${this.escapeHtml(codeText)}</code></pre>
          </div>
        `;

        pre.parentNode.replaceChild(wrapper, pre);
      });

      // Re-query copy buttons after enhancement
      this.copyButtons = Array.from(document.querySelectorAll(".btn-copy-code"));
    }

    escapeHtml(text) {
      const div = document.createElement("div");
      div.textContent = text;
      return div.innerHTML;
    }

    bindEvents() {
      // 1. Category filter tabs
      this.filterTabs.forEach((tab) => {
        tab.addEventListener("click", () => {
          const category = tab.getAttribute("data-category");
          if (category) {
            this.activeCategory = category;
            this.filterTabs.forEach((t) => t.classList.remove("active"));
            tab.classList.add("active");

            this.applyFilters();
            if (window.playPixelSound) window.playPixelSound("click");
          }
        });
      });

      // 2. Code Copy Buttons (in Reader & Catalog)
      document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-copy-code");
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const targetId = btn.getAttribute("data-code-id") || btn.getAttribute("data-target");
        let codeText = "";

        if (targetId) {
          const targetEl = document.getElementById(targetId);
          if (targetEl) codeText = targetEl.textContent;
        } else {
          const preOrBody =
            btn.closest(".terminal-block-wrap")?.querySelector(".terminal-block-body") ||
            btn.parentElement?.nextElementSibling;
          if (preOrBody) codeText = preOrBody.textContent;
        }

        if (codeText) {
          navigator.clipboard
            .writeText(codeText.trim())
            .then(() => {
              const originalHtml = btn.innerHTML;
              btn.innerHTML = `<span>✓ Disalin!</span>`;
              btn.style.color = "#50e3c2";
              btn.style.borderColor = "#50e3c2";

              if (window.playPixelSound) window.playPixelSound("coin");

              setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.style.color = "";
                btn.style.borderColor = "";
                if (window.lucide) window.lucide.createIcons();
              }, 2000);
            })
            .catch((err) => {
              console.error("Failed to copy code: ", err);
            });
        }
      });
    }

    applyFilters() {
      // Filter each card & section
      this.sections.forEach((section) => {
        const secCat = (section.getAttribute("data-category") || "").toLowerCase();

        const matchesCat =
          this.activeCategory === "all" ||
          secCat === this.activeCategory.toLowerCase();

        if (matchesCat) {
          section.classList.remove("is-hidden");
          section.style.display = "block";
        } else {
          section.classList.add("is-hidden");
          section.style.display = "none";
        }
      });
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    window.netQuizLearn = new NetQuizLearnEngine();
  });
})();
