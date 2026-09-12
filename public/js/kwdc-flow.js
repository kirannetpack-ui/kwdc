/**
 * KWDC Ultra-Smooth SPA Navigation Engine
 * Provides instant, silky-smooth client-side navigation between portal pages.
 * Keeps the sidebar & topbar mounted without refreshing or jumping.
 */
(function () {
    'use strict';

    const progressEl = () => document.getElementById('kwdc-page-progress');
    let isNavigating = false;

    function setProgress(pct, opacity = 1) {
        const bar = progressEl();
        if (!bar) return;
        bar.style.opacity = String(opacity);
        bar.style.transform = `scaleX(${pct})`;
    }

    function startProgress() {
        setProgress(0.2, 1);
        setTimeout(() => {
            if (isNavigating) setProgress(0.7, 1);
        }, 120);
    }

    function completeProgress() {
        setProgress(1, 1);
        setTimeout(() => {
            setProgress(1, 0);
            setTimeout(() => {
                setProgress(0, 0);
            }, 200);
        }, 180);
    }

    function isPlainLeftClick(event) {
        return event.button === 0 && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey;
    }

    function isNavigableLink(link) {
        if (!link || !link.href) return false;
        const rawHref = link.getAttribute('href') || '';
        if (!rawHref || rawHref === '#' || rawHref.startsWith('#') || rawHref.startsWith('javascript:')) return false;
        if (link.hasAttribute('data-bs-toggle') || link.hasAttribute('data-toggle') || link.hasAttribute('data-no-spa')) return false;
        if (link.target && link.target !== '_self') return false;
        if (link.hasAttribute('download')) return false;
        if (link.closest('form')) return false;

        try {
            const url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) return false;
            // Ignore same page hash jumps
            if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return false;
            // Ignore logout or explicit download routes
            if (url.pathname.includes('logout') || url.pathname.endsWith('.pdf') || url.pathname.endsWith('.csv')) return false;
            return true;
        } catch (e) {
            return false;
        }
    }

    function updateActiveSidebarLink(targetPathname) {
        const sidebarLinks = document.querySelectorAll('.sidebar .sidebar-link');
        const normalizedTarget = targetPathname.replace(/\/$/, '');

        sidebarLinks.forEach(link => {
            try {
                const linkUrl = new URL(link.href, window.location.href);
                const normalizedLink = linkUrl.pathname.replace(/\/$/, '');

                const isExact = normalizedLink === normalizedTarget;
                const isSubpath = normalizedLink.length > 1 && normalizedTarget.startsWith(normalizedLink) && normalizedLink !== '/dashboard';

                if (isExact || isSubpath) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            } catch (e) {}
        });
    }

    function executeScripts(container) {
        if (!container) return;
        const scripts = Array.from(container.querySelectorAll('script'));
        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
            newScript.textContent = oldScript.textContent;
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    }

    async function navigateTo(targetUrl, pushState = true) {
        if (isNavigating) return;

        const currentUrl = new URL(window.location.href);
        const nextUrl = new URL(targetUrl, window.location.href);

        // If same page without hash difference, just scroll to top
        if (currentUrl.pathname === nextUrl.pathname && currentUrl.search === nextUrl.search && !nextUrl.hash) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        const contentEl = document.getElementById('kwdc-page-content') || document.querySelector('.page-content');
        if (!contentEl) {
            window.location.href = nextUrl.href;
            return;
        }

        isNavigating = true;
        startProgress();

        // Immediately update sidebar link highlighting so user feels zero lag
        updateActiveSidebarLink(nextUrl.pathname);

        // Close mobile drawer if open
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList.contains('mobile-open') && typeof window.toggleMobileMenu === 'function') {
            window.toggleMobileMenu();
        }

        // Soft fade out of main page content
        contentEl.style.transition = 'opacity 0.14s ease, transform 0.14s ease';
        contentEl.style.opacity = '0.35';
        contentEl.style.transform = 'translateY(4px)';

        try {
            const response = await fetch(nextUrl.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-KWDC-SPA': 'true'
                }
            });

            // If redirect occurred (e.g. session expired or redirected to login)
            if (response.redirected && response.url) {
                window.location.href = response.url;
                return;
            }

            if (!response.ok) {
                window.location.href = nextUrl.href;
                return;
            }

            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('text/html')) {
                window.location.href = nextUrl.href;
                return;
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newContentEl = doc.getElementById('kwdc-page-content') || doc.querySelector('.page-content');
            if (!newContentEl) {
                window.location.href = nextUrl.href;
                return;
            }

            // Update document title
            if (doc.title) {
                document.title = doc.title;
            }

            // Update Topbar header breadcrumb
            const newHeader = doc.getElementById('kwdc-topbar-header');
            const currentHeader = document.getElementById('kwdc-topbar-header');
            if (newHeader && currentHeader) {
                currentHeader.textContent = newHeader.textContent;
            }

            // Swap Page Content
            contentEl.innerHTML = newContentEl.innerHTML;

            // Scroll window to top smoothly
            window.scrollTo({ top: 0, behavior: 'instant' });

            // Fade in new content
            contentEl.style.opacity = '1';
            contentEl.style.transform = 'translateY(0)';

            // Push state to browser history
            if (pushState) {
                history.pushState({ url: nextUrl.href }, doc.title, nextUrl.href);
            }

            // Execute scripts embedded in the new content
            executeScripts(contentEl);

            // Re-execute scripts that were in @push('scripts') if found in response
            const pushedScripts = Array.from(doc.querySelectorAll('body > script:not([src*="bootstrap"]):not([src*="jquery"]):not([src*="kwdc-flow"]):not([src*="voice-assistant"])'));
            pushedScripts.forEach(script => {
                const s = document.createElement('script');
                Array.from(script.attributes).forEach(attr => s.setAttribute(attr.name, attr.value));
                s.textContent = script.textContent;
                document.body.appendChild(s);
            });

            // Dispatch global event for interactive components
            document.dispatchEvent(new CustomEvent('kwdc:page-loaded', { detail: { url: nextUrl.href } }));

            // If Leaflet map container exists, re-initialize if function present
            if (document.getElementById('map') && typeof window.initKwdcMap === 'function') {
                try { window.initKwdcMap(); } catch (e) {}
            }

        } catch (err) {
            console.warn('[KWDC Flow] Client-side navigation failed, falling back to full reload:', err);
            window.location.href = nextUrl.href;
        } finally {
            isNavigating = false;
            completeProgress();
            setTimeout(() => {
                contentEl.style.transition = '';
                contentEl.style.transform = '';
            }, 250);
        }
    }

    // Intercept clicks on links
    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || !isPlainLeftClick(event)) return;

        const link = event.target.closest('a[href]');
        if (!link || !isNavigableLink(link)) return;

        event.preventDefault();
        navigateTo(link.href, true);
    }, false);

    // Handle browser Back / Forward buttons
    window.addEventListener('popstate', function (event) {
        navigateTo(window.location.href, false);
    });

    // Handle forms marked for smooth submission (e.g. GET filter forms)
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!form || form.method?.toUpperCase() !== 'GET' || form.hasAttribute('data-no-spa')) return;

        try {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const actionUrl = new URL(form.action || window.location.href, window.location.href);
            actionUrl.search = params.toString();

            event.preventDefault();
            navigateTo(actionUrl.href, true);
        } catch (e) {}
    });

    // Export globally for programmatic navigation
    window.kwdcNavigate = navigateTo;

    // Initial highlight sync
    document.addEventListener('DOMContentLoaded', () => {
        updateActiveSidebarLink(window.location.pathname);
    });
})();
