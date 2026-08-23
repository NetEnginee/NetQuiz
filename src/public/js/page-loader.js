/**
 * NetQuiz - Fullscreen Geist Terminal HUD Loading Engine
 * Standards: Vercel Geist / Linear / CAD Blueprint Minimalist
 * Zero Dependencies | Ultra-Snappy Non-Blocking | Self-Contained Resilient Styles
 */
(function (window, document) {
  "use strict";

  let status = null;
  let timer = null;
  let safetyTimeout = null;

  // DOM Elements
  let fsLoaderEl = null;
  let hudFillEl = null;
  let hudPercentEl = null;
  let hudStatusEl = null;
  let styleInjected = false;

  const STATUS_MESSAGES = [
    { threshold: 0, text: "[SYS.INIT // DISPATCHING_BUFFERS]" },
    { threshold: 25, text: "[ROUTEROS // MOUNTING_CORE_V8]" },
    { threshold: 60, text: "[NETQUIZ // SYNC_COMPONENTS]" },
    { threshold: 85, text: "[TERMINAL // READY_FOR_DISPATCH]" },
    { threshold: 100, text: "[SYSTEM // CONNECTION_ONLINE]" },
  ];

  function getStatusMessage(percent) {
    let msg = STATUS_MESSAGES[0].text;
    for (let i = 0; i < STATUS_MESSAGES.length; i++) {
      if (percent >= STATUS_MESSAGES[i].threshold) {
        msg = STATUS_MESSAGES[i].text;
      }
    }
    return msg;
  }

  function injectLoaderStyles() {
    if (styleInjected || document.getElementById("netquiz-loader-styles"))
      return;
    styleInjected = true;

    const style = document.createElement("style");
    style.id = "netquiz-loader-styles";
    style.textContent = `
      .netquiz-fullscreen-loader {
        position: fixed;
        inset: 0;
        z-index: 999999;
        background-color: rgba(0, 0, 0, 0.88);
        -webkit-backdrop-filter: blur(12px);
        backdrop-filter: blur(12px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.2s;
        will-change: opacity, transform;
      }
      .netquiz-fullscreen-loader.active {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
      }
      .netquiz-fullscreen-loader.fade-out {
        opacity: 0;
        transform: scale(1.02);
        pointer-events: none;
      }
      .hud-canvas-dots {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 24px 24px;
        pointer-events: none;
        z-index: 1;
      }
      .hud-card {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 360px;
        background-color: #0a0a0a;
        border: 1px solid #222222;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 0 30px rgba(0, 112, 243, 0.15), 0 20px 40px rgba(0, 0, 0, 0.8);
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }
      .hud-crosshair {
        position: absolute;
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 600;
        color: #52525b;
        line-height: 1;
        user-select: none;
        pointer-events: none;
      }
      .hud-tl { top: -6px; left: -6px; }
      .hud-tr { top: -6px; right: -6px; }
      .hud-bl { bottom: -6px; left: -6px; }
      .hud-br { bottom: -6px; right: -6px; }
      .hud-header {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #1e1e1e;
      }
      .hud-brand-mark {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background-color: #18181b;
        color: #ffffff;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.8rem;
        font-weight: 800;
        border: 1px solid #27272a;
      }
      .hud-term-prompt {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        font-weight: 800;
        color: #50e3c2;
      }
      .hud-live-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 6px #10b981;
      }
      .hud-brand-title {
        font-family: 'Inter', sans-serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: -0.02em;
      }
      .hud-accent {
        color: #0070f3;
        font-weight: 700;
      }
      .hud-cursor {
        color: #0070f3;
        animation: hud-blink 1.2s infinite;
      }
      @keyframes hud-blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
      }
      .hud-body {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }
      .hud-status-line {
        font-size: 11px;
        color: #a1a1aa;
        letter-spacing: 0.04em;
        min-height: 16px;
      }
      .hud-meter-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
      }
      .hud-progress-track {
        flex: 1;
        height: 6px;
        background-color: #18181b;
        border: 1px solid #27272a;
        border-radius: 3px;
        overflow: hidden;
        position: relative;
      }
      .hud-progress-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #0070f3, #50e3c2);
        border-radius: 2px;
        transition: width 0.14s ease-out;
        box-shadow: 0 0 10px rgba(80, 227, 194, 0.4);
      }
      .hud-percent-number {
        font-size: 11px;
        font-weight: 700;
        color: #50e3c2;
        min-width: 32px;
        text-align: right;
      }
    `;
    (document.head || document.documentElement).appendChild(style);
  }

  function createLoaderElements() {
    if (fsLoaderEl) return;
    injectLoaderStyles();

    fsLoaderEl = document.createElement("div");
    fsLoaderEl.id = "netquiz-fullscreen-loader";
    fsLoaderEl.className = "netquiz-fullscreen-loader";
    fsLoaderEl.setAttribute("aria-hidden", "true");

    fsLoaderEl.innerHTML = `
      <div class="hud-canvas-dots" aria-hidden="true"></div>
      <div class="hud-card">
        <span class="hud-crosshair hud-tl">+</span>
        <span class="hud-crosshair hud-tr">+</span>
        <span class="hud-crosshair hud-bl">+</span>
        <span class="hud-crosshair hud-br">+</span>
        <div class="hud-header">
          <div class="hud-brand-mark">
            <span class="hud-term-prompt">&gt;_</span>
            <span class="hud-live-dot"></span>
          </div>
          <div class="hud-brand-title">Net<span class="hud-accent">Quiz</span><span class="hud-cursor font-mono">_</span></div>
        </div>
        <div class="hud-body">
          <div class="hud-status-line font-mono">
            <span id="hud-status-text">[SYS.INIT // DISPATCHING_BUFFERS]</span>
          </div>
          <div class="hud-meter-row">
            <div class="hud-progress-track">
              <div id="hud-progress-fill" class="hud-progress-fill"></div>
            </div>
            <span id="hud-percentage" class="hud-percent-number font-mono">0%</span>
          </div>
        </div>
      </div>
    `;

    (document.body || document.documentElement).appendChild(fsLoaderEl);
    hudFillEl = fsLoaderEl.querySelector("#hud-progress-fill");
    hudPercentEl = fsLoaderEl.querySelector("#hud-percentage");
    hudStatusEl = fsLoaderEl.querySelector("#hud-status-text");
  }

  function set(percent) {
    createLoaderElements();
    percent = Math.max(0, Math.min(100, percent));
    status = percent;

    if (!fsLoaderEl.classList.contains("active")) {
      fsLoaderEl.classList.add("active");
    }
    if (hudFillEl) {
      hudFillEl.style.width = `${percent}%`;
    }
    if (hudPercentEl) {
      hudPercentEl.textContent = `${Math.round(percent)}%`;
    }
    if (hudStatusEl) {
      hudStatusEl.textContent = getStatusMessage(percent);
    }
  }

  function trickle() {
    if (status === null) return;
    let amount = 0;
    if (status < 25) {
      amount = 8;
    } else if (status < 60) {
      amount = 5;
    } else if (status < 85) {
      amount = 3;
    } else if (status < 96) {
      amount = 1;
    } else {
      amount = 0;
    }
    set(status + amount);
  }

  function start() {
    if (status !== null) return;
    set(0);
    clearInterval(timer);
    clearTimeout(safetyTimeout);

    setTimeout(() => {
      set(28);
      timer = setInterval(trickle, 100);
    }, 10);

    // Safety fallback auto-dismiss (never lock UI for more than 1.2s)
    safetyTimeout = setTimeout(() => {
      done(true);
    }, 1200);
  }

  function done(force = false) {
    if (status === null && !force) return;
    clearInterval(timer);
    clearTimeout(safetyTimeout);
    timer = null;
    safetyTimeout = null;
    set(100);

    setTimeout(() => {
      if (fsLoaderEl) {
        fsLoaderEl.classList.add("fade-out");
      }

      setTimeout(() => {
        if (fsLoaderEl) {
          fsLoaderEl.classList.remove("active", "fade-out");
          if (hudFillEl) hudFillEl.style.width = "0%";
          if (hudPercentEl) hudPercentEl.textContent = "0%";
        }
        status = null;
      }, 200);
    }, 120);
  }

  // Global Controller Export
  window.NetQuizLoading = {
    start: start,
    set: set,
    done: done,
    isStarted: function () {
      return status !== null;
    },
  };

  // Immediate check on script execution
  if (document.readyState === "loading") {
    start();
    document.addEventListener("DOMContentLoaded", function () {
      done();
    });
    window.addEventListener("load", function () {
      done(true);
    });
  } else {
    // If already loaded or parsed
    done(true);
  }

  // Handle browser back/forward navigation (bfcache)
  window.addEventListener("pageshow", function (event) {
    done(true);
    if (event.persisted) {
      window.location.reload();
    }
  });

  // Universal Navigation Click Interceptor (Only for standard cross-page GET links)
  document.addEventListener("click", function (e) {
    if (e.which > 1 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
      return;
    }

    const anchor = e.target.closest("a");
    if (!anchor) return;
    if (e.defaultPrevented) return;

    const href = anchor.getAttribute("href");
    if (!href) return;

    const target = anchor.getAttribute("target");
    if (target && target !== "_self") return;

    if (
      href.startsWith("#") ||
      href.startsWith("javascript:") ||
      href.startsWith("mailto:") ||
      href.startsWith("tel:") ||
      anchor.hasAttribute("download")
    ) {
      return;
    }

    try {
      const url = new URL(anchor.href, window.location.href);
      if (url.origin === window.location.origin) {
        if (
          url.pathname === window.location.pathname &&
          url.search === window.location.search &&
          url.hash === window.location.hash
        ) {
          return;
        }
        start();
      }
    } catch (err) {
      // Ignore invalid URLs
    }
  });

  // Intercept form submissions only for traditional full-page GET/POST (exclude AJAX forms)
  document.addEventListener("submit", function (e) {
    const form = e.target;
    // If submission is handled via JS (novalidate or preventDefault in AJAX like login), do not lock with full page loader
    if (form && !form.hasAttribute("data-ajax") && form.id !== "login-form") {
      if (
        !form.hasAttribute("target") ||
        form.getAttribute("target") === "_self"
      ) {
        start();
      }
    }
  });
})(window, document);
