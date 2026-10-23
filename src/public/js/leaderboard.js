/**
 * NetQuiz Leaderboard Realtime Live Filter Engine
 * Handles: Zero-page-reload category filtering, in-memory caching, browser history (popstate),
 *          smooth visual transitions, Web Audio API sound FX, and dynamic DOM rendering.
 */
(function () {
  "use strict";

  function escapeHtml(str) {
    if (str === null || str === undefined) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function formatNumber(num) {
    const n = parseInt(num, 10);
    return isNaN(n) ? "0" : n.toLocaleString("en-US");
  }

  class NetQuizLeaderboard {
    constructor() {
      this.cache = {};
      this.activeCategory = "all";
      this.currentUserId = 0;
      this.abortController = null;

      this.initDom();
      if (!this.filterTabs || this.filterTabs.length === 0) return;

      this.initState();
      this.bindEvents();
    }

    initDom() {
      this.filterTabs = Array.from(
        document.querySelectorAll(
          "#leaderboard-filter-tabs .leaderboard-segment-tab[data-category]"
        )
      );
      this.breadcrumbActiveTag = document.querySelector(
        ".leaderboard-breadcrumb-wrap .active-tag"
      );
      this.podiumBadge = document.getElementById("podium-category-badge");
      this.podiumContainer = document.getElementById(
        "podium-content-container"
      );
      this.tableMeta = document.getElementById("table-panel-meta");
      this.tableContainer = document.getElementById(
        "leaderboard-table-container"
      );
    }

    initState() {
      if (window.INITIAL_LEADERBOARD) {
        this.activeCategory =
          window.INITIAL_LEADERBOARD.activeCategory || "all";
        this.currentUserId =
          parseInt(window.INITIAL_LEADERBOARD.currentUserId, 10) || 0;

        // Populate initial data into in-memory cache
        this.cache[this.activeCategory] = {
          status: "success",
          activeCategory: this.activeCategory,
          activeCategoryLabel:
            window.INITIAL_LEADERBOARD.activeCategoryLabel || "Semua Topik",
          leaderboard: window.INITIAL_LEADERBOARD.leaderboard || [],
          currentUserId: this.currentUserId
        };
      }
    }

    bindEvents() {
      // 1. Tab Click Filtering
      this.filterTabs.forEach((tab) => {
        tab.addEventListener("click", (e) => {
          e.preventDefault();
          const targetCategory = tab.getAttribute("data-category") || "all";

          if (window.playPixelSound) {
            window.playPixelSound("click");
          }

          if (targetCategory === this.activeCategory) return;

          this.switchCategory(targetCategory, true);
        });
      });

      // 2. Browser Back / Forward Button Handling (popstate)
      window.addEventListener("popstate", () => {
        const urlParams = new URLSearchParams(window.location.search);
        const catFromUrl = urlParams.get("category") || "all";
        this.switchCategory(catFromUrl, false);
      });
    }

    updateActiveTabUI(targetCategory) {
      this.filterTabs.forEach((tab) => {
        const tabCategory = tab.getAttribute("data-category") || "all";
        if (tabCategory.toLowerCase() === targetCategory.toLowerCase()) {
          tab.classList.add("active");
        } else {
          tab.classList.remove("active");
        }
      });
    }

    updateBrowserUrl(targetCategory) {
      if (!window.history || !window.history.pushState) return;

      const baseUrl = (window.BASE_URL || "").replace(/\/$/, "");
      const newUrl =
        targetCategory === "all"
          ? `${baseUrl}/leaderboard`
          : `${baseUrl}/leaderboard?category=${encodeURIComponent(targetCategory)}`;

      window.history.pushState({ category: targetCategory }, "", newUrl);
    }

    setLoading(loading) {
      if (this.podiumContainer) {
        if (loading) {
          this.podiumContainer.classList.add("leaderboard-updating");
        } else {
          this.podiumContainer.classList.remove("leaderboard-updating");
        }
      }
      if (this.tableContainer) {
        if (loading) {
          this.tableContainer.classList.add("leaderboard-updating");
        } else {
          this.tableContainer.classList.remove("leaderboard-updating");
        }
      }
    }

    async switchCategory(targetCategory, updateUrl = true) {
      this.activeCategory = targetCategory;
      this.updateActiveTabUI(targetCategory);

      if (updateUrl) {
        this.updateBrowserUrl(targetCategory);
      }

      // Check Cache for instant 0ms response
      if (this.cache[targetCategory]) {
        this.render(this.cache[targetCategory]);
        return;
      }

      // Abort previous in-flight fetch if user clicked rapidly
      if (this.abortController) {
        this.abortController.abort();
      }
      this.abortController = new AbortController();

      this.setLoading(true);

      const baseUrl = (window.BASE_URL || "").replace(/\/$/, "");
      const fetchUrl =
        targetCategory === "all"
          ? `${baseUrl}/leaderboard?ajax=1`
          : `${baseUrl}/leaderboard?category=${encodeURIComponent(targetCategory)}&ajax=1`;

      try {
        const response = await fetch(fetchUrl, {
          signal: this.abortController.signal,
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json"
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP Error ${response.status}`);
        }

        const data = await response.json();
        if (data && data.status === "success") {
          this.cache[targetCategory] = data;
          if (this.activeCategory === targetCategory) {
            this.render(data);
          }
        }
      } catch (err) {
        if (err.name !== "AbortError") {
          console.error("Gagal memuat data leaderboard live:", err);
        }
      } finally {
        this.setLoading(false);
      }
    }

    render(data) {
      const activeLabel = data.activeCategoryLabel || "Semua Topik";
      const leaderboard = Array.isArray(data.leaderboard) ? data.leaderboard : [];
      const currentUserId =
        parseInt(data.currentUserId || this.currentUserId, 10) || 0;

      // 1. Update Breadcrumb & Header Labels
      if (this.breadcrumbActiveTag) {
        this.breadcrumbActiveTag.textContent = activeLabel;
      }
      if (this.podiumBadge) {
        this.podiumBadge.textContent = activeLabel;
      }
      if (this.tableMeta) {
        this.tableMeta.textContent = `Kategori: ${activeLabel}`;
      }

      // 2. Render Podium Stage
      if (this.podiumContainer) {
        this.podiumContainer.innerHTML = this.buildPodiumHtml(leaderboard);
      }

      // 3. Render Rankings Table (#4 - #10)
      if (this.tableContainer) {
        this.tableContainer.innerHTML = this.buildTableHtml(
          leaderboard,
          currentUserId
        );
      }

      // 4. Refresh Lucide Icons if available
      if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
      }
    }

    buildPodiumHtml(leaderboard) {
      if (!leaderboard || leaderboard.length === 0) {
        return `
          <div class="podium-empty-state">
              <div class="pixel-empty-state empty-theme-amber" style="padding: 2rem 1rem; background: transparent; border: none; box-shadow: none;">
                  <div class="empty-scene">
                      <div class="sparkle-orbit">
                          <span class="sparkle-dot"></span>
                          <span class="sparkle-dot"></span>
                          <span class="sparkle-dot"></span>
                          <span class="sparkle-dot"></span>
                      </div>
                      <div class="empty-main-icon">
                          <svg class="w-10 h-10 pixelated" viewBox="0 0 16 16">
                              <use href="#pixel-sparkle"></use>
                          </svg>
                      </div>
                  </div>
                  <h3 class="empty-headline">Belum Ada Aktivitas Kejuaraan</h3>
                  <p class="empty-subtext">Belum ada aktivitas ujian kuis yang tercatat pada topik ini.</p>
              </div>
          </div>
        `;
      }

      const rank1 = leaderboard[0] || null;
      const rank2 = leaderboard[1] || null;
      const rank3 = leaderboard[2] || null;

      return `
        <div class="podium-columns-container">

            <!-- 1. Silver / Juara 2 (Left Column) -->
            <div class="podium-column podium-rank-2">
                ${
                  rank2
                    ? `
                    <div class="podium-player-card">
                        <div class="podium-avatar-wrap">
                            <div class="podium-avatar">
                                ${escapeHtml(
                                  (rank2.username || "U").charAt(0).toUpperCase()
                                )}
                            </div>
                            <span class="podium-rank-badge">2nd</span>
                        </div>
                        <div class="podium-username" title="${escapeHtml(
                          rank2.username || "Peserta"
                        )}">
                            ${escapeHtml(rank2.username || "Peserta")}
                        </div>
                        <div class="podium-score">
                            ${formatNumber(
                              rank2.total_score || 0
                            )} <span class="text-[11px] font-normal text-zinc-400">Pts</span>
                        </div>
                        <div class="podium-quiz-count">
                            ${parseInt(
                              rank2.completed_quizzes || 0,
                              10
                            )} Kuis Selesai
                        </div>
                    </div>
                `
                    : `
                    <div class="podium-player-card opacity-30">
                        <div class="podium-avatar-wrap">
                            <div class="podium-avatar border-zinc-700 text-zinc-600">-</div>
                            <span class="podium-rank-badge bg-zinc-800 text-zinc-400">2nd</span>
                        </div>
                        <div class="podium-username text-zinc-500">Kosong</div>
                    </div>
                `
                }
                <div class="podium-pedestal-block pedestal-2">
                    <span class="podium-pedestal-number">2</span>
                </div>
            </div>

            <!-- 2. Gold / Juara 1 (Center Column - Highest) -->
            <div class="podium-column podium-rank-1">
                ${
                  rank1
                    ? `
                    <div class="podium-player-card">
                        <div class="podium-avatar-wrap">
                            <div class="podium-crown-icon">
                                <svg class="w-6 h-6 pixelated" width="24" height="24" viewBox="0 0 16 16">
                                    <use href="#pixel-coin"></use>
                                </svg>
                            </div>
                            <div class="podium-avatar">
                                ${escapeHtml(
                                  (rank1.username || "U").charAt(0).toUpperCase()
                                )}
                            </div>
                            <span class="podium-rank-badge">1st</span>
                        </div>
                        <div class="podium-username" title="${escapeHtml(
                          rank1.username || "Peserta"
                        )}">
                            ${escapeHtml(rank1.username || "Peserta")}
                        </div>
                        <div class="podium-score">
                            ${formatNumber(
                              rank1.total_score || 0
                            )} <span class="text-[11px] font-normal text-amber-200/70">Pts</span>
                        </div>
                        <div class="podium-quiz-count">
                            ${parseInt(
                              rank1.completed_quizzes || 0,
                              10
                            )} Kuis Selesai
                        </div>
                    </div>
                `
                    : `
                    <div class="podium-player-card opacity-30">
                        <div class="podium-avatar-wrap">
                            <div class="podium-avatar border-zinc-700 text-zinc-600">-</div>
                            <span class="podium-rank-badge bg-zinc-800 text-zinc-400">1st</span>
                        </div>
                        <div class="podium-username text-zinc-500">Kosong</div>
                    </div>
                `
                }
                <div class="podium-pedestal-block pedestal-1">
                    <span class="podium-pedestal-number">1</span>
                </div>
            </div>

            <!-- 3. Bronze / Juara 3 (Right Column) -->
            <div class="podium-column podium-rank-3">
                ${
                  rank3
                    ? `
                    <div class="podium-player-card">
                        <div class="podium-avatar-wrap">
                            <div class="podium-avatar">
                                ${escapeHtml(
                                  (rank3.username || "U").charAt(0).toUpperCase()
                                )}
                            </div>
                            <span class="podium-rank-badge">3rd</span>
                        </div>
                        <div class="podium-username" title="${escapeHtml(
                          rank3.username || "Peserta"
                        )}">
                            ${escapeHtml(rank3.username || "Peserta")}
                        </div>
                        <div class="podium-score">
                            ${formatNumber(
                              rank3.total_score || 0
                            )} <span class="text-[11px] font-normal text-zinc-400">Pts</span>
                        </div>
                        <div class="podium-quiz-count">
                            ${parseInt(
                              rank3.completed_quizzes || 0,
                              10
                            )} Kuis Selesai
                        </div>
                    </div>
                `
                    : `
                    <div class="podium-player-card opacity-30">
                        <div class="podium-avatar-wrap">
                            <div class="podium-avatar border-zinc-700 text-zinc-600">-</div>
                            <span class="podium-rank-badge bg-zinc-800 text-zinc-400">3rd</span>
                        </div>
                        <div class="podium-username text-zinc-500">Kosong</div>
                    </div>
                `
                }
                <div class="podium-pedestal-block pedestal-3">
                    <span class="podium-pedestal-number">3</span>
                </div>
            </div>

        </div>
      `;
    }

    buildTableHtml(leaderboard, currentUserId) {
      const tableLeaderboard = (leaderboard || []).slice(3);

      if (tableLeaderboard.length === 0) {
        return `
          <div class="pixel-empty-state empty-theme-amber" style="padding: 3rem 1.5rem; background: transparent; border: none; box-shadow: none;">
              <div class="empty-scene">
                  <div class="sparkle-orbit">
                      <span class="sparkle-dot"></span>
                      <span class="sparkle-dot"></span>
                      <span class="sparkle-dot"></span>
                      <span class="sparkle-dot"></span>
                  </div>
                  <div class="empty-main-icon">
                      <svg class="w-10 h-10 pixelated" viewBox="0 0 16 16">
                          <use href="#pixel-coin"></use>
                      </svg>
                  </div>
              </div>

              <h3 class="empty-headline">Belum Ada Peserta Tambahan</h3>
              <p class="empty-subtext">
                  Papan peringkat #4 ke bawah masih kosong. Taklukkan kuis RouterOS, kumpulkan akumulasi poin, dan amankan posisimu di arena ini!
              </p>
          </div>
        `;
      }

      const rowsHtml = tableLeaderboard
        .map((row, index) => {
          const rankNumber = index + 4;
          const isCurrentUser =
            parseInt(row.id, 10) === parseInt(currentUserId, 10);

          return `
            <tr class="${isCurrentUser ? "current-user-row" : ""}">
                <td style="text-align: center;">
                    <span class="table-rank-badge rank-other">#${rankNumber}</span>
                </td>
                <td>
                    <div class="table-user-cell">
                        <div class="table-avatar-circle">
                            ${escapeHtml(
                              (row.username || "U").charAt(0).toUpperCase()
                            )}
                        </div>
                        <span class="table-username">${escapeHtml(
                          row.username || "Peserta"
                        )}</span>
                        ${isCurrentUser ? '<span class="badge-you-tag">Anda</span>' : ""}
                    </div>
                </td>
                <td style="text-align: right; font-family: var(--font-mono); color: var(--text-secondary);">
                    ${parseInt(row.completed_quizzes || 0, 10)} Kuis
                </td>
                <td style="text-align: right;">
                    <span class="table-score-cell">${formatNumber(
                      row.total_score || 0
                    )}</span>
                    <span class="table-pts-suffix">Pts</span>
                </td>
            </tr>
          `;
        })
        .join("");

      return `
        <div class="leaderboard-table-wrap">
            <table class="leaderboard-data-table">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Posisi</th>
                        <th>Peserta</th>
                        <th style="text-align: right;">Kuis Selesai</th>
                        <th style="text-align: right;">Total Skor</th>
                    </tr>
                </thead>
                <tbody>
                    ${rowsHtml}
                </tbody>
            </table>
        </div>
      `;
    }
  }

  // Auto-initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
      new NetQuizLeaderboard();
    });
  } else {
    new NetQuizLeaderboard();
  }
})();
