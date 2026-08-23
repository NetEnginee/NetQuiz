        </div>
    </main>

    <!-- Global Minimalist Vercel Dark Footer -->
    <footer class="student-dark-footer font-mono" aria-label="Footer Status">
        <div class="student-shell-container footer-inner">
            <div class="footer-left">
                <span class="nav-brand-text">Net<span class="nav-brand-accent">Quiz</span><span class="brand-cursor">_</span></span>
                <span class="footer-copy">© <?= date('Y') ?> RouterOS Academy. All rights reserved.</span>
            </div>
            <div class="footer-right">
                <span class="footer-badge">
                    <span class="badge-dot"></span>
                    <span>Edge Protected</span>
                </span>
                <span class="footer-latency">Latency: <strong class="text-zinc-300">12ms</strong></span>
            </div>
        </div>
    </footer>

    <!-- Global Scripts & Canvas / Audio Initializer -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Initialize Lucide Icons
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // 2. Web Audio API Synth Global Sound Engine
            let soundEnabled = localStorage.getItem("netquiz_sound") !== "false";
            let audioCtx = null;

            function getAudioContext() {
                if (!audioCtx) {
                    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                    if (AudioContextClass) {
                        audioCtx = new AudioContextClass();
                    }
                }
                if (audioCtx && audioCtx.state === "suspended") {
                    audioCtx.resume();
                }
                return audioCtx;
            }

            window.playPixelSound = function(type) {
                if (!soundEnabled) return;
                try {
                    const ctx = getAudioContext();
                    if (!ctx) return;

                    const now = ctx.currentTime;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    if (type === "click") {
                        osc.type = "sawtooth";
                        osc.frequency.setValueAtTime(320, now);
                        osc.frequency.exponentialRampToValueAtTime(140, now + 0.04);
                        gain.gain.setValueAtTime(0.04, now);
                        gain.gain.linearRampToValueAtTime(0, now + 0.04);
                        osc.start(now);
                        osc.stop(now + 0.04);
                    } else if (type === "blip") {
                        osc.type = "square";
                        osc.frequency.setValueAtTime(440, now);
                        osc.frequency.exponentialRampToValueAtTime(880, now + 0.08);
                        gain.gain.setValueAtTime(0.05, now);
                        gain.gain.linearRampToValueAtTime(0, now + 0.08);
                        osc.start(now);
                        osc.stop(now + 0.08);
                    } else if (type === "coin") {
                        osc.type = "square";
                        osc.frequency.setValueAtTime(987.77, now);
                        osc.frequency.setValueAtTime(1318.51, now + 0.08);
                        gain.gain.setValueAtTime(0.08, now);
                        gain.gain.linearRampToValueAtTime(0, now + 0.28);
                        osc.start(now);
                        osc.stop(now + 0.28);
                    } else if (type === "badge") {
                        osc.type = "triangle";
                        osc.frequency.setValueAtTime(523.25, now);
                        osc.frequency.setValueAtTime(659.25, now + 0.08);
                        osc.frequency.setValueAtTime(783.99, now + 0.16);
                        osc.frequency.setValueAtTime(1046.50, now + 0.24);
                        gain.gain.setValueAtTime(0.1, now);
                        gain.gain.linearRampToValueAtTime(0, now + 0.4);
                        osc.start(now);
                        osc.stop(now + 0.4);
                    }
                } catch (e) {
                    console.warn("Audio Context Notice:", e);
                }
            };

            const soundToggleBtn = document.getElementById("soundToggleBtn");
            const soundIcon = document.getElementById("soundIcon");
            const soundLabel = document.getElementById("soundLabel");

            function updateSoundButtonUI() {
                if (soundToggleBtn && soundIcon && soundLabel) {
                    if (soundEnabled) {
                        soundIcon.textContent = "🔊";
                        soundLabel.textContent = "Sound: ON";
                    } else {
                        soundIcon.textContent = "🔇";
                        soundLabel.textContent = "Sound: OFF";
                    }
                }
            }

            updateSoundButtonUI();

            if (soundToggleBtn) {
                soundToggleBtn.addEventListener("click", () => {
                    soundEnabled = !soundEnabled;
                    localStorage.setItem("netquiz_sound", soundEnabled ? "true" : "false");
                    updateSoundButtonUI();
                    if (soundEnabled) {
                        window.playPixelSound("blip");
                    }
                });
            }

            // 3. Canvas Network Data Stream Animation
            const canvas = document.getElementById("networkCanvas");
            if (canvas) {
                const ctx = canvas.getContext("2d");
                let width = (canvas.width = window.innerWidth);
                let height = (canvas.height = window.innerHeight);

                window.addEventListener("resize", () => {
                    width = canvas.width = window.innerWidth;
                    height = canvas.height = window.innerHeight;
                });

                class DataPacket {
                    constructor() {
                        this.reset();
                    }

                    reset() {
                        const gridSize = 48;
                        this.x = Math.floor(Math.random() * (width / gridSize)) * gridSize;
                        this.y = Math.floor(Math.random() * (height / gridSize)) * gridSize;
                        this.horizontal = Math.random() > 0.5;
                        this.speed = (Math.random() * 1.5 + 1) * (Math.random() > 0.5 ? 1 : -1);
                        this.size = 3;
                        this.color = Math.random() > 0.5 ? "#0070f3" : "#50e3c2";
                        this.life = 0;
                        this.maxLife = Math.floor(Math.random() * 140 + 80);
                    }

                    update() {
                        if (this.horizontal) {
                            this.x += this.speed;
                        } else {
                            this.y += this.speed;
                        }
                        this.life++;
                        if (
                            this.life > this.maxLife ||
                            this.x < 0 ||
                            this.x > width ||
                            this.y < 0 ||
                            this.y > height
                        ) {
                            this.reset();
                        }
                    }

                    draw() {
                        ctx.fillStyle = this.color;
                        for (let i = 0; i < 3; i++) {
                            const offset = i * 4;
                            const px = this.horizontal
                                ? this.x - offset * Math.sign(this.speed)
                                : this.x;
                            const py = this.horizontal
                                ? this.y
                                : this.y - offset * Math.sign(this.speed);
                            ctx.globalAlpha =
                                (1 - i * 0.3) * (1 - this.life / this.maxLife) * 0.85;
                            ctx.fillRect(px, py, this.size, this.size);
                        }
                        ctx.globalAlpha = 1;
                    }
                }

                const packetCount = Math.min(25, Math.floor(window.innerWidth / 45));
                const packets = Array.from({ length: packetCount }, () => new DataPacket());

                function animateCanvas() {
                    ctx.clearRect(0, 0, width, height);
                    packets.forEach((p) => {
                        p.update();
                        p.draw();
                    });
                    requestAnimationFrame(animateCanvas);
                }

                animateCanvas();
            }
        });
    </script>
</body>

</html>
