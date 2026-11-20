/**
 * Scroll reveal and count-up numbers for public pages.
 *
 * - [data-reveal] elements fade up the first time they scroll into view.
 *   Elements already on screen at load are shown immediately, and nothing
 *   is hidden until this script has run (html.motion), so content never
 *   depends on JavaScript.
 * - [data-count-to] numbers count up from zero when they become visible
 *   (data-count-decimals, data-count-grouping, data-count-bangla).
 *
 * Visitors who prefer reduced motion get the final state with no animation.
 */
const BANGLA_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
const COUNT_DURATION_MS = 1400;

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function isOnScreen(element) {
    const rect = element.getBoundingClientRect();

    return rect.top < window.innerHeight * 0.92 && rect.bottom > 0;
}

function formatCount(value, element) {
    const decimals = Number(element.dataset.countDecimals || 0);
    let text = element.dataset.countGrouping === '1'
        ? value.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals })
        : value.toFixed(decimals);

    if (element.dataset.countBangla === '1') {
        text = text.replace(/[0-9]/g, (digit) => BANGLA_DIGITS[digit]);
    }

    return text;
}

function countUp(element) {
    const target = Number(element.dataset.countTo);
    const start = performance.now();

    const step = (now) => {
        const progress = Math.min((now - start) / COUNT_DURATION_MS, 1);
        const eased = 1 - Math.pow(1 - progress, 3);

        element.textContent = formatCount(target * eased, element);

        if (progress < 1) {
            requestAnimationFrame(step);
        }
    };

    element.textContent = formatCount(0, element);
    requestAnimationFrame(step);
}

function observeOnce(elements, callback, options) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                callback(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, options);

    elements.forEach((element) => observer.observe(element));
}

export function initMotion() {
    const revealItems = [...document.querySelectorAll('[data-reveal]')];
    const counters = [...document.querySelectorAll('[data-count-to]')];

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        return;
    }

    const hidden = revealItems.filter((element) => !isOnScreen(element));
    revealItems.filter((element) => !hidden.includes(element)).forEach((element) => element.classList.add('is-revealed'));

    document.documentElement.classList.add('motion');

    observeOnce(hidden, (element) => element.classList.add('is-revealed'), { rootMargin: '0px 0px -8% 0px', threshold: 0.1 });
    observeOnce(counters, countUp, { threshold: 0.5 });
}
