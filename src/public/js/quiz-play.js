// --- NETQUIZ QUIZ PLAY CONTROLLER (EXTERNAL JS) ---
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize global controls and configurations
    if (window.lucide) {
        lucide.createIcons();
    }

    window.isQuizSubmitting = false;

    // Read the securely exposed config object
    const config = window.NetQuizData || {};
    const quizId = parseInt(config.quizId, 10) || 0;
    const userId = parseInt(config.userId, 10) || 0;
    const durationSeconds = parseInt(config.durationSeconds, 10) || 0;
    const initialTimeLeft = parseInt(config.timeLeft, 10) || 0;
    const isResumed = !!config.isResumed;

    const storageKey = `quiz_timer_${userId}_${quizId}`;
    const timerText = document.getElementById('timer-text');
    const timerPill = document.getElementById('quiz-timer');
    const quizForm = document.getElementById('quiz-form');
    const timeLeftInput = document.getElementById('time_left');

    // 2. Timer Engine (Run only if quiz has a duration)
    if (durationSeconds > 0 && timerText) {
        let targetTimestamp = localStorage.getItem(storageKey);
        let timeLeft;
        
        if (targetTimestamp) {
            timeLeft = Math.max(0, Math.floor((parseInt(targetTimestamp, 10) - Date.now()) / 1000));
            
            // Re-sync if expired or invalid
            if (timeLeft <= 0 || timeLeft > initialTimeLeft) {
                timeLeft = initialTimeLeft;
                targetTimestamp = Date.now() + (timeLeft * 1000);
                localStorage.setItem(storageKey, targetTimestamp);
            }
        } else {
            timeLeft = initialTimeLeft;
            targetTimestamp = Date.now() + (timeLeft * 1000);
            localStorage.setItem(storageKey, targetTimestamp);
        }

        const updateTimerDisplay = () => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerText.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeftInput) {
                timeLeftInput.value = timeLeft;
            }

            if (timeLeft <= 60 && timeLeft > 0) {
                timerPill.classList.add('warning');
            }
        };

        updateTimerDisplay();

        const timerInterval = setInterval(() => {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerText.textContent = "00:00";
                localStorage.removeItem(storageKey);

                // Force layout lock overlay on expiration
                const overlay = document.createElement('div');
                overlay.style.position = 'fixed';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100vw';
                overlay.style.height = '100vh';
                overlay.style.backgroundColor = 'rgba(15, 23, 42, 0.9)';
                overlay.style.backdropFilter = 'blur(10px)';
                overlay.style.color = '#ffffff';
                overlay.style.display = 'flex';
                overlay.style.flexDirection = 'column';
                overlay.style.justifyContent = 'center';
                overlay.style.alignItems = 'center';
                overlay.style.zIndex = '9999';
                overlay.style.fontFamily = "'Plus Jakarta Sans', sans-serif";

                overlay.innerHTML = `
                    <div style="background: #ef4444; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; animation: pulse 1.5s infinite;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: white;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <h2 style="font-size:2.5rem; font-weight:800; margin-bottom:0.5rem; color:#f8fafc;">Waktu Habis!</h2>
                    <p style="font-size:1.1rem; color:#94a3b8; font-weight:500;">Sistem sedang mengumpulkan kuis Anda secara otomatis...</p>
                `;
                document.body.appendChild(overlay);

                // Bypass required fields validation to force submit
                quizForm.querySelectorAll('input[required]').forEach(input => input.removeAttribute('required'));

                window.isQuizSubmitting = true;
                setTimeout(() => {
                    quizForm.submit();
                }, 2000);
            } else {
                updateTimerDisplay();
            }
        }, 1000);

        if (quizForm) {
            quizForm.addEventListener('submit', () => {
                localStorage.removeItem(storageKey);
            });
        }

        window.addEventListener('pagehide', () => {
            if (window.isQuizSubmitting) {
                localStorage.removeItem(storageKey);
            }
        });
    }

    // 3. Questions Shuffle Engine (Shuffle on refresh/load or resume state)
    const shuffle = (array) => {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    };

    const isReload = performance.getEntriesByType('navigation')[0]?.type === 'reload';

    if (quizForm && (isReload || isResumed)) {
        const blocks = Array.from(quizForm.querySelectorAll('.question-block'));
        shuffle(blocks);
        
        blocks.forEach((block, index) => {
            const navContainer = quizForm.querySelector('.quiz-nav-container');
            quizForm.insertBefore(block, navContainer);
            
            const indexHeader = block.querySelector('div');
            if (indexHeader) {
                indexHeader.textContent = `Pertanyaan ${index + 1} dari ${blocks.length}`;
            }
            
            block.classList.remove('active');
            if (index === 0) {
                block.classList.add('active');
            }
        });
    }

    // 4. Options Selection Styling & Interaction
    const optionLabels = document.querySelectorAll('.option-label');
    optionLabels.forEach(label => {
        label.addEventListener('click', () => {
            const block = label.closest('.question-block');
            block.querySelectorAll('.option-label').forEach(el => el.classList.remove('selected'));
            label.classList.add('selected');
        });
    });

    // 5. Navigation & Pagination slider
    const blocks = document.querySelectorAll('.question-block');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnSubmit = document.getElementById('btn-submit-quiz');
    let currentSlide = 0;
    const totalSlides = blocks.length;

    const updateSlider = () => {
        blocks.forEach((block, index) => {
            if (index === currentSlide) {
                block.classList.add('active');
            } else {
                block.classList.remove('active');
            }
        });

        if (btnPrev) btnPrev.disabled = currentSlide === 0;
        if (btnNext) btnNext.disabled = currentSlide === totalSlides - 1;

        document.querySelectorAll('.page-number').forEach((btn, index) => {
            if (index === currentSlide) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    };

    const checkSubmitReadiness = () => {
        const answeredCount = document.querySelectorAll('.options-list input[type="radio"]:checked').length;
        if (btnSubmit) {
            if (answeredCount === totalSlides) {
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                btnSubmit.style.cursor = 'pointer';
            } else {
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = '0.5';
                btnSubmit.style.cursor = 'not-allowed';
            }
        }

        blocks.forEach((block, index) => {
            const radioChecked = block.querySelector('.options-list input[type="radio"]:checked');
            const pageBtn = document.querySelector(`.page-number[data-slide="${index}"]`);
            if (pageBtn) {
                if (radioChecked) {
                    pageBtn.classList.add('answered');
                } else {
                    pageBtn.classList.remove('answered');
                }
            }
        });
    };

    const radioInputs = document.querySelectorAll('.options-list input[type="radio"]');
    radioInputs.forEach(radio => {
        radio.addEventListener('change', checkSubmitReadiness);
    });

    checkSubmitReadiness();
    updateSlider();

    document.querySelectorAll('.page-number').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = parseInt(btn.getAttribute('data-slide'), 10);
            currentSlide = target;
            updateSlider();
        });
    });

    if (btnNext) {
        btnNext.addEventListener('click', () => {
            if (currentSlide < totalSlides - 1) {
                currentSlide++;
                updateSlider();
            }
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            if (currentSlide > 0) {
                currentSlide--;
                updateSlider();
            }
        });
    }

    if (quizForm) {
        quizForm.addEventListener('submit', () => {
            window.isQuizSubmitting = true;
        });
    }
});
