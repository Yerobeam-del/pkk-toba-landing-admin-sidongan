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
     * @returns {{ type: string, domain: string, suggested: string, message: string } | null}
     *   type: 'typo' | 'work-email' | 'unknown-domain'
     */
    function check(email) {
        if (!email || email.indexOf('@') === -1) return null;

        var parts = email.split('@');
        if (parts.length !== 2) return null;

        var localPart = parts[0].toLowerCase().trim();
        var domain = parts[1].toLowerCase().trim();
        if (!domain) return null;

        // 1) Exact match in typo map
        if (typoMap[domain]) {
            return {
                type: 'typo',
                domain: domain,
                suggested: localPart + '@' + typoMap[domain],
                message: 'Kemungkinan typo domain.'
            };
        }

        // 2) Check if it's a government/work domain (pemkab, dinas, rsud, etc.)
        var workResult = checkWorkDomain(localPart, domain);
        if (workResult) return workResult;

        // 3) Fuzzy: Levenshtein distance 1 from known personal email domains
        var knownDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'protonmail.com', 'aol.com', 'live.com'];
        for (var i = 0; i < knownDomains.length; i++) {
            if (domain !== knownDomains[i] && levenshtein(domain, knownDomains[i]) === 1) {
                return {
                    type: 'typo',
                    domain: domain,
                    suggested: localPart + '@' + knownDomains[i],
                    message: 'Kemungkinan typo domain.'
                };
            }
        }

        // 4) Unknown domain — might be a work email
        var personalDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'protonmail.com', 'aol.com', 'live.com', 'mail.com', 'zoho.com', 'yandex.com'];
        if (personalDomains.indexOf(domain) === -1 && !domain.endsWith('.go.id')) {
            return {
                type: 'unknown-domain',
                domain: domain,
                suggested: null,
                message: 'Domain belum dikenal. Pastikan email sudah benar.'
            };
        }

        return null;
    }

    /**
     * Check if domain is a government/work domain that should use personal email.
     * @param {string} localPart
     * @param {string} domain
     * @returns {{ type: string, domain: string, suggested: string, message: string } | null}
     */
    function checkWorkDomain(localPart, domain) {
        // Government domain patterns
        var govPatterns = [
            'pemkab-', 'pemkot-', 'disdik-', 'diskominfo-', 'dinkes-', 'dinas-',
            'rsud-', 'rs-', 'puskesmas-', 'kec-', 'kelurahan-', 'desa-',
            'bappeda-', 'setda-', 'keuangan-', 'pendapatan-', 'pertanian-',
            'perhubungan-', 'lh-', 'perizinan-', 'ptsp-',
            'polres-', 'polresta-', 'kodim-', 'korem-',
            'kemenag-', 'kantor-', 'upt-',
        ];

        // Check if domain matches government pattern
        var isGovDomain = domain.endsWith('.go.id') || domain.endsWith('.mil.id');
        var hasGovPrefix = false;
        for (var i = 0; i < govPatterns.length; i++) {
            if (domain.indexOf(govPatterns[i]) !== -1) {
                hasGovPrefix = true;
                break;
            }
        }

        if (isGovDomain || hasGovPrefix) {
            return {
                type: 'work-email',
                domain: domain,
                suggested: null,
                message: 'Email ini menggunakan domain kantor/pemerintah. Gunakan email pribadi (Gmail/Yahoo) untuk login ke SIDONGAN.'
            };
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
                    // Choose banner style based on type
                    var bgColor, borderColor, textColor, iconColor;
                    if (result.type === 'typo') {
                        bgColor = '#fffbeb'; borderColor = '#fde68a'; textColor = '#92400e'; iconColor = '#f59e0b';
                    } else if (result.type === 'work-email') {
                        bgColor = '#eff6ff'; borderColor = '#bfdbfe'; textColor = '#1e40af'; iconColor = '#3b82f6';
                    } else {
                        bgColor = '#fef2f2'; borderColor = '#fecaca'; textColor = '#991b1b'; iconColor = '#ef4444';
                    }
                    banner.style.background = bgColor;
                    banner.style.borderColor = borderColor;
                    banner.style.color = textColor;

                    var html = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="' + iconColor + '" stroke-width="2" style="flex-shrink:0">';
                    if (result.type === 'typo') {
                        html += '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>';
                    } else if (result.type === 'work-email') {
                        html += '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 7L2 7"/>';
                    } else {
                        html += '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>';
                    }
                    html += '</svg>';
                    html += '<span>' + result.message;
                    if (result.suggested) {
                        html += ' Mungkin <strong>' + result.suggested + '</strong>?';
                    }
                    html += '</span>';
                    if (result.suggested) {
                        html += '<button type="button" onclick="this.parentNode.style.display=\'none\';document.querySelector(\'' + inputSelector + '\').value=\'' + result.suggested + '\'" style="background:' + iconColor + ';color:white;border:none;padding:0.2rem 0.5rem;border-radius:4px;cursor:pointer;font-size:0.75rem;font-weight:600;white-space:nowrap;margin-left:auto">Gunakan</button>';
                    }
                    banner.innerHTML = html;
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
