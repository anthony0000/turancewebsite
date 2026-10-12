(() => {
    const prompt = document.querySelector('[data-cookie-prompt]');
    if (!prompt) return;

    const storageKey = 'turance-cookie-choice-v1';
    const choices = ['accepted', 'essential'];
    let savedChoice = null;

    try {
        savedChoice = localStorage.getItem(storageKey);
    } catch (_) {
        // Private browsing may disable storage; the prompt still works for this page.
    }

    if (!choices.includes(savedChoice)) prompt.hidden = false;

    prompt.querySelectorAll('[data-cookie-choice]').forEach((button) => {
        button.addEventListener('click', () => {
            const choice = button.dataset.cookieChoice;
            if (!choices.includes(choice)) return;
            try { localStorage.setItem(storageKey, choice); } catch (_) { /* Storage unavailable. */ }
            prompt.hidden = true;
        });
    });

    document.querySelectorAll('[data-cookie-preferences]').forEach((button) => {
        button.addEventListener('click', () => {
            prompt.hidden = false;
            prompt.querySelector('[data-cookie-choice]')?.focus();
        });
    });
})();
