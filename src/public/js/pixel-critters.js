/**
 * NetQuiz - Pixel Critters Interactive Background Engine
 * 8-Bit Retro Animated Floating Characters (Routers, Robots, Coins, Packets, Terminals)
 * High-performance 60FPS HTML5 Canvas Particle System
 */

(function () {
  'use strict';

  function initPixelCritters() {
    const canvas = document.getElementById('pixelCrittersCanvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d', { alpha: true });
    if (!ctx) return;

    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    // Mouse Interaction Coordinates
    const mouse = { x: -1000, y: -1000, radius: 130 };

    window.addEventListener('mousemove', (e) => {
      mouse.x = e.clientX;
      mouse.y = e.clientY;
    }, { passive: true });

    window.addEventListener('mouseleave', () => {
      mouse.x = -1000;
      mouse.y = -1000;
    });

    window.addEventListener('resize', () => {
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;
    }, { passive: true });

    // Helper: Create an offscreen pixel sprite
    function createOffscreenSprite(size, drawFn) {
      const offCanvas = document.createElement('canvas');
      offCanvas.width = size;
      offCanvas.height = size;
      const offCtx = offCanvas.getContext('2d');
      offCtx.imageSmoothingEnabled = false;
      drawFn(offCtx, size);
      return offCanvas;
    }

    const PIXEL_SCALE = 2.5;

    // Sprite 1: MikroTik Router (16x16)
    const routerSprite = createOffscreenSprite(16 * PIXEL_SCALE, (c, s) => {
      const p = PIXEL_SCALE;
      // Antennas
      c.fillStyle = '#50E3C2';
      c.fillRect(4 * p, 1 * p, 1 * p, 6 * p);
      c.fillRect(11 * p, 1 * p, 1 * p, 6 * p);
      // Main Body
      c.fillStyle = '#0070F3';
      c.fillRect(2 * p, 7 * p, 12 * p, 6 * p);
      // Ports
      c.fillStyle = '#7928CA';
      c.fillRect(3 * p, 8 * p, 2 * p, 2 * p);
      c.fillRect(7 * p, 8 * p, 2 * p, 2 * p);
      c.fillRect(11 * p, 8 * p, 2 * p, 2 * p);
      // Green LEDs
      c.fillStyle = '#00FF66';
      c.fillRect(3 * p, 11 * p, 1 * p, 1 * p);
      c.fillRect(5 * p, 11 * p, 1 * p, 1 * p);
      c.fillRect(7 * p, 11 * p, 1 * p, 1 * p);
      c.fillRect(9 * p, 11 * p, 1 * p, 1 * p);
      // Base shadow
      c.fillStyle = '#000000';
      c.fillRect(1 * p, 13 * p, 14 * p, 1 * p);
    });

    // Sprite 2: Cute Mini AI Robot (16x16)
    const robotSprite = createOffscreenSprite(16 * PIXEL_SCALE, (c, s) => {
      const p = PIXEL_SCALE;
      // Antenna
      c.fillStyle = '#50E3C2';
      c.fillRect(7 * p, 1 * p, 2 * p, 2 * p);
      // Head
      c.fillStyle = '#7928CA';
      c.fillRect(4 * p, 3 * p, 8 * p, 5 * p);
      // Visor
      c.fillStyle = '#000000';
      c.fillRect(5 * p, 4 * p, 6 * p, 2 * p);
      c.fillStyle = '#FF0080';
      c.fillRect(5 * p, 4 * p, 2 * p, 2 * p);
      c.fillRect(9 * p, 4 * p, 2 * p, 2 * p);
      // Body
      c.fillStyle = '#0070F3';
      c.fillRect(3 * p, 8 * p, 10 * p, 5 * p);
      // Chest Light
      c.fillStyle = '#50E3C2';
      c.fillRect(7 * p, 9 * p, 2 * p, 2 * p);
      // Thrusters
      c.fillStyle = '#F5A623';
      c.fillRect(5 * p, 13 * p, 2 * p, 2 * p);
      c.fillRect(9 * p, 13 * p, 2 * p, 2 * p);
    });

    // Sprite 3: Spinning Gold Coin (16x16)
    const coinSprite = createOffscreenSprite(16 * PIXEL_SCALE, (c, s) => {
      const p = PIXEL_SCALE;
      c.fillStyle = '#F5A623';
      c.fillRect(4 * p, 2 * p, 8 * p, 12 * p);
      c.fillRect(2 * p, 4 * p, 12 * p, 8 * p);
      c.fillStyle = '#FFE17D';
      c.fillRect(5 * p, 3 * p, 4 * p, 2 * p);
      c.fillRect(3 * p, 5 * p, 2 * p, 4 * p);
      c.fillStyle = '#D48806';
      c.fillRect(7 * p, 11 * p, 4 * p, 2 * p);
      c.fillRect(11 * p, 7 * p, 2 * p, 4 * p);
      c.fillStyle = '#FFFFFF';
      c.fillRect(6 * p, 6 * p, 4 * p, 4 * p);
    });

    // Sprite 4: Flying Data Packet (16x16)
    const packetSprite = createOffscreenSprite(16 * PIXEL_SCALE, (c, s) => {
      const p = PIXEL_SCALE;
      // Wings
      c.fillStyle = '#FFFFFF';
      c.fillRect(1 * p, 3 * p, 3 * p, 4 * p);
      c.fillRect(12 * p, 3 * p, 3 * p, 4 * p);
      // Packet Box
      c.fillStyle = '#50E3C2';
      c.fillRect(4 * p, 4 * p, 8 * p, 8 * p);
      // Inner Bit Grid
      c.fillStyle = '#050505';
      c.fillRect(5 * p, 5 * p, 6 * p, 6 * p);
      c.fillStyle = '#00FF66';
      c.fillRect(6 * p, 6 * p, 2 * p, 1 * p);
      c.fillRect(6 * p, 8 * p, 4 * p, 1 * p);
    });

    // Sprite 5: CRT Terminal / Monitor (16x16)
    const terminalSprite = createOffscreenSprite(16 * PIXEL_SCALE, (c, s) => {
      const p = PIXEL_SCALE;
      c.fillStyle = '#27272A';
      c.fillRect(2 * p, 2 * p, 12 * p, 10 * p);
      // Stand
      c.fillRect(6 * p, 12 * p, 4 * p, 2 * p);
      c.fillRect(4 * p, 14 * p, 8 * p, 1 * p);
      // Screen
      c.fillStyle = '#09090B';
      c.fillRect(3 * p, 3 * p, 10 * p, 8 * p);
      // Code Lines
      c.fillStyle = '#50E3C2';
      c.fillRect(4 * p, 4 * p, 5 * p, 1 * p);
      c.fillRect(4 * p, 6 * p, 7 * p, 1 * p);
      c.fillStyle = '#00FF66';
      c.fillRect(4 * p, 8 * p, 2 * p, 1 * p);
    });

    // Sprite 6: 404 Lost Wifi / Ghost (16x16)
    const ghostSprite = createOffscreenSprite(16 * PIXEL_SCALE, (c, s) => {
      const p = PIXEL_SCALE;
      c.fillStyle = 'rgba(80, 227, 194, 0.85)';
      c.fillRect(4 * p, 2 * p, 8 * p, 10 * p);
      c.fillRect(2 * p, 4 * p, 12 * p, 8 * p);
      // Eyes
      c.fillStyle = '#000000';
      c.fillRect(4 * p, 5 * p, 2 * p, 2 * p);
      c.fillRect(10 * p, 5 * p, 2 * p, 2 * p);
      // Wavy tail
      c.fillRect(3 * p, 12 * p, 2 * p, 2 * p);
      c.fillRect(7 * p, 12 * p, 2 * p, 2 * p);
      c.fillRect(11 * p, 12 * p, 2 * p, 2 * p);
    });

    const spritePool = [
      routerSprite,
      robotSprite,
      coinSprite,
      packetSprite,
      terminalSprite,
      ghostSprite
    ];

    // Initialize 20-30 Floating Pixel Critters
    const critterCount = Math.max(16, Math.min(32, Math.floor(window.innerWidth / 50)));
    const critters = [];

    for (let i = 0; i < critterCount; i++) {
      critters.push({
        x: Math.random() * width,
        y: Math.random() * height,
        vx: (Math.random() - 0.5) * 0.85,
        vy: (Math.random() - 0.5) * 0.85,
        sprite: spritePool[Math.floor(Math.random() * spritePool.length)],
        size: 16 * PIXEL_SCALE,
        scale: 0.75 + Math.random() * 0.5,
        opacity: 0.45 + Math.random() * 0.5,
        floatPhase: Math.random() * Math.PI * 2,
        floatSpeed: 0.02 + Math.random() * 0.03,
        rotation: (Math.random() - 0.5) * 0.2,
        rotSpeed: (Math.random() - 0.5) * 0.015
      });
    }

    // Initialize 25 Twinkling Stars
    const starCount = 30;
    const stars = [];
    for (let i = 0; i < starCount; i++) {
      stars.push({
        x: Math.random() * width,
        y: Math.random() * height,
        size: Math.random() < 0.5 ? 2 : 3,
        phase: Math.random() * Math.PI * 2,
        speed: 0.03 + Math.random() * 0.04,
        color: Math.random() < 0.3 ? '#50E3C2' : (Math.random() < 0.6 ? '#F5A623' : '#FFFFFF')
      });
    }

    let isRunning = true;

    document.addEventListener('visibilitychange', () => {
      isRunning = !document.hidden;
      if (isRunning) requestAnimationFrame(loop);
    });

    function loop() {
      if (!isRunning) return;

      ctx.clearRect(0, 0, width, height);

      // 1. Draw Twinkling Stars
      for (let s of stars) {
        s.phase += s.speed;
        const alpha = 0.2 + Math.sin(s.phase) * 0.25;
        if (alpha > 0) {
          ctx.fillStyle = s.color;
          ctx.globalAlpha = Math.max(0, Math.min(1, alpha));
          ctx.fillRect(Math.floor(s.x), Math.floor(s.y), s.size, s.size);
        }
      }

      // 2. Draw & Update Critters
      for (let c of critters) {
        c.floatPhase += c.floatSpeed;
        c.rotation += c.rotSpeed;

        // Base velocity drift + floating hover wobble
        c.x += c.vx;
        c.y += c.vy + Math.sin(c.floatPhase) * 0.35;

        // Screen boundary rebound
        const pad = c.size;
        if (c.x < -pad) c.x = width + pad;
        if (c.x > width + pad) c.x = -pad;
        if (c.y < -pad) c.y = height + pad;
        if (c.y > height + pad) c.y = -pad;

        // Interactive Mouse Avoidance / Repulsion
        const dx = c.x - mouse.x;
        const dy = c.y - mouse.y;
        const dist = Math.sqrt(dx * dx + dy * dy);

        if (dist < mouse.radius && dist > 0) {
          const force = (mouse.radius - dist) / mouse.radius;
          c.x += (dx / dist) * force * 3.5;
          c.y += (dy / dist) * force * 3.5;
          c.rotation += 0.05;
        }

        // Render Critter
        ctx.save();
        ctx.translate(Math.floor(c.x), Math.floor(c.y));
        ctx.rotate(c.rotation);
        ctx.scale(c.scale, c.scale);
        ctx.globalAlpha = c.opacity;
        ctx.imageSmoothingEnabled = false;
        ctx.drawImage(c.sprite, -c.size / 2, -c.size / 2);
        ctx.restore();
      }

      ctx.globalAlpha = 1;
      requestAnimationFrame(loop);
    }

    requestAnimationFrame(loop);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPixelCritters);
  } else {
    initPixelCritters();
  }
})();
