(function () {
    const startedAt = Date.now();
    const prefetched = new Set();
    const progress = () => document.getElementById('kwdc-page-progress');

    function startLoading() {
        document.documentElement.classList.add('kwdc-page-loading');
    }

    function stopLoading() {
        const bar = progress();
        if (bar) {
            bar.style.transform = 'scaleX(1)';
        }

        window.setTimeout(() => {
            document.documentElement.classList.remove('kwdc-page-loading');
            if (bar) {
                bar.style.transform = '';
            }
        }, 180);
    }

    function isPlainLeftClick(event) {
        return event.button === 0 && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey;
    }

    function isSmoothLink(link) {
        if (!link || !link.href) return false;
        if (link.target && link.target !== '_self') return false;
        if (link.hasAttribute('download')) return false;
        if (link.dataset.noSmooth === 'true') return false;

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin) return false;
        if (url.pathname === window.location.pathname && url.hash) return false;
        if (['mailto:', 'tel:', 'javascript:'].includes(url.protocol)) return false;

        return true;
    }

    function prefetch(link) {
        if (!isSmoothLink(link)) return;

        const url = new URL(link.href, window.location.href);
        url.hash = '';
        const key = url.toString();
        if (prefetched.has(key)) return;
        prefetched.add(key);

        const tag = document.createElement('link');
        tag.rel = 'prefetch';
        tag.href = key;
        tag.as = 'document';
        document.head.appendChild(tag);
    }

    function markSubmitting(form) {
        if (!form || form.dataset.submitting === 'true') return;

        form.dataset.submitting = 'true';
        const submitter = form.querySelector('[type="submit"], button:not([type]), button[type="button"].submit');
        if (submitter) {
            submitter.dataset.originalHtml = submitter.innerHTML;
            submitter.classList.add('kwdc-submit-busy');
            submitter.setAttribute('aria-busy', 'true');
            if (!submitter.dataset.keepLabel) {
                submitter.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Working...';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        stopLoading();

        document.addEventListener('pointerenter', function (event) {
            const link = event.target.closest?.('a[href]');
            prefetch(link);
        }, true);

        document.addEventListener('touchstart', function (event) {
            const link = event.target.closest?.('a[href]');
            prefetch(link);
        }, { passive: true, capture: true });

        document.addEventListener('click', function (event) {
            const link = event.target.closest?.('a[href]');
            if (!isPlainLeftClick(event) || !isSmoothLink(link)) return;

            startLoading();
        });

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!form || form.dataset.noSmooth === 'true') return;

            markSubmitting(form);
            startLoading();
        }, true);

        document.querySelectorAll('[autofocus]').forEach((element) => {
            window.setTimeout(() => element.focus(), 50);
        });
    });

    window.addEventListener('pageshow', stopLoading);
    window.addEventListener('beforeunload', function () {
        if (Date.now() - startedAt > 300) {
            startLoading();
        }
    });
})();
