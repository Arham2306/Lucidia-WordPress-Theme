/**
 * Custom Editorial - Main Frontend JavaScript
 * Pure Vanilla JavaScript for mobile drawer, search modal, sticky header, reading progress, and accessibility.
 *
 * @package Custom_Theme
 */

(function () {
    'use strict';

    // Global theme localized strings
    const i18n = window.customThemeData || {
        copiedText: 'Link copied!',
        copyErrorText: 'Unable to copy',
        openMenu: 'Open navigation menu',
        closeMenu: 'Close navigation menu'
    };

    /**
     * DOM Ready Handler
     */
    document.addEventListener('DOMContentLoaded', function () {
        initDarkMode();
        initStickyHeader();
        initMobileDrawer();
        initSearchModal();
        initReadingProgress();
        initBackToTop();
        initShareButtons();
        initAjaxPagination();
        initTableOfContents();
        initReadingMode();
        initElementorNavMenus();
        initElementorSearchWidgets();
    });

    /**
     * 0. Dark Mode Toggle
     */
    function initDarkMode() {
        const toggle = document.getElementById('dark-mode-toggle');
        const html = document.documentElement;
        const settings = window.customThemeData || {};
        
        if (!settings.darkModeEnabled) return;

        // Determine initial theme
        const stored = localStorage.getItem('custom-theme-color-scheme');
        let theme;
        
        if (stored) {
            theme = stored;
        } else if (settings.darkModeDefault === 'auto') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        } else {
            theme = settings.darkModeDefault || 'light';
        }
        
        html.setAttribute('data-theme', theme);

        // Listen for system preference changes when set to auto
        if (!stored && settings.darkModeDefault === 'auto') {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (!localStorage.getItem('custom-theme-color-scheme')) {
                    html.setAttribute('data-theme', e.matches ? 'dark' : 'light');
                }
            });
        }

        if (!toggle) return;

        toggle.addEventListener('click', function () {
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            
            // Add transition class for smooth color change
            html.classList.add('theme-transitioning');
            html.setAttribute('data-theme', next);
            localStorage.setItem('custom-theme-color-scheme', next);
            
            // Update aria label
            toggle.setAttribute('aria-label', 
                next === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'
            );
            
            // Remove transition class after animation completes
            setTimeout(function() {
                html.classList.remove('theme-transitioning');
            }, 350);
        });
    }

    /**
     * 1. Smart Sticky Header (Smart Hide on Scroll Down or Always Fixed)
     */
    function initStickyHeader() {
        const header = document.getElementById('masthead');
        if (!header || !header.classList.contains('sticky-header-enabled')) return;

        const isAlwaysFixed = header.classList.contains('sticky-mode-always-fixed');
        let lastScrollY = window.pageYOffset;
        let ticking = false;
        const scrollThreshold = 100;

        function updateHeader() {
            const currentScrollY = window.pageYOffset;

            if (currentScrollY > scrollThreshold) {
                header.classList.add('is-sticky');

                if (!isAlwaysFixed) {
                    if (currentScrollY > lastScrollY && currentScrollY > 240) {
                        // Scrolling down - hide
                        header.classList.add('header-hidden');
                    } else {
                        // Scrolling up - reveal
                        header.classList.remove('header-hidden');
                    }
                }
            } else {
                header.classList.remove('is-sticky', 'header-hidden');
            }

            lastScrollY = currentScrollY <= 0 ? 0 : currentScrollY;
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(updateHeader);
                ticking = true;
            }
        }, { passive: true });
    }

    /**
     * 2. Accessible Mobile Drawer Navigation with Focus Trapping
     */
    function initMobileDrawer() {
        const menuBtn = document.getElementById('menu-toggle-btn');
        const drawer = document.getElementById('mobile-navigation-drawer');
        const closeBtn = document.getElementById('mobile-drawer-close');
        const backdrop = document.getElementById('mobile-drawer-backdrop');

        if (!menuBtn || !drawer) return;

        function openDrawer() {
            drawer.removeAttribute('hidden');
            drawer.classList.add('is-active');
            menuBtn.setAttribute('aria-expanded', 'true');
            menuBtn.setAttribute('aria-label', i18n.closeMenu);
            document.body.classList.add('drawer-open');

            // Focus first interactive element in drawer
            const focusable = drawer.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (focusable.length) {
                setTimeout(() => focusable[0].focus(), 50);
            }
        }

        function closeDrawer() {
            drawer.classList.remove('is-active');
            menuBtn.setAttribute('aria-expanded', 'false');
            menuBtn.setAttribute('aria-label', i18n.openMenu);
            document.body.classList.remove('drawer-open');

            setTimeout(() => {
                drawer.setAttribute('hidden', '');
                menuBtn.focus();
            }, 300);
        }

        menuBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const isExpanded = menuBtn.getAttribute('aria-expanded') === 'true';
            if (isExpanded) {
                closeDrawer();
            } else {
                openDrawer();
            }
        });

        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
        if (backdrop) backdrop.addEventListener('click', closeDrawer);

        // Escape Key Listener & Focus Trapping inside Drawer
        drawer.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDrawer();
                return;
            }

            if (e.key === 'Tab') {
                const focusable = drawer.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (!focusable.length) return;

                const firstEl = focusable[0];
                const lastEl = focusable[focusable.length - 1];

                if (e.shiftKey && document.activeElement === firstEl) {
                    lastEl.focus();
                    e.preventDefault();
                } else if (!e.shiftKey && document.activeElement === lastEl) {
                    firstEl.focus();
                    e.preventDefault();
                }
            }
        });

        // Purge any inline chevrons that may have been rendered inside the mobile drawer
        drawer.querySelectorAll('.nav-dropdown-chevron').forEach(el => el.remove());

        // Setup Mobile Submenu Accordion Toggles
        const menuItemsWithChildren = drawer.querySelectorAll('.mobile-nav-list .menu-item-has-children, .mobile-nav-list .page_item_has_children');
        menuItemsWithChildren.forEach(item => {
            const subMenu = item.querySelector('.sub-menu, .children');
            if (!subMenu) return;

            // Remove any nested inline chevrons inside link text
            const inlineChevron = item.querySelector(':scope > a .nav-dropdown-chevron');
            if (inlineChevron) inlineChevron.remove();

            let toggleBtn = item.querySelector('.mobile-submenu-toggle');
            if (!toggleBtn) {
                toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'mobile-submenu-toggle';
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.setAttribute('aria-label', 'Toggle submenu');
                toggleBtn.innerHTML = '<svg class="icon icon-chevron-down" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>';

                const parentLink = item.querySelector(':scope > a');
                if (parentLink) {
                    parentLink.insertAdjacentElement('afterend', toggleBtn);
                } else {
                    item.insertBefore(toggleBtn, subMenu);
                }
            }

            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';

                if (isExpanded) {
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    toggleBtn.classList.remove('is-active');
                    subMenu.classList.remove('is-open');
                } else {
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    toggleBtn.classList.add('is-active');
                    subMenu.classList.add('is-open');
                }
            });

            const parentLink = item.querySelector(':scope > a');
            if (parentLink && (parentLink.getAttribute('href') === '#' || parentLink.getAttribute('href') === '')) {
                parentLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    toggleBtn.click();
                });
            }
        });
    }

    /**
     * 3. Smart Accessible Search Modal with Instant Live Results & Keyboard Navigation
     */
    function initSearchModal() {
        const searchBtn = document.getElementById('search-toggle-btn');
        const modal = document.getElementById('search-modal');
        const closeBtn = document.getElementById('search-modal-close');
        const backdrop = document.getElementById('search-modal-backdrop');
        const searchInput = document.getElementById('modal-search-field');
        const clearBtn = document.getElementById('modal-search-clear');
        const spinner = document.getElementById('modal-search-spinner');
        const resultsContainer = document.getElementById('search-live-results');
        const recentContainer = document.getElementById('search-recent-searches');
        const recentList = document.getElementById('recent-searches-list');
        const recentClearBtn = document.getElementById('recent-searches-clear');
        const quickCategories = document.getElementById('search-quick-categories');
        const searchForm = document.getElementById('search-form-modal');

        if (!searchBtn || !modal) return;

        const RECENT_KEY = 'custom-theme-recent-searches';
        const searchApiUrl = (window.customThemeData && window.customThemeData.searchApiUrl) || '';
        let debounceTimer = null;
        let activeController = null;
        let selectedIndex = -1;
        const queryCache = {};

        function openModal() {
            modal.removeAttribute('hidden');
            modal.classList.add('is-active');
            searchBtn.setAttribute('aria-expanded', 'true');
            document.body.classList.add('modal-open');

            renderRecentSearches();

            if (searchInput) {
                setTimeout(() => searchInput.focus(), 60);
            }
        }

        function closeModal() {
            modal.classList.remove('is-active');
            searchBtn.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('modal-open');

            if (activeController) {
                activeController.abort();
            }

            setTimeout(() => {
                modal.setAttribute('hidden', '');
                searchBtn.focus();
                resetSearchState();
            }, 250);
        }

        function resetSearchState() {
            if (searchInput) searchInput.value = '';
            if (clearBtn) clearBtn.setAttribute('hidden', '');
            if (spinner) spinner.setAttribute('hidden', '');
            if (resultsContainer) {
                resultsContainer.innerHTML = '';
                resultsContainer.setAttribute('hidden', '');
            }
            if (quickCategories) quickCategories.removeAttribute('hidden');
            selectedIndex = -1;
            renderRecentSearches();
        }

        // Recent Searches Storage
        function getRecentSearches() {
            try {
                const stored = localStorage.getItem(RECENT_KEY);
                return stored ? JSON.parse(stored) : [];
            } catch (e) {
                return [];
            }
        }

        function saveRecentSearch(query) {
            const trimmed = query.trim();
            if (!trimmed || trimmed.length < 2) return;
            try {
                let recents = getRecentSearches();
                recents = recents.filter(item => item.toLowerCase() !== trimmed.toLowerCase());
                recents.unshift(trimmed);
                if (recents.length > 6) recents = recents.slice(0, 6);
                localStorage.setItem(RECENT_KEY, JSON.stringify(recents));
            } catch (e) {}
        }

        function removeRecentSearch(query) {
            try {
                let recents = getRecentSearches();
                recents = recents.filter(item => item.toLowerCase() !== query.toLowerCase());
                localStorage.setItem(RECENT_KEY, JSON.stringify(recents));
                renderRecentSearches();
            } catch (e) {}
        }

        function renderRecentSearches() {
            if (!recentContainer || !recentList) return;
            const recents = getRecentSearches();

            if (!recents.length || (searchInput && searchInput.value.trim().length > 0)) {
                recentContainer.setAttribute('hidden', '');
                return;
            }

            recentList.innerHTML = recents.map(term => `
                <button type="button" class="recent-search-chip" data-search="${escapeHtml(term)}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>${escapeHtml(term)}</span>
                    <span class="recent-chip-remove" data-remove="${escapeHtml(term)}" title="Remove" aria-label="Remove search">&times;</span>
                </button>
            `).join('');

            recentContainer.removeAttribute('hidden');
        }

        // Highlight matching query terms
        function highlightMatch(text, query) {
            if (!query) return escapeHtml(text);
            const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp(`(${escaped})`, 'gi');
            return escapeHtml(text).replace(regex, '<mark class="search-match">$1</mark>');
        }

        function escapeHtml(string) {
            const div = document.createElement('div');
            div.textContent = string;
            return div.innerHTML;
        }

        // Live Search Execution
        function performLiveSearch(query) {
            const trimmed = query.trim();
            selectedIndex = -1;

            if (trimmed.length < 2) {
                if (spinner) spinner.setAttribute('hidden', '');
                if (resultsContainer) {
                    resultsContainer.innerHTML = '';
                    resultsContainer.setAttribute('hidden', '');
                }
                if (quickCategories) quickCategories.removeAttribute('hidden');
                renderRecentSearches();
                return;
            }

            if (recentContainer) recentContainer.setAttribute('hidden', '');
            if (quickCategories) quickCategories.setAttribute('hidden', '');
            if (spinner) spinner.removeAttribute('hidden');

            // Check in-memory cache
            if (queryCache[trimmed]) {
                renderSearchResults(queryCache[trimmed], trimmed);
                if (spinner) spinner.setAttribute('hidden', '');
                return;
            }

            if (activeController) {
                activeController.abort();
            }
            activeController = new AbortController();

            const fetchUrl = searchApiUrl + (searchApiUrl.includes('?') ? '&' : '?') + 'q=' + encodeURIComponent(trimmed);

            fetch(fetchUrl, { signal: activeController.signal })
                .then(response => {
                    if (!response.ok) throw new Error('Search request failed');
                    return response.json();
                })
                .then(data => {
                    queryCache[trimmed] = data;
                    renderSearchResults(data, trimmed);
                })
                .catch(err => {
                    if (err.name !== 'AbortError' && resultsContainer) {
                        resultsContainer.innerHTML = `<div class="search-no-results"><p>Unable to load results. Press enter to search.</p></div>`;
                        resultsContainer.removeAttribute('hidden');
                    }
                })
                .finally(() => {
                    if (spinner) spinner.setAttribute('hidden', '');
                });
        }

        function renderSearchResults(data, query) {
            if (!resultsContainer) return;
            const items = (data && data.results) || [];
            const totalCount = (data && data.total_count) || items.length;

            if (items.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="search-no-results">
                        <div class="search-no-results-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                        </div>
                        <p><strong>No articles found matching "${escapeHtml(query)}"</strong></p>
                        <p class="search-no-results-hint" style="font-size: var(--text-xs); color: var(--color-text-muted); margin-top: 0.25rem;">Try searching for a different keyword or browse popular topics.</p>
                    </div>
                `;
                resultsContainer.removeAttribute('hidden');
                return;
            }

            let html = items.map((item, index) => `
                <a href="${escapeHtml(item.url)}" class="search-result-item" data-index="${index}">
                    <div class="search-result-thumb">
                        ${item.thumbnail ? `<img src="${escapeHtml(item.thumbnail)}" alt="" loading="lazy">` : `<div class="thumb-mini-placeholder"></div>`}
                    </div>
                    <div class="search-result-content">
                        <h4 class="search-result-title">${highlightMatch(item.title, query)}</h4>
                        <div class="search-result-meta">
                            ${item.category ? `<span class="search-result-cat">${escapeHtml(item.category)}</span> &bull; ` : ''}
                            <span>${escapeHtml(item.date)}</span>
                            ${item.reading_time ? ` &bull; <span>${escapeHtml(item.reading_time)}</span>` : ''}
                        </div>
                    </div>
                    <div class="search-result-arrow">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
            `).join('');

            const viewAllUrl = (data && data.view_all) || ((window.customThemeData ? window.customThemeData.homeUrl : '/') + '?s=' + encodeURIComponent(query));
            html += `
                <a href="${escapeHtml(viewAllUrl)}" class="search-view-all-btn" id="search-view-all-link">
                    <span>View all ${totalCount} results for "<strong>${escapeHtml(query)}</strong>"</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            `;

            resultsContainer.innerHTML = html;
            resultsContainer.removeAttribute('hidden');
        }

        // Input event with debounce
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const val = this.value;
                if (clearBtn) {
                    if (val.length > 0) {
                        clearBtn.removeAttribute('hidden');
                    } else {
                        clearBtn.setAttribute('hidden', '');
                    }
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    performLiveSearch(val);
                }, 250);
            });

            // Keyboard navigation (ArrowDown, ArrowUp, Enter)
            searchInput.addEventListener('keydown', function (e) {
                const resultItems = resultsContainer ? resultsContainer.querySelectorAll('.search-result-item, .search-view-all-btn') : [];

                if (e.key === 'ArrowDown') {
                    if (!resultItems.length) return;
                    e.preventDefault();
                    selectedIndex = (selectedIndex + 1) % resultItems.length;
                    updateActiveItem(resultItems);
                } else if (e.key === 'ArrowUp') {
                    if (!resultItems.length) return;
                    e.preventDefault();
                    selectedIndex = (selectedIndex - 1 + resultItems.length) % resultItems.length;
                    updateActiveItem(resultItems);
                } else if (e.key === 'Enter') {
                    if (selectedIndex >= 0 && resultItems[selectedIndex]) {
                        e.preventDefault();
                        saveRecentSearch(searchInput.value);
                        resultItems[selectedIndex].click();
                    } else if (searchInput.value.trim().length > 0) {
                        saveRecentSearch(searchInput.value);
                    }
                }
            });
        }

        function updateActiveItem(items) {
            items.forEach((item, idx) => {
                if (idx === selectedIndex) {
                    item.classList.add('is-selected');
                    item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                } else {
                    item.classList.remove('is-selected');
                }
            });
        }

        // Clear Search Button
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                resetSearchState();
                if (searchInput) searchInput.focus();
            });
        }

        // Recent searches delegate clicks
        if (recentContainer) {
            recentContainer.addEventListener('click', function (e) {
                const removeBtn = e.target.closest('.recent-chip-remove');
                if (removeBtn) {
                    e.stopPropagation();
                    const term = removeBtn.getAttribute('data-remove');
                    if (term) removeRecentSearch(term);
                    return;
                }

                const chip = e.target.closest('.recent-search-chip');
                if (chip && searchInput) {
                    const term = chip.getAttribute('data-search');
                    if (term) {
                        searchInput.value = term;
                        if (clearBtn) clearBtn.removeAttribute('hidden');
                        performLiveSearch(term);
                        searchInput.focus();
                    }
                }
            });
        }

        if (recentClearBtn) {
            recentClearBtn.addEventListener('click', function () {
                try {
                    localStorage.removeItem(RECENT_KEY);
                } catch (e) {}
                if (recentContainer) recentContainer.setAttribute('hidden', '');
            });
        }

        // Quick Categories delegate clicks
        if (quickCategories) {
            quickCategories.addEventListener('click', function (e) {
                const link = e.target.closest('.quick-cat-link');
                if (link && searchInput) {
                    e.preventDefault();
                    const query = link.getAttribute('data-query') || link.textContent.trim();
                    searchInput.value = query;
                    if (clearBtn) clearBtn.removeAttribute('hidden');
                    performLiveSearch(query);
                    searchInput.focus();
                }
            });
        }

        // Save to recents on regular form submit
        if (searchForm) {
            searchForm.addEventListener('submit', function () {
                if (searchInput && searchInput.value.trim()) {
                    saveRecentSearch(searchInput.value);
                }
            });
        }

        // Modal Open / Close triggers
        searchBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openModal();
        });

        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (backdrop) backdrop.addEventListener('click', closeModal);

        // Global Keyboard Shortcut: Cmd/Ctrl + K to toggle search modal
        document.addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                if (modal.classList.contains('is-active')) {
                    closeModal();
                } else {
                    openModal();
                }
            }
        });

        // ESC key listener in modal
        modal.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    }

    /**
     * 4. Single Post Reading Progress Indicator
     */
    function initReadingProgress() {
        const bar = document.getElementById('reading-progress-bar');
        const article = document.querySelector('article.type-post');

        if (!bar || !article) return;

        let ticking = false;
        let totalHeight = 0;

        function cacheHeight() {
            totalHeight = article.offsetHeight - window.innerHeight;
        }
        cacheHeight();
        window.addEventListener('resize', cacheHeight);

        function updateProgress() {
            const rect = article.getBoundingClientRect();
            const scrollDistance = -rect.top;

            if (scrollDistance <= 0) {
                bar.style.transform = 'scaleX(0)';
            } else if (scrollDistance >= totalHeight) {
                bar.style.transform = 'scaleX(1)';
            } else {
                const percent = scrollDistance / totalHeight;
                bar.style.transform = 'scaleX(' + Math.min(1, Math.max(0, percent)) + ')';
            }
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(updateProgress);
                ticking = true;
            }
        }, { passive: true });
    }

    /**
     * 5. Smooth Back to Top Button
     */
    function initBackToTop() {
        const btn = document.getElementById('back-to-top');
        if (!btn) return;

        let ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    if (window.pageYOffset > 450) {
                        btn.removeAttribute('hidden');
                        btn.classList.add('is-visible');
                    } else {
                        btn.classList.remove('is-visible');
                        setTimeout(() => {
                            if (window.pageYOffset <= 450) btn.setAttribute('hidden', '');
                        }, 200);
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        btn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * 6. Social Share & Copy Link Trigger
     */
    function initShareButtons() {
        const shareContainer = document.querySelector('.social-share') || document.body;
        
        shareContainer.addEventListener('click', function(e) {
            const btn = e.target.closest('.share-btn-copy');
            if (!btn) return;
            
            e.preventDefault();
            const url = btn.getAttribute('data-url') || window.location.href;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => {
                    showCopyFeedback(btn, i18n.copiedText);
                }).catch(() => {
                    fallbackCopyText(url, btn);
                });
            } else {
                fallbackCopyText(url, btn);
            }
        });

        function fallbackCopyText(text, element) {
            const input = document.createElement('input');
            input.value = text;
            input.style.position = 'fixed';
            input.style.opacity = '0';
            document.body.appendChild(input);
            input.select();
            try {
                document.execCommand('copy');
                showCopyFeedback(element, i18n.copiedText);
            } catch (err) {
                showCopyFeedback(element, i18n.copyErrorText);
            }
            document.body.removeChild(input);
        }

        function showCopyFeedback(element, message) {
            const originalTitle = element.getAttribute('title') || '';
            const tooltip = document.createElement('span');
            tooltip.className = 'copy-tooltip';
            tooltip.textContent = message;
            element.appendChild(tooltip);

            setTimeout(() => {
                tooltip.classList.add('is-active');
            }, 10);

            setTimeout(() => {
                tooltip.classList.remove('is-active');
                setTimeout(() => tooltip.remove(), 250);
            }, 2000);
        }
    }

    /**
     * 7. AJAX Pagination (Load More Button & Infinite Scroll)
     */
    function initAjaxPagination() {
        const container = document.querySelector('.ajax-pagination-container');
        if (!container) return;

        const paginationType = container.getAttribute('data-pagination-type') || 'load_more';
        const loadMoreBtn = document.getElementById('btn-load-more');
        const trigger = container.querySelector('.infinite-scroll-trigger');
        const noMoreMsg = container.querySelector('.no-more-posts-msg');
        
        let maxPages = parseInt(container.getAttribute('data-max-pages'), 10) || 1;
        let currentPage = parseInt(container.getAttribute('data-current-page'), 10) || 1;
        let nextUrl = container.getAttribute('data-next-url');
        let isLoading = false;

        const archiveContainer = document.querySelector('.article-grid, .article-list, .article-classic');
        if (!archiveContainer) return;

        async function loadNextPosts() {
            if (isLoading || !nextUrl || currentPage >= maxPages) return;

            isLoading = true;
            if (loadMoreBtn) {
                loadMoreBtn.classList.add('is-loading');
                loadMoreBtn.setAttribute('disabled', 'disabled');
            }

            try {
                const response = await fetch(nextUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error('Network error');

                const text = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');

                const newArticles = doc.querySelectorAll('.article-grid > article, .article-list > article, .article-classic > article');
                
                if (newArticles.length > 0) {
                    newArticles.forEach((article, index) => {
                        article.classList.add('fade-in-up');
                        article.style.animationDelay = (index * 60) + 'ms';
                        archiveContainer.appendChild(article);
                    });

                    currentPage++;
                    container.setAttribute('data-current-page', currentPage);

                    // Check for next page URL in the fetched HTML
                    const nextContainer = doc.querySelector('.ajax-pagination-container');
                    const nextLink = doc.querySelector('.nav-links .next');
                    
                    if (nextContainer && nextContainer.getAttribute('data-next-url')) {
                        nextUrl = nextContainer.getAttribute('data-next-url');
                        container.setAttribute('data-next-url', nextUrl);
                    } else if (nextLink && nextLink.getAttribute('href')) {
                        nextUrl = nextLink.getAttribute('href');
                        container.setAttribute('data-next-url', nextUrl);
                    } else {
                        nextUrl = null;
                    }

                    if (currentPage >= maxPages || !nextUrl) {
                        if (loadMoreBtn) loadMoreBtn.style.display = 'none';
                        if (trigger) trigger.style.display = 'none';
                        if (noMoreMsg) noMoreMsg.style.display = 'block';
                    }
                } else {
                    if (loadMoreBtn) loadMoreBtn.style.display = 'none';
                    if (trigger) trigger.style.display = 'none';
                    if (noMoreMsg) noMoreMsg.style.display = 'block';
                }
            } catch (err) {
                console.error('Error loading posts:', err);
                if (loadMoreBtn) {
                    const btnText = loadMoreBtn.querySelector('.btn-text');
                    if (btnText) btnText.textContent = 'Retry Loading';
                }
            } finally {
                isLoading = false;
                if (loadMoreBtn) {
                    loadMoreBtn.classList.remove('is-loading');
                    loadMoreBtn.removeAttribute('disabled');
                }
            }
        }

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function (e) {
                e.preventDefault();
                loadNextPosts();
            });
        }

        if ('infinite' === paginationType && trigger && 'IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !isLoading && currentPage < maxPages) {
                        loadNextPosts();
                    }
                });
            }, { rootMargin: '300px 0px 300px 0px' });

            observer.observe(trigger);
        }
    }

    /**
     * 9. Interactive Table of Contents (Accordion & Active Heading Tracking)
     */
    function initTableOfContents() {
        const tocBox = document.querySelector('.article-toc-box');
        if (!tocBox) return;

        const header = tocBox.querySelector('.toc-header');
        const links = tocBox.querySelectorAll('.toc-link');

        // Collapsible Accordion Toggle
        if (header) {
            header.addEventListener('click', function () {
                const isExpanded = header.getAttribute('aria-expanded') === 'true';
                header.setAttribute('aria-expanded', !isExpanded);
                tocBox.classList.toggle('is-collapsed', isExpanded);
            });
        }

        // Smooth Scroll with Header Offset
        links.forEach((link) => {
            link.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (!targetId || targetId.charAt(0) !== '#') return;

                const targetEl = document.querySelector(targetId);
                if (targetEl) {
                    e.preventDefault();
                    const headerHeight = document.getElementById('masthead')?.offsetHeight || 80;
                    const targetTop = targetEl.getBoundingClientRect().top + window.pageYOffset - (headerHeight + 24);

                    window.scrollTo({
                        top: targetTop,
                        behavior: 'smooth'
                    });

                    if (history.pushState) {
                        history.pushState(null, '', targetId);
                    }
                }
            });
        });

        // Active Heading Highlighting via IntersectionObserver
        const isStickyToc = tocBox.classList.contains('is-sticky-toc');
        if (isStickyToc && 'IntersectionObserver' in window) {
            const headingIds = Array.from(links).map(l => l.getAttribute('href').replace('#', ''));
            const headings = headingIds.map(id => document.getElementById(id)).filter(Boolean);

            if (headings.length > 0) {
                const observerOptions = {
                    root: null,
                    rootMargin: '-80px 0px -65% 0px',
                    threshold: 0
                };

                const headingObserver = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            updateActiveTocLink(entry.target.id);
                        }
                    });
                }, observerOptions);

                headings.forEach(h => headingObserver.observe(h));

                function updateActiveTocLink(activeId) {
                    const tocItems = tocBox.querySelectorAll('.toc-item');
                    tocItems.forEach((item) => {
                        const link = item.querySelector('.toc-link');
                        const href = link?.getAttribute('href');
                        if (href === '#' + activeId) {
                            item.classList.add('is-active');
                        } else {
                            item.classList.remove('is-active');
                        }
                    });
                }
            }
        }
    }

    /**
     * 10. Distraction-Free Reading Mode
     */
    function initReadingMode() {
        const toggleBtn = document.getElementById('reading-mode-toggle');
        const readerBar = document.getElementById('reading-mode-bar');
        const exitBtn = document.getElementById('reader-exit-btn');
        const body = document.body;

        if (!toggleBtn && !readerBar) return;

        // Restore saved reader preferences
        const savedSettings = JSON.parse(localStorage.getItem('custom_theme_reader_settings') || '{}');
        let currentScale = savedSettings.scale || 100;
        let currentTheme = savedSettings.theme || 'light';
        let currentFont = savedSettings.font || 'serif';

        function applyReaderSettings() {
            body.setAttribute('data-reader-theme', currentTheme);
            body.setAttribute('data-reader-font', currentFont);
            body.style.setProperty('--reader-font-scale', (currentScale / 100).toString());

            // Update toolbar buttons
            document.querySelectorAll('.reader-theme-btn').forEach(btn => {
                btn.classList.toggle('is-active', btn.getAttribute('data-reader-theme') === currentTheme);
            });
            document.querySelectorAll('.reader-font-btn').forEach(btn => {
                btn.classList.toggle('is-active', btn.getAttribute('data-reader-font') === currentFont);
            });
            const sizeResetBtn = document.getElementById('reader-font-reset');
            if (sizeResetBtn) sizeResetBtn.textContent = currentScale + '%';

            localStorage.setItem('custom_theme_reader_settings', JSON.stringify({
                scale: currentScale,
                theme: currentTheme,
                font: currentFont
            }));
        }

        function enterReaderMode() {
            body.classList.add('reading-mode-active');
            if (readerBar) readerBar.removeAttribute('hidden');
            applyReaderSettings();
        }

        function exitReaderMode() {
            body.classList.remove('reading-mode-active');
            if (readerBar) readerBar.setAttribute('hidden', '');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                if (body.classList.contains('reading-mode-active')) {
                    exitReaderMode();
                } else {
                    enterReaderMode();
                }
            });
        }

        if (exitBtn) {
            exitBtn.addEventListener('click', function (e) {
                e.preventDefault();
                exitReaderMode();
            });
        }

        // Keyboard ESC to exit
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && body.classList.contains('reading-mode-active')) {
                exitReaderMode();
            }
        });

        // Reader Toolbar Interactions
        if (readerBar) {
            readerBar.addEventListener('click', function (e) {
                const themeBtn = e.target.closest('.reader-theme-btn');
                if (themeBtn) {
                    currentTheme = themeBtn.getAttribute('data-reader-theme');
                    applyReaderSettings();
                    return;
                }

                const fontBtn = e.target.closest('.reader-font-btn');
                if (fontBtn) {
                    currentFont = fontBtn.getAttribute('data-reader-font');
                    applyReaderSettings();
                    return;
                }

                if (e.target.closest('#reader-font-increase')) {
                    if (currentScale < 160) {
                        currentScale += 10;
                        applyReaderSettings();
                    }
                    return;
                }

                if (e.target.closest('#reader-font-decrease')) {
                    if (currentScale > 80) {
                        currentScale -= 10;
                        applyReaderSettings();
                    }
                    return;
                }

                if (e.target.closest('#reader-font-reset')) {
                    currentScale = 100;
                    applyReaderSettings();
                    return;
                }
            });
        }
    }

    /**
     * 10. Elementor Editorial Navigation Menu Widget Handler
     */
    function initElementorNavMenus() {
        const navWidgets = document.querySelectorAll('.editorial-nav-menu-widget');
        if (!navWidgets.length) return;

        navWidgets.forEach(widget => {
            if (widget.dataset.navInitialized) return;
            widget.dataset.navInitialized = 'true';

            const toggleBtn = widget.querySelector('.editorial-menu-toggle');
            const container = widget.querySelector('.editorial-nav-container');
            const iconOpen = widget.querySelector('.toggle-icon-open');
            const iconClose = widget.querySelector('.toggle-icon-close');

            if (toggleBtn && container) {
                toggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
                    const willOpen = !isExpanded;

                    toggleBtn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                    container.classList.toggle('is-open', willOpen);

                    if (iconOpen && iconClose) {
                        iconOpen.style.display = willOpen ? 'none' : 'inline-flex';
                        iconClose.style.display = willOpen ? 'inline-flex' : 'none';
                    }
                });
            }

            // Mobile Accordion for Submenu items inside this widget
            const parentItems = widget.querySelectorAll('.editorial-nav-list .menu-item-has-children, .editorial-nav-list .page_item_has_children');
            parentItems.forEach(item => {
                const subMenu = item.querySelector('.sub-menu, .children');
                if (!subMenu) return;

                const parentLink = item.querySelector(':scope > a');
                if (parentLink) {
                    parentLink.addEventListener('click', function (e) {
                        const isMobileView = window.innerWidth <= 1024;
                        if (isMobileView && (parentLink.getAttribute('href') === '#' || parentLink.getAttribute('href') === '')) {
                            e.preventDefault();
                            subMenu.classList.toggle('is-open');
                        }
                    });
                }
            });
        });
    }

    /**
     * 11. Elementor Editorial Smart Search Widget Handler
     */
    function initElementorSearchWidgets() {
        const searchWidgets = document.querySelectorAll('.editorial-search-widget');
        if (!searchWidgets.length) return;

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        const restUrl = (window.customThemeData && window.customThemeData.restUrl) || '/wp-json/';

        searchWidgets.forEach(widget => {
            if (widget.dataset.searchInitialized) return;
            widget.dataset.searchInitialized = 'true';

            // Modal Trigger Button Handler
            const modalBtn = widget.querySelector('.search-modal-trigger-btn');
            if (modalBtn) {
                modalBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const globalSearchModal = document.getElementById('search-modal');
                    const globalSearchToggle = document.getElementById('search-toggle-btn') || document.getElementById('header-search-toggle');
                    if (globalSearchToggle) {
                        globalSearchToggle.click();
                    } else if (globalSearchModal) {
                        globalSearchModal.removeAttribute('hidden');
                        globalSearchModal.classList.add('is-active');
                        document.body.classList.add('modal-open');
                        const modalInput = globalSearchModal.querySelector('#modal-search-field, .search-field-modal, .search-input');
                        if (modalInput) setTimeout(() => modalInput.focus(), 60);
                    }
                });
            }

            // Inline Live Search Handler
            const form = widget.querySelector('.editorial-search-form');
            if (!form || form.getAttribute('data-live-search') !== 'true') return;

            const input = form.querySelector('.editorial-search-input');
            const spinner = form.querySelector('.editorial-search-spinner');
            const clearBtn = form.querySelector('.editorial-search-clear-btn');
            const resultsBox = form.querySelector('.editorial-live-results');
            const resultsList = form.querySelector('.live-results-list');
            const footerBox = form.querySelector('.live-results-footer');
            const viewAllLink = form.querySelector('.view-all-results-link');

            if (!input || !resultsBox || !resultsList) return;

            const limit = parseInt(form.getAttribute('data-limit'), 10) || 5;
            const minChars = parseInt(form.getAttribute('data-min-length'), 10) || 2;
            const showThumb = form.getAttribute('data-show-thumb') === '1';
            const showCat = form.getAttribute('data-show-cat') === '1';
            const showDate = form.getAttribute('data-show-date') === '1';
            const noResultsMsg = form.getAttribute('data-no-results') || 'No stories found.';

            let debounceTimer = null;
            let currentSelectedIndex = -1;

            function doSearch(query) {
                query = query.trim();
                if (query.length < minChars) {
                    resultsBox.style.display = 'none';
                    if (clearBtn) clearBtn.style.display = query.length > 0 ? 'inline-flex' : 'none';
                    return;
                }

                if (spinner) spinner.style.display = 'inline-flex';
                if (clearBtn) clearBtn.style.display = 'none';

                const endpoint = `${restUrl}custom-theme/v1/search?q=${encodeURIComponent(query)}&limit=${limit}`;

                fetch(endpoint)
                    .then(response => {
                        if (!response.ok) throw new Error('Search failed');
                        return response.json();
                    })
                    .then(data => {
                        if (spinner) spinner.style.display = 'none';
                        if (clearBtn) clearBtn.style.display = 'inline-flex';

                        currentSelectedIndex = -1;
                        resultsList.innerHTML = '';

                        const results = data.results || [];
                        if (results.length === 0) {
                            resultsList.innerHTML = `<div class="editorial-search-no-results">${escapeHtml(noResultsMsg)}</div>`;
                            if (footerBox) footerBox.style.display = 'none';
                        } else {
                            results.forEach(post => {
                                const item = document.createElement('a');
                                item.href = post.url;
                                item.className = 'editorial-search-result-item';

                                let thumbHtml = '';
                                if (showThumb && post.thumbnail) {
                                    thumbHtml = `<div class="result-item-thumb"><img src="${escapeHtml(post.thumbnail)}" alt="${escapeHtml(post.title)}" loading="lazy"></div>`;
                                }

                                let catHtml = '';
                                if (showCat && post.category) {
                                    catHtml = `<span class="result-item-category">${escapeHtml(post.category)}</span>`;
                                }

                                let dateHtml = '';
                                if (showDate && post.date) {
                                    dateHtml = `<span class="result-item-date">${escapeHtml(post.date)}</span>`;
                                }

                                let metaHtml = '';
                                if (catHtml || dateHtml) {
                                    metaHtml = `<div class="result-item-meta">${catHtml}${dateHtml}</div>`;
                                }

                                item.innerHTML = `
                                    ${thumbHtml}
                                    <div class="result-item-info">
                                        <h4 class="result-item-title">${escapeHtml(post.title)}</h4>
                                        ${metaHtml}
                                    </div>
                                `;

                                resultsList.appendChild(item);
                            });

                            if (footerBox && viewAllLink) {
                                viewAllLink.href = `${form.getAttribute('action') || '/'}?s=${encodeURIComponent(query)}`;
                                footerBox.style.display = 'block';
                            }
                        }

                        resultsBox.style.display = 'block';
                    })
                    .catch(() => {
                        if (spinner) spinner.style.display = 'none';
                        if (clearBtn) clearBtn.style.display = 'inline-flex';
                    });
            }

            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const query = this.value;
                if (clearBtn) clearBtn.style.display = query.length > 0 ? 'inline-flex' : 'none';
                debounceTimer = setTimeout(() => doSearch(query), 250);
            });

            input.addEventListener('focus', function () {
                if (this.value.trim().length >= minChars && resultsList.children.length > 0) {
                    resultsBox.style.display = 'block';
                }
            });

            // Keyboard Navigation inside search results (Up/Down/Enter/Escape)
            input.addEventListener('keydown', function (e) {
                const items = resultsList.querySelectorAll('.editorial-search-result-item');
                if (!items.length || resultsBox.style.display === 'none') {
                    if (e.key === 'Escape') {
                        resultsBox.style.display = 'none';
                    }
                    return;
                }

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentSelectedIndex = (currentSelectedIndex + 1) % items.length;
                    highlightItem(items, currentSelectedIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentSelectedIndex = (currentSelectedIndex - 1 + items.length) % items.length;
                    highlightItem(items, currentSelectedIndex);
                } else if (e.key === 'Enter') {
                    if (currentSelectedIndex >= 0 && items[currentSelectedIndex]) {
                        e.preventDefault();
                        items[currentSelectedIndex].click();
                    }
                } else if (e.key === 'Escape') {
                    resultsBox.style.display = 'none';
                }
            });

            function highlightItem(items, idx) {
                items.forEach((item, i) => {
                    item.classList.toggle('is-selected', i === idx);
                    if (i === idx) {
                        item.scrollIntoView({ block: 'nearest' });
                    }
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    input.value = '';
                    resultsBox.style.display = 'none';
                    clearBtn.style.display = 'none';
                    input.focus();
                });
            }

            // Close on click outside
            document.addEventListener('click', function (e) {
                if (!widget.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        });
    }

    // Bind Elementor frontend hook if active
    if (window.elementorFrontend) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/custom_theme_nav_menu.default', function () {
            initElementorNavMenus();
        });
        window.elementorFrontend.hooks.addAction('frontend/element_ready/custom_theme_search_bar.default', function () {
            initElementorSearchWidgets();
        });
    } else {
        window.addEventListener('elementor/frontend/init', function () {
            if (window.elementorFrontend && window.elementorFrontend.hooks) {
                window.elementorFrontend.hooks.addAction('frontend/element_ready/custom_theme_nav_menu.default', function () {
                    initElementorNavMenus();
                });
                window.elementorFrontend.hooks.addAction('frontend/element_ready/custom_theme_search_bar.default', function () {
                    initElementorSearchWidgets();
                });
            }
        });
    }

})();


