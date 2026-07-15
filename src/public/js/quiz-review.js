// --- NETQUIZ QUIZ REVIEW CONTROLLER (EXTERNAL JS) ---
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        lucide.createIcons();
    }

    // Read explanations list from exposed window object
    const config = window.NetQuizData || {};
    const questionExplanations = config.explanations || [];

    // 1. Slider Navigation Logic
    const blocks = document.querySelectorAll('.question-block');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
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

    // 2. Explanation Modal Controllers
    const modal = document.getElementById('explanation-modal');
    const textEl = document.getElementById('explanation-text');

    window.showExplanation = (index) => {
        const text = questionExplanations[index] || 'Tidak ada penjelasan untuk soal ini.';
        if (!modal || !textEl) return;

        textEl.textContent = text;
        modal.style.display = 'flex';
        
        // Trigger reflow for transition
        void modal.offsetWidth;
        modal.style.opacity = '1';
        modal.firstElementChild.style.transform = 'scale(1)';

        if (window.lucide) {
            window.lucide.createIcons();
        }
    };

    window.closeExplanation = () => {
        if (!modal) return;
        modal.style.opacity = '0';
        modal.firstElementChild.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 250);
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') window.closeExplanation();
    });

    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) window.closeExplanation();
        });
    }
});
