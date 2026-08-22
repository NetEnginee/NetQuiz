/**
 * NetQuiz - Fullscreen Geist Terminal HUD Loading Engine
 * Standards: Vercel Geist / Linear / CAD Blueprint Minimalist
 * Zero Dependencies | Ultra-Snappy Non-Blocking (<2.5KB) | Zero-Lag Hardware Accelerated
 */
(function (window, document) {
  'use strict';

  let status = null;
  let timer = null;
  let safetyTimeout = null;

  // DOM Elements
  let fsLoaderEl = null;
  let hudFillEl = null;
  let hudPercentEl = null;
  let hudStatusEl = null;

  const STATUS_MESSAGES = [
    { threshold: 0, text: '[SYS.INIT // DISPATCHING_BUFFERS]' },
    { threshold: 25, text: '[ROUTEROS // MOUNTING_CORE_V8]' },
    { threshold: 60, text: '[NETQUIZ // SYNC_COMPONENTS]' },
    { threshold: 85, text: '[TERMINAL // READY_FOR_DISPATCH]' },
    { threshold: 100, text: '[SYSTEM // CONNECTION_ONLINE]' }
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

  function createLoaderElements() {
    if (fsLoaderEl) return;

    fsLoaderEl = document.createElement('div');
    fsLoaderEl.id = 'netquiz-fullscreen-loader';
    fsLoaderEl.className = 'netquiz-fullscreen-loader';
    fsLoaderEl.setAttribute('aria-hidden', 'true');

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

    document.documentElement.appendChild(fsLoaderEl);
    hudFillEl = fsLoaderEl.querySelector('#hud-progress-fill');
    hudPercentEl = fsLoaderEl.querySelector('#hud-percentage');
    hudStatusEl = fsLoaderEl.querySelector('#hud-status-text');
  }

  function set(percent) {
    createLoaderElements();
    percent = Math.max(0, Math.min(100, percent));
    status = percent;

    if (!fsLoaderEl.classList.contains('active')) {
      fsLoaderEl.classList.add('active');
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
      amount = 4;
    } else if (status < 85) {
      amount = 2;
    } else if (status < 96) {
      amount = 0.5;
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
      timer = setInterval(trickle, 140);
    }, 10);

    // Safety fallback auto-dismiss (never lock UI for more than 1.5s)
    safetyTimeout = setTimeout(() => {
      if (status !== null) {
        done(true);
      }
    }, 1500);
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
        fsLoaderEl.classList.add('fade-out');
      }

      setTimeout(() => {
        if (fsLoaderEl) {
          fsLoaderEl.classList.remove('active', 'fade-out');
          if (hudFillEl) hudFillEl.style.width = '0%';
          if (hudPercentEl) hudPercentEl.textContent = '0%';
        }
        status = null;
      }, 250);
    }, 180);
  }

  // Global Controller Export
  window.NetQuizLoading = {
    start: start,
    set: set,
    done: done,
    isStarted: function () {
      return status !== null;
    }
  };

  // Immediate check on script execution
  if (document.readyState === 'loading') {
    start();
    document.addEventListener('DOMContentLoaded', function () {
      done();
    });
  }

  // Handle browser back/forward navigation (bfcache)
  window.addEventListener('pageshow', function () {
    done(true);
  });

  // Universal Navigation & Form Click Interceptors
  document.addEventListener('click', function (e) {
    if (e.which > 1 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
      return;
    }

    const anchor = e.target.closest('a');
    if (!anchor) return;
    if (e.defaultPrevented) return;

    const href = anchor.getAttribute('href');
    if (!href) return;

    const target = anchor.getAttribute('target');
    if (target && target !== '_self') return;

    if (
      href.startsWith('#') ||
      href.startsWith('javascript:') ||
      href.startsWith('mailto:') ||
      href.startsWith('tel:') ||
      anchor.hasAttribute('download')
    ) {
      return;
    }

    try {
      const url = new URL(anchor.href, window.location.href);
      if (url.origin === window.location.origin) {
        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash === window.location.hash) {
          return;
        }
        start();
      }
    } catch (err) {
      // Ignore invalid URLs
    }
  });

  document.addEventListener('submit', function (e) {
    if (e.defaultPrevented) return;
    const form = e.target;
    if (form && (!form.hasAttribute('target') || form.getAttribute('target') === '_self')) {
      start();
    }
  });

})(window, document);
