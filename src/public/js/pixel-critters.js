/**
 * NetQuiz - Pixel Critters Bottom Runner Engine
 * Cute animated 8-bit retro pixel mascots running across the bottom of the viewport
 * Features: Floating Name Popups, Interactive Click/Hop, Dynamic Speech Dialogue, 60FPS Smooth Motion
 */

(function (window, document) {
  "use strict";

  // Cute Mascot Definitions & Retro Pixel SVGs (24x24 pixel grid)
  const MASCOTS = [
    {
      id: "cal",
      name: "Adel",
      color: "#C084FC",
      badgeBorder: "rgba(192, 132, 252, 0.6)",
      speed: 1.25,
      quotes: [
        "Halo! Siap untuk kuis hari ini? ✨",
        "Config VLAN & Routing dulu yuk! 💜",
        "Subnetting /24 itu ada 254 host tau! 🧠",
        "Kabel LAN nya udah dicolok belum? 🔌",
        "Gas login ke WinBox 🚀",
        "Semangat mencapai peringkat 1 ya! 🏆",
      ],
      // 24x24 Pixel Art SVG of Female Character Cal (Purple Aesthetic)
      svg: `
        <svg class="critter-pixel-art" viewBox="0 0 24 24" width="36" height="36" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Cute Hair Ribbon / Hairclip (Neon Pink / Magenta) -->
          <rect x="5" y="1" width="3" height="2" fill="#FF0080" />
          <rect x="6" y="2" width="1" height="1" fill="#FFFFFF" />

          <!-- Cal Hair (Cute Purple Twintails / Long Bangs) -->
          <rect x="7" y="2" width="10" height="3" fill="#7928CA" />
          <rect x="6" y="3" width="12" height="3" fill="#7928CA" />
          <rect x="8" y="1" width="7" height="2" fill="#9333EA" />
          <!-- Left Twintail / Side Hair -->
          <rect x="4" y="4" width="3" height="7" fill="#7928CA" />
          <rect x="3" y="6" width="2" height="6" fill="#6B21A8" />
          <!-- Right Twintail / Side Hair -->
          <rect x="17" y="4" width="3" height="7" fill="#7928CA" />
          <rect x="19" y="6" width="2" height="6" fill="#6B21A8" />
          
          <!-- Face / Head (Warm Soft Skin Tone) -->
          <rect x="7" y="5" width="10" height="7" fill="#FFE0BD" />
          <!-- Front Bangs -->
          <rect x="7" y="5" width="2" height="2" fill="#7928CA" />
          <rect x="15" y="5" width="2" height="2" fill="#7928CA" />
          <rect x="11" y="5" width="2" height="1" fill="#7928CA" />
          
          <!-- Big Anime Pixel Eyes (Deep Violet with Twinkle) -->
          <rect class="critter-eye" x="8" y="7" width="3" height="3" fill="#581C87" />
          <rect class="critter-eye" x="13" y="7" width="3" height="3" fill="#581C87" />
          <rect x="8" y="7" width="1" height="1" fill="#FFFFFF" />
          <rect x="13" y="7" width="1" height="1" fill="#FFFFFF" />
          <rect x="9" y="9" width="2" height="1" fill="#A855F7" />
          <rect x="14" y="9" width="2" height="1" fill="#A855F7" />
          
          <!-- Cute Rosy Blush -->
          <rect x="7" y="9" width="2" height="1" fill="#FF8BA7" />
          <rect x="15" y="9" width="2" height="1" fill="#FF8BA7" />
          
          <!-- Cute Smile -->
          <rect x="11" y="10" width="2" height="1" fill="#E11D48" />
          
          <!-- Cyber Outfit: Purple Hoodie / Jacket with Lilac & Neon Accents -->
          <rect x="6" y="12" width="12" height="6" fill="#9333EA" />
          <rect x="7" y="12" width="10" height="5" fill="#A855F7" />
          <rect x="11" y="12" width="2" height="6" fill="#F3E8FF" /> <!-- White/Lilac Zipper -->
          
          <!-- Arms / Sleeves -->
          <rect x="4" y="13" width="2" height="4" fill="#7E22CE" />
          <rect x="4" y="16" width="2" height="2" fill="#FFE0BD" />
          <rect x="18" y="13" width="2" height="4" fill="#7E22CE" />
          <rect x="18" y="16" width="2" height="2" fill="#FFE0BD" />
          
          <!-- Cute Dark Skirt / Shorts -->
          <rect x="7" y="17" width="10" height="2" fill="#3B0764" />
          
          <!-- Running Legs / High Socks (White/Lilac) -->
          <rect class="critter-leg-l" x="8" y="19" width="3" height="3" fill="#F5D0FE" />
          <rect class="critter-leg-r" x="13" y="19" width="3" height="3" fill="#F5D0FE" />
          
          <!-- Purple Retro Sneakers with White Soles -->
          <rect class="critter-leg-l" x="7" y="21" width="4" height="2" fill="#7E22CE" />
          <rect class="critter-leg-l" x="7" y="22" width="4" height="1" fill="#FFFFFF" />
          <rect class="critter-leg-r" x="13" y="21" width="4" height="2" fill="#7E22CE" />
          <rect class="critter-leg-r" x="13" y="22" width="4" height="1" fill="#FFFFFF" />
        </svg>
      `,
    },
  ];

  // Global Runner Container Injection
  function ensureRunnerContainer() {
    let container = document.getElementById("pixel-critters-bottom-runner");
    if (!container) {
      container = document.createElement("div");
      container.id = "pixel-critters-bottom-runner";
      container.className = "pixel-critters-bottom-runner";
      container.setAttribute("aria-hidden", "true");
      document.body.appendChild(container);
    }
    return container;
  }

  // Inject Critical CSS for Pixel Critters
  function injectCritterStyles() {
    if (document.getElementById("pixel-critters-css")) return;

    const style = document.createElement("style");
    style.id = "pixel-critters-css";
    style.textContent = `
      .pixel-critters-bottom-runner {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100vw;
        height: 105px;
        pointer-events: none;
        z-index: 1005; /* Di atas seluruh section page, card, dan tepat di atas nav */
        overflow: hidden;
        user-select: none;
      }
      
      /* Tablet Breakpoint (<= 1024px) */
      @media (max-width: 1024px) {
        .pixel-critters-bottom-runner {
          /* Ditempatkan tepat di atas floating bottom nav tablet */
          bottom: calc(58px + env(safe-area-inset-bottom, 0px));
          height: 98px;
        }
      }

      /* Mobile Phone Breakpoint (<= 640px) */
      @media (max-width: 640px) {
        .pixel-critters-bottom-runner {
          /* Disesuaikan dengan pill nav mobile + safe area */
          bottom: calc(62px + env(safe-area-inset-bottom, 0px));
          height: 92px;
        }
      }

      .critter-walker-box {
        position: absolute;
        bottom: 4px;
        left: 0;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        pointer-events: auto;
        cursor: pointer;
        will-change: transform;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
      }

      /* Name & Speech Popup Bubble - Perfectly Positioned Above Sprite */
      .critter-name-bubble {
        position: absolute;
        bottom: 42px;
        left: 50%;
        transform: translateX(-50%);
        display: inline-flex;
        align-items: center;
        background: rgba(13, 10, 24, 0.95);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        border: 1px solid var(--bubble-border, rgba(192, 132, 252, 0.6));
        border-radius: 8px;
        padding: 4px 10px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.75), 0 0 12px var(--bubble-glow, rgba(192, 132, 252, 0.3));
        font-family: 'JetBrains Mono', 'Press Start 2P', monospace;
        font-size: 11px;
        font-weight: 700;
        color: #ffffff;
        white-space: nowrap;
        max-width: min(85vw, 280px);
        overflow: hidden;
        text-overflow: ellipsis;
        pointer-events: none;
        animation: critterBubbleFloat 2s ease-in-out infinite alternate;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      }

      /* Mobile & Tablet Typography Adjustment */
      @media (max-width: 768px) {
        .critter-name-bubble {
          font-size: 10px;
          padding: 3px 8px;
          bottom: 40px;
        }
      }

      /* Pixel Arrow Pointer Notch at Bottom of Bubble */
      .critter-name-bubble::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid rgba(13, 10, 24, 0.95);
      }

      .critter-name-text {
        color: #f8fafc;
        letter-spacing: -0.01em;
        display: block;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      /* Speech Dialog Mode (When Cal Speaks) */
      .critter-name-bubble.speech-active {
        background: rgba(18, 12, 34, 0.98);
        border-color: #D8B4FE;
        color: #ffffff;
        padding: 5px 12px;
        font-size: 11.5px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.85), 0 0 18px rgba(192, 132, 252, 0.5);
      }

      @media (max-width: 768px) {
        .critter-name-bubble.speech-active {
          font-size: 10.5px;
          padding: 4px 10px;
        }
      }

      .critter-name-bubble.speech-active::after {
        border-top-color: rgba(18, 12, 34, 0.98);
      }

      .critter-name-bubble.speech-active .critter-name-text {
        color: #ffffff;
        font-weight: 800;
      }

      /* Pixel Sprite Container */
      .critter-sprite-wrap {
        position: relative;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        image-rendering: pixelated;
        image-rendering: crisp-edges;
        transition: transform 0.15s ease-out;
      }

      .critter-pixel-art {
        image-rendering: pixelated;
        image-rendering: crisp-edges;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.7));
      }

      /* Running Leg Animations */
      .critter-running .critter-leg-l {
        animation: critterLegCycle 0.22s steps(2, start) infinite alternate;
      }
      .critter-running .critter-leg-r {
        animation: critterLegCycle 0.22s steps(2, start) infinite alternate-reverse;
      }
      .critter-running .critter-sprite-wrap {
        animation: critterRunBob 0.22s ease-in-out infinite alternate;
      }

      /* Wing Flap */
      .critter-running .critter-wings {
        animation: critterWingFlap 0.14s steps(2, start) infinite alternate;
      }

      /* Tail Wag */
      .critter-tail {
        transform-origin: 17px 14px;
        animation: critterTailWag 0.6s ease-in-out infinite alternate;
      }

      /* Hop Jump Animation on Click */
      .critter-hopping .critter-sprite-wrap {
        animation: critterJump 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
      }

      @keyframes critterBubbleFloat {
        0% { transform: translateY(0px); }
        100% { transform: translateY(-3px); }
      }

      @keyframes critterRunBob {
        0% { transform: translateY(0px); }
        100% { transform: translateY(-2px); }
      }

      @keyframes critterLegCycle {
        0% { transform: translateY(0px); }
        100% { transform: translateY(-3px); }
      }

      @keyframes critterWingFlap {
        0% { transform: scaleY(1); }
        100% { transform: scaleY(0.65); }
      }

      @keyframes critterTailWag {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(18deg); }
      }

      @keyframes critterJump {
        0% { transform: translateY(0) scale(1, 1); }
        35% { transform: translateY(-24px) scale(0.9, 1.15); }
        70% { transform: translateY(-8px) scale(1.05, 0.95); }
        100% { transform: translateY(0) scale(1, 1); }
      }
    `;
    (document.head || document.documentElement).appendChild(style);
  }

  // Critter Mascot Entity Class
  class PixelCritter {
    constructor(config, container) {
      this.config = config;
      this.container = container;
      this.id = config.id;
      this.width = 44;
      this.screenW = window.innerWidth;

      // Position & Dynamics (Jalan santai)
      this.x = Math.max(50, Math.random() * (this.screenW - 140));
      this.direction = Math.random() > 0.5 ? 1 : -1; // 1 = right, -1 = left
      this.speed = config.speed * 0.85; // Kecepatan jalan santai
      this.currentSpeed = this.speed;

      // State Machine: 'walking' | 'idling' | 'hopping'
      this.state = "walking";
      this.stateTimer = Math.floor(Math.random() * 250 + 200);
      this.speechTimeout = null;

      this.createDOM();
    }

    createDOM() {
      this.el = document.createElement("div");
      this.el.className = "critter-walker-box critter-running";
      this.el.style.setProperty("--bubble-color", this.config.color);
      this.el.style.setProperty("--bubble-border", this.config.badgeBorder);
      this.el.style.setProperty("--bubble-glow", this.config.badgeBorder);

      this.el.innerHTML = `
        <div class="critter-name-bubble font-mono">
          <span class="critter-name-text">${this.config.name}</span>
        </div>
        <div class="critter-sprite-wrap">
          ${this.config.svg}
        </div>
      `;

      this.bubbleText = this.el.querySelector(".critter-name-text");
      this.bubbleEl = this.el.querySelector(".critter-name-bubble");

      // Click / Touch Interaction
      this.el.addEventListener("click", (e) => {
        e.stopPropagation();
        this.interact();
      });

      this.container.appendChild(this.el);
      this.updatePosition();
    }

    interact() {
      // 1. Play Sound if sound engine is available
      if (typeof window.playPixelSound === "function") {
        window.playPixelSound("coin");
      }

      // 2. Jump Hop Animation
      this.hop();

      // 3. Show Speech Bubble Dialogue
      const quote =
        this.config.quotes[
          Math.floor(Math.random() * this.config.quotes.length)
        ];
      this.speak(quote, 2800);

      // 4. Boost jalan sedikit saat disapa
      this.currentSpeed = this.speed * 1.4;
      setTimeout(() => {
        this.currentSpeed = this.speed;
      }, 1500);
    }

    hop() {
      this.el.classList.add("critter-hopping");
      setTimeout(() => {
        this.el.classList.remove("critter-hopping");
      }, 600);
    }

    speak(text, duration = 2500) {
      if (this.speechTimeout) clearTimeout(this.speechTimeout);

      this.bubbleText.textContent = text;
      this.bubbleEl.classList.add("speech-active");

      this.speechTimeout = setTimeout(() => {
        this.bubbleText.textContent = this.config.name;
        this.bubbleEl.classList.remove("speech-active");
      }, duration);
    }

    update(screenW, deltaFactor = 1) {
      this.screenW = screenW;
      // Margin yang cukup besar agar seluruh karakter dan balon nama/pesan keluar layar penuh
      const wrapMargin = Math.min(160, Math.max(90, screenW * 0.25));

      // State Machine Update
      this.stateTimer -= deltaFactor;
      if (this.stateTimer <= 0) {
        if (this.state === "walking") {
          // Berhenti istirahat sejenak (santai/idle)
          this.state = "idling";
          this.stateTimer = Math.floor(Math.random() * 120 + 60); // Diam 1-3 detik
          this.el.classList.remove("critter-running");

          // 30% kemungkinan menyapa saat santai
          if (Math.random() < 0.35) {
            const quote =
              this.config.quotes[
                Math.floor(Math.random() * this.config.quotes.length)
              ];
            this.speak(quote, 2200);
          }
        } else {
          // Mulai berjalan santai kembali
          this.state = "walking";
          this.stateTimer = Math.floor(Math.random() * 320 + 220); // Jalan 4-9 detik
          this.el.classList.add("critter-running");

          // 30% kemungkinan putar balik arah jalan
          if (Math.random() < 0.3) {
            this.direction = -this.direction;
          }
        }
      }

      // Pergerakan jalan santai
      if (this.state === "walking") {
        this.x += this.direction * this.currentSpeed * deltaFactor;

        // Screen wrap-around: keluar penuh di satu sisi dan masuk perlahan di sisi lainnya
        if (this.x > this.screenW + wrapMargin) {
          this.x = -wrapMargin;
        } else if (this.x < -wrapMargin) {
          this.x = this.screenW + wrapMargin;
        }
      }

      this.updatePosition();
    }

    updatePosition() {
      const scaleX = this.direction >= 0 ? 1 : -1;
      this.el.style.transform = `translate3d(${Math.round(this.x)}px, 0, 0)`;

      const spriteWrap = this.el.querySelector(".critter-sprite-wrap");
      if (spriteWrap) {
        spriteWrap.style.transform = `scaleX(${scaleX})`;
      }
    }
  }

  // Engine Initializer: Single Cal/Adel strolling calmly along the bottom
  function initCrittersEngine() {
    if (
      window.__pixelCrittersInitialized &&
      document.getElementById("pixel-critters-bottom-runner")
    ) {
      return;
    }
    window.__pixelCrittersInitialized = true;

    injectCritterStyles();
    const container = ensureRunnerContainer();

    // Pastikan container bersih jika di-reinit
    container.innerHTML = "";

    // Hanya menggunakan karakter Cal/Adel
    const calConfig = MASCOTS.find((m) => m.id === "cal") || MASCOTS[0];
    const cal = new PixelCritter(calConfig, container);

    function updateViewportWidth() {
      return (
        window.innerWidth ||
        document.documentElement.clientWidth ||
        document.body.clientWidth ||
        360
      );
    }

    let screenWidth = updateViewportWidth();

    window.addEventListener(
      "resize",
      () => {
        screenWidth = updateViewportWidth();
      },
      { passive: true },
    );

    window.addEventListener(
      "orientationchange",
      () => {
        setTimeout(() => {
          screenWidth = updateViewportWidth();
        }, 150);
      },
      { passive: true },
    );

    let isVisible = !document.hidden;
    document.addEventListener("visibilitychange", () => {
      isVisible = !document.hidden;
    });

    // High-performance Framerate-independent Game Loop (Smooth on 60Hz/90Hz/120Hz mobile screens)
    let lastTime = performance.now();

    function loop(now) {
      if (isVisible) {
        const deltaMs = Math.min(now - lastTime, 100); // Batasi max delta agar tidak loncat saat tab resume
        const deltaFactor = deltaMs / 16.666; // Normalisasi ke target 60FPS
        cal.update(screenWidth, deltaFactor);
      }
      lastTime = now;
      requestAnimationFrame(loop);
    }

    requestAnimationFrame((now) => {
      lastTime = now;
      loop(now);
    });
  }

  // Multi-lifecycle hooks untuk memastikan berjalan di semua views & SPA navigation
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initCrittersEngine);
  } else {
    initCrittersEngine();
  }

  // Backup fallback on window load & pageshow (BFCache / dynamic page transitions)
  window.addEventListener("pageshow", function () {
    setTimeout(initCrittersEngine, 50);
  });
})(window, document);
