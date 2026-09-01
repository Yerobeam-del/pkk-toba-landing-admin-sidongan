/* ============================================================
 * Email Typo Checker
 * Detects common email domain typos and suggests corrections.
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
var EmailTypoChecker = (function () {
    // Common domain typos → correct domain
    var typoMap = {
        // Gmail typos
        'gmai.com': 'gmail.com',
        'gmal.com': 'gmail.com',
        'gmail.co': 'gmail.com',
        'gmail.con': 'gmail.com',
        'gmial.com': 'gmail.com',
        'gnail.com': 'gmail.com',
        'gmaill.com': 'gmail.com',
        'gamil.com': 'gmail.com',
        'gemail.com': 'gmail.com',
        'gma.com': 'gmail.com',
        'ggmail.com': 'gmail.com',
        'gmsil.com': 'gmail.com',
        'gmail.cm': 'gmail.com',
        'gail.com': 'gmail.com',

        // Yahoo typos
        'yahooo.com': 'yahoo.com',
        'yaho.com': 'yahoo.com',
        'yahoo.co': 'yahoo.com',
        'yahoo.con': 'yahoo.com',
        'yhoo.com': 'yahoo.com',
        'yaahoo.com': 'yahoo.com',
        'yahooo.co.id': 'yahoo.co.id',
        'yaho.co.id': 'yahoo.co.id',

        // Hotmail typos
        'hotmal.com': 'hotmail.com',
        'hotmial.com': 'hotmail.com',
        'hotmail.co': 'hotmail.com',
        'hotmail.con': 'hotmail.com',
        'hotmil.com': 'hotmail.com',
        'hotmmail.com': 'hotmail.com',
        'htmail.com': 'hotmail.com',

        // Outlook typos
        'outlook.con': 'outlook.com',
        'outlok.com': 'outlook.com',
        'outloo.com': 'outlook.com',
        'outlook.co': 'outlook.com',
        'outlok.co': 'outlook.com',

        // iCloud typos
        'icloud.con': 'icloud.com',
        'iclod.com': 'icloud.com',
        'icloud.co': 'icloud.com',

        // Other common providers
        'protonmail.con': 'protonmail.com',
        'protonmail.co': 'protonmail.com',
        'aol.con': 'aol.com',
        'live.con': 'live.com',
        'msn.con': 'msn.com',
        'zoho.con': 'zoho.com',
        'mail.con': 'mail.com',
        'yandex.con': 'yandex.com',

        // Indonesian providers
        'telkomsel.con': 'telkomsel.net',
        'Indosat.con': 'indosat.net',

        // TLD typos
        'gmail.cmo': 'gmail.com',
        'gmail.ocm': 'gmail.com',
        'yahoo.cmo': 'yahoo.com',
        'yahoo.ocm': 'yahoo.com',
    };

    /**
     * Check email for typo and return suggestion (or null if OK).
     * @param {string} email
     * @returns {{ domain: string, suggested: string } | null}
     */
    function check(email) {
        if (!email || email.indexOf('@') === -1) return null;

        var parts = email.split('@');
        if (parts.length !== 2) return null;

        var domain = parts[1].toLowerCase().trim();
        if (!domain) return null;

        // Exact match in typo map
        if (typoMap[domain]) {
            return { domain: domain, suggested: parts[1].split('@')[0] + '@' + typoMap[domain] };
        }

        // Fuzzy: Levenshtein distance 1 from known domains
        var knownDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'protonmail.com', 'aol.com', 'live.com'];
        for (var i = 0; i < knownDomains.length; i++) {
            if (domain !== knownDomains[i] && levenshtein(domain, knownDomains[i]) === 1) {
                return { domain: domain, suggested: parts[1].split('@')[0] + '@' + knownDomains[i] };
            }
        }

        return null;
    }

    /**
     * Simple Levenshtein distance (for single-char typo detection).
     */
    function levenshtein(a, b) {
        if (a.length === 0) return b.length;
        if (b.length === 0) return a.length;
        var matrix = [];
        for (var i = 0; i <= b.length; i++) matrix[i] = [i];
        for (var j = 0; j <= a.length; j++) matrix[0][j] = j;
        for (var i = 1; i <= b.length; i++) {
            for (var j = 1; j <= a.length; j++) {
                var cost = b.charAt(i - 1) === a.charAt(j - 1) ? 0 : 1;
                matrix[i][j] = Math.min(
                    matrix[i - 1][j] + 1,
                    matrix[i][j - 1] + 1,
                    matrix[i - 1][j - 1] + cost
                );
            }
        }
        return matrix[b.length][a.length];
    }

    /**
     * Attach to an email input: shows suggestion banner when typo detected.
     * @param {string} inputSelector - CSS selector for email input
     * @param {string} bannerSelector - CSS selector for suggestion banner (created if null)
     */
    function attach(inputSelector, bannerSelector) {
        var input = document.querySelector(inputSelector);
        if (!input) return;

        // Create suggestion banner if not provided
        var banner;
        if (bannerSelector) {
            banner = document.querySelector(bannerSelector);
        }
        if (!banner) {
            banner = document.createElement('div');
            banner.className = 'email-typo-banner';
            banner.style.cssText = 'display:none;margin-top:0.4rem;padding:0.5rem 0.75rem;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;font-size:0.8rem;color:#92400e;align-items:center;gap:0.5rem;';
            banner.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
            input.parentNode.insertBefore(banner, input.nextSibling);
        }

        var debounceTimer;

        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                var result = check(input.value);
                if (result) {
                    var suggestedEmail = result.suggested;
                    banner.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
                        '<span>Mungkin yang dimaksud <strong>' + suggestedEmail + '</strong>?</span>' +
                        '<button type="button" onclick="this.parentNode.style.display=\'none\';document.querySelector(\'' + inputSelector + '\').value=\'' + suggestedEmail + '\'" style="background:#f59e0b;color:white;border:none;padding:0.2rem 0.5rem;border-radius:4px;cursor:pointer;font-size:0.75rem;font-weight:600;white-space:nowrap;margin-left:auto">Gunakan</button>';
                    banner.style.display = 'flex';
                } else {
                    banner.style.display = 'none';
                }
            }, 300);
        });

        // Also check on blur
        input.addEventListener('blur', function () {
            setTimeout(function () {
                var result = check(input.value);
                if (result) {
                    // Auto-correct silently
                    input.value = result.suggested;
                    banner.style.display = 'none';
                }
            }, 200);
        });
    }

    return { check: check, attach: attach, typoMap: typoMap };
})();
