/**
 * Main
 */

'use strict';

let menu, animate;

(function () {
    // Initialize menu
    //-----------------

    let layoutMenuEl = document.querySelectorAll('#layout-menu');
    layoutMenuEl.forEach(function (element) {
        menu = new Menu(element, {
            orientation: 'vertical',
            closeChildren: false
        });
        // Change parameter to true if you want scroll animation
        window.Helpers.scrollToActive((animate = false));
        window.Helpers.mainMenu = menu;
    });

    // Initialize menu togglers and bind click on each
    let menuToggler = document.querySelectorAll('.layout-menu-toggle');
    menuToggler.forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            window.Helpers.toggleCollapsed();
        });
    });

    // Display menu toggle (layout-menu-toggle) on hover with delay
    let delay = function (elem, callback) {
        let timeout = null;
        elem.onmouseenter = function () {
            // Set timeout to be a timer which will invoke callback after 300ms (not for small screen)
            if (!Helpers.isSmallScreen()) {
                timeout = setTimeout(callback, 300);
            } else {
                timeout = setTimeout(callback, 0);
            }
        };

        elem.onmouseleave = function () {
            // Clear any timers set to timeout
            document.querySelector('.layout-menu-toggle').classList.remove('d-block');
            clearTimeout(timeout);
        };
    };
    if (document.getElementById('layout-menu')) {
        delay(document.getElementById('layout-menu'), function () {
            // not for small screen
            if (!Helpers.isSmallScreen()) {
                document.querySelector('.layout-menu-toggle').classList.add('d-block');
            }
        });
    }

    // Display in main menu when menu scrolls
    let menuInnerContainer = document.getElementsByClassName('menu-inner'),
        menuInnerShadow = document.getElementsByClassName('menu-inner-shadow')[0];
    if (menuInnerContainer.length > 0 && menuInnerShadow) {
        menuInnerContainer[0].addEventListener('ps-scroll-y', function () {
            if (this.querySelector('.ps__thumb-y').offsetTop) {
                menuInnerShadow.style.display = 'block';
            } else {
                menuInnerShadow.style.display = 'none';
            }
        });
    }

    // Init helpers & misc
    // --------------------

    // Init BS Tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Accordion active class
    const accordionActiveFunction = function (e) {
        if (e.type == 'show.bs.collapse' || e.type == 'show.bs.collapse') {
            e.target.closest('.accordion-item').classList.add('active');
        } else {
            e.target.closest('.accordion-item').classList.remove('active');
        }
    };

    const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
    const accordionList = accordionTriggerList.map(function (accordionTriggerEl) {
        accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
        accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
    });

    // Auto update layout based on screen size
    window.Helpers.setAutoUpdate(true);

    // Toggle Password Visibility
    window.Helpers.initPasswordToggle();

    // Speech To Text
    window.Helpers.initSpeechToText();

    // Manage menu expanded/collapsed with templateCustomizer & local storage
    //------------------------------------------------------------------

    // If current layout is horizontal OR current window screen is small (overlay menu) than return from here
    if (window.Helpers.isSmallScreen()) {
        return;
    }

    // If current layout is vertical and current window screen is > small

    // Auto update menu collapsed/expanded based on the themeConfig
    window.Helpers.setCollapsed(true, false);

    $(document).ready(function () {
        setTimeout(function () {
            "undefined" != typeof $ && $(function () {
                window.Helpers.initSidebarToggle();
                var t, o, e, n = $(".search-toggler"), a = $(".search-input-wrapper"), s = $(".search-input"),
                    l = $(".content-backdrop");
                n.length && n.on("click", function () {
                    a.length && (a.toggleClass("d-none"),
                        s.focus())
                }),
                    $(document).on("keydown", function (e) {
                        var t = e.ctrlKey
                            , e = 191 === e.which;
                        t && e && a.length && (a.toggleClass("d-none"),
                            s.focus())
                    }),
                    setTimeout(function () {
                        var e = $(".twitter-typeahead");
                        s.on("focus", function () {
                            a.hasClass("container-xxl") ? (a.find(e).addClass("container-xxl"),
                                e.removeClass("container-fluid")) : a.hasClass("container-fluid") && (a.find(e).addClass("container-fluid"),
                                e.removeClass("container-xxl"))
                        })
                    }, 10),
                s.length && (t = function (n) {
                    return function (t, e) {
                        let o;
                        o = [],
                            n.filter(function (e) {
                                if (e.name.toLowerCase().startsWith(t.toLowerCase()))
                                    o.push(e);
                                else {
                                    if (e.name.toLowerCase().startsWith(t.toLowerCase()) || !e.name.toLowerCase().includes(t.toLowerCase()))
                                        return [];
                                    o.push(e),
                                        o.sort(function (e, t) {
                                            return t.name < e.name ? 1 : -1
                                        })
                                }
                            }),
                            e(o)
                    }
                }
                    ,
                    n = "search-vertical.json",
                $("#layout-menu").hasClass("menu-horizontal") && (n = "search-horizontal.json"),
                    o = $.ajax({
                        url: assetsPath + "json/" + n,
                        dataType: "json",
                        async: !1
                    }).responseJSON,
                    s.each(function () {
                        var e = $(this);
                        s.typeahead({
                            hint: !1,
                            classNames: {
                                menu: "tt-menu navbar-search-suggestion",
                                cursor: "active",
                                suggestion: "suggestion d-flex justify-content-between px-3 py-2 w-100"
                            }
                        }, {
                            name: "pages",
                            display: "name",
                            limit: 5,
                            source: t(o.pages),
                            templates: {
                                header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Pages</h6>',
                                suggestion: function ({url: e, icon: t, name: o}) {
                                    return '<a href="' + e + '"><div><i class="bx ' + t + ' me-2"></i><span class="align-middle">' + o + "</span></div></a>"
                                },
                                notFound: '<div class="not-found px-3 py-2"><h6 class="suggestions-header text-primary mb-2">Pages</h6><p class="py-2 mb-0"><i class="bx bx-error-circle bx-xs me-2"></i> No Results Found</p></div>'
                            }
                        }, {
                            name: "files",
                            display: "name",
                            limit: 4,
                            source: t(o.files),
                            templates: {
                                header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Files</h6>',
                                suggestion: function ({src: e, name: t, subtitle: o, meta: n}) {
                                    return '<a href="javascript:;"><div class="d-flex w-50"><img class="me-3" src="' + assetsPath + e + '" alt="' + t + '" height="32"><div class="w-75"><h6 class="mb-0">' + t + '</h6><small class="text-muted">' + o + '</small></div></div><small class="text-muted">' + n + "</small></a>"
                                },
                                notFound: '<div class="not-found px-3 py-2"><h6 class="suggestions-header text-primary mb-2">Files</h6><p class="py-2 mb-0"><i class="bx bx-error-circle bx-xs me-2"></i> No Results Found</p></div>'
                            }
                        }, {
                            name: "members",
                            display: "name",
                            limit: 4,
                            source: t(o.members),
                            templates: {
                                header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Members</h6>',
                                suggestion: function ({name: e, src: t, subtitle: o}) {
                                    return '<a href="app-user-view-account.html"><div class="d-flex align-items-center"><img class="rounded-circle me-3" src="' + assetsPath + t + '" alt="' + e + '" height="32"><div class="user-info"><h6 class="mb-0">' + e + '</h6><small class="text-muted">' + o + "</small></div></div></a>"
                                },
                                notFound: '<div class="not-found px-3 py-2"><h6 class="suggestions-header text-primary mb-2">Members</h6><p class="py-2 mb-0"><i class="bx bx-error-circle bx-xs me-2"></i> No Results Found</p></div>'
                            }
                        }).bind("typeahead:render", function () {
                            l.addClass("show").removeClass("fade")
                        }).bind("typeahead:select", function (e, t) {
                            t.url && (window.location = t.url)
                        }).bind("typeahead:close", function () {
                            s.val(""),
                                e.typeahead("val", ""),
                                a.addClass("d-none"),
                                l.addClass("fade").removeClass("show")
                        }),
                            s.on("keyup", function () {
                                "" == s.val() && l.addClass("fade").removeClass("show")
                            })
                    }),
                    $(".navbar-search-suggestion").each(function () {
                        e = new PerfectScrollbar($(this)[0], {
                            wheelPropagation: !1,
                            suppressScrollX: !0
                        })
                    }),
                    s.on("keyup", function () {
                        e.update()
                    }))
            });
        );
})();




