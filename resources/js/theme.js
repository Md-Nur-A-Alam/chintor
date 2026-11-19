/**
 * Theme preference: "light", "dark" or "system".
 *
 * The inline script in the layout <head> applies the stored preference
 * before first paint (no flash). This module only handles changes made
 * after the page has loaded.
 */
const STORAGE_KEY = 'chintor-theme';
const PREFERENCES = ['light', 'dark', 'system'];
const darkQuery = window.matchMedia('(prefers-color-scheme: dark)');

function readPreference() {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);

        return PREFERENCES.includes(stored) ? stored : 'system';
    } catch {
        return 'system';
    }
}

function applyPreference(preference) {
    const resolved = preference === 'system' ? (darkQuery.matches ? 'dark' : 'light') : preference;
    const root = document.documentElement;

    root.dataset.theme = resolved;
    root.dataset.themePreference = preference;

    document
        .querySelector('meta[name="theme-color"]')
        ?.setAttribute('content', resolved === 'dark' ? '#021c1c' : '#fffbf6');
}

export function themeSwitcher() {
    return {
        preference: readPreference(),

        init() {
            darkQuery.addEventListener('change', () => {
                if (this.preference === 'system') {
                    applyPreference('system');
                }
            });
        },

        choose(preference) {
            this.preference = preference;

            try {
                localStorage.setItem(STORAGE_KEY, preference);
            } catch {
                // Storage can be blocked (private mode); the choice still applies to this page.
            }

            applyPreference(preference);
        },

        cycle() {
            const next = PREFERENCES[(PREFERENCES.indexOf(this.preference) + 1) % PREFERENCES.length];

            this.choose(next);
        },
    };
}
