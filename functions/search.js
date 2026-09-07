/* GLOREFY live search - powers desktop + mobile search boxes.
   Markup contract:
   - wrapper:      [data-glor-search] + data-glor-domain="<DOMAIN>"
   - text input:   [data-glor-q]
   - search button:[data-glor-btn]   (optional)
   - results panel:[data-glor-panel] (hidden by default)
*/
(function () {
    'use strict';

    var MIN_CHARS = 2;
    var DEBOUNCE_MS = 260;

    function escapeHtml(str) {
        return String(str == null ? '' : str).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function highlight(text, q) {
        var str = String(text == null ? '' : text);
        var safe = String(q || '').toLowerCase();
        if (!safe) return escapeHtml(str);
        var idx = str.toLowerCase().indexOf(safe);
        if (idx === -1) return escapeHtml(str);
        return (
            escapeHtml(str.slice(0, idx)) +
            '<mark class="bg-transparent glor-text font-medium">' +
            escapeHtml(str.slice(idx, idx + safe.length)) +
            '</mark>' +
            escapeHtml(str.slice(idx + safe.length))
        );
    }

    function money(n) {
        if (n === null || n === undefined || isNaN(n)) return '';
        return '\u20A6' + Number(n).toLocaleString();
    }

    function initSearch(root) {
        if (root.getAttribute('data-glor-init')) return;
        root.setAttribute('data-glor-init', '1');

        var input = root.querySelector('[data-glor-q]');
        var button = root.querySelector('[data-glor-btn]');
        var panel = root.querySelector('[data-glor-panel]');
        var domain = root.getAttribute('data-glor-domain') || '';

        if (!input || !panel) return;

        var timer = null;
        var controller = null;
        var items = [];
        var selIndex = -1;
        var lastTerm = '';

        function openPanel() {
            panel.classList.remove('hidden');
            panel.classList.add('block');
        }

        function closePanel() {
            panel.classList.add('hidden');
            panel.classList.remove('block');
            items = [];
            selIndex = -1;
        }

        function setLoading() {
            panel.innerHTML =
                '<div class="flex items-center gap-2 p-4" aria-live="polite">' +
                '<span class="inline-block w-4 h-4 rounded-full border-2 glor-border border-t-transparent animate-spin"></span>' +
                '<span class="text-[13px] text-[#777777] font-Onest font-regular">Searching\u2026</span>' +
                '</div>';
            openPanel();
        }

        function render(term, data) {
            var hasProducts = data.products && data.products.length > 0;
            var hasCategories = data.categories && data.categories.length > 0;
            var hasBrands = data.brands && data.brands.length > 0;
            var hasAny = hasProducts || hasCategories || hasBrands;

            var html = '';

            if (hasProducts) {
                html += '<div class="mb-4">' +
                    '<h4 class="glor-text font-medium text-[14px] mb-2 font-Onest">Products</h4>' +
                    '<div class="flex flex-col glor-product-list"></div></div>';
            }
            if (hasCategories) {
                html += '<div class="mb-3">' +
                    '<h4 class="glor-text font-medium text-[14px] mb-1 font-Onest">Categories</h4>' +
                    '<div class="flex flex-col glor-cat-list"></div></div>';
            }
            if (hasBrands) {
                html += '<div class="mb-3">' +
                    '<h4 class="glor-text font-medium text-[14px] mb-1 font-Onest">Brands</h4>' +
                    '<div class="flex flex-col glor-brand-list"></div></div>';
            }
            if (!hasAny) {
                html += '<div class="text-center py-3">' +
                    '<p class="text-[14px] text-[#777777] font-Onest font-regular">No results for &ldquo;' + escapeHtml(term) + '&rdquo;</p></div>';
            }
            html += '<div class="border-t-[1px] border-[#F0EDF2] pt-2">' +
                '<a href="' + escapeHtml(domain + '/products/index.php?search=' + encodeURIComponent(term)) + '" class="glor-viewall flex items-center justify-between py-1 text-[13px] font-Onest font-medium text-[#262626] hover:glor-text cursor-pointer">' +
                'See all results for &ldquo;' + escapeHtml(term) + '&rdquo;' +
                '<i class="fa-solid fa-arrow-right text-[11px] leading-none"></i>' +
                '</a></div>';

            panel.innerHTML = '<div class="p-3">' + html + '</div>';
            openPanel();

            items = [];
            selIndex = -1;

            if (hasProducts) {
                var prodList = panel.querySelector('.glor-product-list');
                if (prodList) {
                    data.products.forEach(function (p) {
                        var name = p.product_name || 'Product';
                        var img = p.image ? domain + '/assets/products/' + p.image : domain + '/assets/products/default.svg';
                        var url = p.slug ? domain + '/products/show.php?slug=' + encodeURIComponent(p.slug) : domain + '/products/show.php?id=' + p.product_id;
                        var meta = [];
                        if (p.category_title) meta.push(p.category_title);
                        if (p.brand_title) meta.push(p.brand_title);
                        var price = p.discount_price ? money(p.discount_price) : money(p.price);

                        var el = document.createElement('a');
                        el.href = url;
                        el.className = 'glor-item flex items-center gap-3 py-2 px-2 rounded-[6px] hover:glor-tintbg cursor-pointer';
                        el.innerHTML =
                            '<img src="' + escapeHtml(img) + '" alt="" loading="lazy" class="w-10 h-10 object-cover rounded-[4px] shrink-0">' +
                            '<span class="flex-1 min-w-0">' +
                            '<span class="block truncate text-[13px] text-[#262626] font-Onest font-medium">' + highlight(name, term) + '</span>' +
                            (meta.length ? '<span class="block truncate text-[12px] text-[#8A8A8A] font-Onest font-regular">' + escapeHtml(meta.join(' \u00B7 ')) + '</span>' : '') +
                            '</span>' +
                            '<span class="text-[13px] text-[#262626] font-Onest font-medium shrink-0">' + price + '</span>';
                        prodList.appendChild(el);
                        items.push(el);
                    });
                }
            }

            if (hasCategories) {
                var catList = panel.querySelector('.glor-cat-list');
                if (catList) {
                    data.categories.forEach(function (cat) {
                        var el = document.createElement('a');
                        el.href = domain + '/products/index.php?category=' + cat.category_id;
                        el.className = 'glor-item block py-1.5 px-2 rounded-[4px] text-[13px] text-[#262626] hover:glor-tintbg hover:glor-text cursor-pointer';
                        el.innerHTML = highlight(cat.category_label, term);
                        catList.appendChild(el);
                        items.push(el);
                    });
                }
            }

            if (hasBrands) {
                var brandList = panel.querySelector('.glor-brand-list');
                if (brandList) {
                    data.brands.forEach(function (brand) {
                        var el = document.createElement('a');
                        el.href = domain + '/products/index.php?brand=' + brand.brand_id;
                        el.className = 'glor-item block py-1.5 px-2 rounded-[4px] text-[13px] text-[#262626] hover:glor-tintbg hover:glor-text cursor-pointer';
                        el.innerHTML = highlight(brand.brand_label, term);
                        brandList.appendChild(el);
                        items.push(el);
                    });
                }
            }

            var viewAll = panel.querySelector('.glor-viewall');
            if (viewAll) items.push(viewAll);
        }

        function renderError() {
            panel.innerHTML =
                '<div class="p-4 text-center">' +
                '<p class="text-[13px] text-[#777777] font-Onest font-regular">Search is unavailable right now. Please try again.</p>' +
                '</div>';
            openPanel();
            items = [];
            selIndex = -1;
        }

        function run(term) {
            if (controller) controller.abort();
            controller = new AbortController();

            items = [];
            selIndex = -1;
            setLoading();

            fetch(domain + '/products/autocomplete.php?q=' + encodeURIComponent(term), {
                signal: controller.signal,
                headers: { 'Accept': 'application/json' }
            })
                .then(function (res) {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(function (data) {
                    if (!data || data.ok === false) {
                        render(term, { products: [], categories: [], brands: [] });
                        return;
                    }
                    render(term, data);
                })
                .catch(function (err) {
                    if (err && err.name === 'AbortError') return;
                    renderError();
                });
        }

        function schedule(term) {
            if (timer) clearTimeout(timer);
            timer = setTimeout(function () { run(term); }, DEBOUNCE_MS);
        }

        function goToSearchPage() {
            var term = input.value.trim();
            if (term.length < 1) return;
            window.location.href = domain + '/products/index.php?search=' + encodeURIComponent(term);
        }

        function highlightItem() {
            items.forEach(function (el, i) {
                if (i === selIndex) {
                    el.classList.add('glor-tintbg');
                } else {
                    el.classList.remove('glor-tintbg');
                }
            });
        }

        input.addEventListener('input', function () {
            var term = input.value.trim();
            if (term.length < MIN_CHARS) {
                closePanel();
                return;
            }
            lastTerm = term;
            schedule(term);
        });

        input.addEventListener('focus', function () {
            var term = input.value.trim();
            if (term.length >= MIN_CHARS) {
                lastTerm = term;
                schedule(term);
            }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (items.length === 0) {
                    var term = input.value.trim();
                    if (term.length >= MIN_CHARS) {
                        lastTerm = term;
                        run(term);
                    }
                    return;
                }
                selIndex = (selIndex + 1) % items.length;
                highlightItem();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (items.length === 0) return;
                selIndex = (selIndex - 1 + items.length) % items.length;
                highlightItem();
            } else if (e.key === 'Enter') {
                if (selIndex >= 0 && selIndex < items.length) {
                    e.preventDefault();
                    window.location.href = items[selIndex].getAttribute('href');
                } else if (input.value.trim().length >= 1) {
                    e.preventDefault();
                    goToSearchPage();
                }
            } else if (e.key === 'Escape') {
                closePanel();
                input.blur();
            }
        });

        if (button) {
            button.addEventListener('click', function () {
                goToSearchPage();
            });
        }

        document.addEventListener('click', function (e) {
            if (!root.contains(e.target)) {
                closePanel();
            }
        });
    }

    function init() {
        var roots = document.querySelectorAll('[data-glor-search]');
        for (var i = 0; i < roots.length; i++) initSearch(roots[i]);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();