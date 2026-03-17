(function () {
    'use strict';

    var COOKIE_NAME = 'umani_consent';
    var ACCEPT_DAYS = 365;
    var REJECT_DAYS = 1;

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    function setCookie(name, value, days) {
        var d = new Date();
        d.setTime(d.getTime() + days * 86400000);
        document.cookie = name + '=' + encodeURIComponent(value) +
            ';expires=' + d.toUTCString() +
            ';path=/;SameSite=Lax';
    }

    function getSavedConsent() {
        var raw = getCookie(COOKIE_NAME);
        if (!raw) {
            // Migration from old cookie format
            var legacy = getCookie('cookie_consent');
            if (legacy === '1') {
                return acceptAll(false);
            }
            if (legacy === '0') {
                return null;
            }
            return null;
        }
        try {
            var parsed = JSON.parse(raw);
            return typeof parsed === 'object' ? parsed : null;
        } catch (e) {
            return null;
        }
    }

    function getCategories() {
        return (window.umaniCC && window.umaniCC.categories) || {};
    }

    function buildChoices(accepted) {
        var categories = getCategories();
        var choices = {};
        for (var key in categories) {
            if (categories[key].required) {
                choices[key] = true;
            } else {
                choices[key] = !!accepted;
            }
        }
        return choices;
    }

    function acceptAll(save) {
        var choices = buildChoices(true);
        if (save !== false) {
            saveConsent(choices, ACCEPT_DAYS);
        }
        return choices;
    }

    function rejectAll() {
        var choices = buildChoices(false);
        saveConsent(choices, REJECT_DAYS);
        return choices;
    }

    function saveConsent(choices, days) {
        setCookie(COOKIE_NAME, JSON.stringify(choices), days);

        if (typeof umaniUpdateConsent === 'function') {
            umaniUpdateConsent(choices);
        }

        // Remove legacy cookie
        document.cookie = 'cookie_consent=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
    }

    function hideBanner() {
        var wrap = document.getElementById('umani-cc-wrap');
        if (wrap) {
            wrap.classList.remove('umani-cc-visible');
            wrap.addEventListener('transitionend', function handler() {
                wrap.removeEventListener('transitionend', handler);
                wrap.setAttribute('hidden', '');
            });
        }
    }

    function showBanner() {
        var wrap = document.getElementById('umani-cc-wrap');
        if (wrap) {
            wrap.removeAttribute('hidden');
            // Force reflow for animation
            void wrap.offsetHeight;
            wrap.classList.add('umani-cc-visible');
        }
    }

    function showMainView() {
        var main = document.getElementById('umani-cc-main');
        var prefs = document.getElementById('umani-cc-preferences');
        if (main) main.removeAttribute('hidden');
        if (prefs) prefs.setAttribute('hidden', '');
    }

    function showPreferencesView() {
        var main = document.getElementById('umani-cc-main');
        var prefs = document.getElementById('umani-cc-preferences');
        if (main) main.setAttribute('hidden', '');
        if (prefs) prefs.removeAttribute('hidden');
    }

    function getTogglesChoices() {
        var toggles = document.querySelectorAll('.umani-cc-toggle');
        var choices = {};
        for (var i = 0; i < toggles.length; i++) {
            var toggle = toggles[i];
            var cat = toggle.getAttribute('data-category');
            if (cat) {
                choices[cat] = toggle.checked;
            }
        }
        return choices;
    }

    function hasAnyAccepted(choices) {
        for (var key in choices) {
            var categories = getCategories();
            if (categories[key] && !categories[key].required && choices[key]) {
                return true;
            }
        }
        return false;
    }

    function init() {
        var saved = getSavedConsent();

        if (saved) {
            hideBanner();
            return;
        }

        showBanner();

        var acceptBtn = document.getElementById('umani-cc-accept');
        var rejectBtn = document.getElementById('umani-cc-reject');
        var settingsBtn = document.getElementById('umani-cc-settings-toggle');
        var saveBtn = document.getElementById('umani-cc-save');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () {
                acceptAll(true);
                hideBanner();
            });
        }

        if (rejectBtn) {
            rejectBtn.addEventListener('click', function () {
                rejectAll();
                hideBanner();
            });
        }

        if (settingsBtn) {
            settingsBtn.addEventListener('click', function () {
                showPreferencesView();
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                var choices = getTogglesChoices();
                var days = hasAnyAccepted(choices) ? ACCEPT_DAYS : REJECT_DAYS;
                saveConsent(choices, days);
                hideBanner();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
