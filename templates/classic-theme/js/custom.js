(function ($) {
    "use strict";

    if ($("body").hasClass("rtl")) var rtl = true;
    else rtl = false;

    $(document).ready(function () {
        /*--------------------------------------------------*/
        /*  Sticky Header
        /*--------------------------------------------------*/
        function stickyHeader() {

            $(window).on('scroll load', function() {

                if($(window).width() < '1099') {
                    $("#header-container").removeClass("cloned");
                }

                if($(window).width() > '1099') {

                    // CSS adjustment
                    $("#header-container").css({
                        position: 'fixed',
                    });

                    var headerOffset = $("#header-container").height();

                    if($(window).scrollTop() >= headerOffset){
                        $("#header-container").addClass('cloned');
                        $(".wrapper-with-transparent-header #header-container").addClass('cloned').removeClass("transparent-header unsticky");
                    } else {
                        $("#header-container").removeClass("cloned");
                        $(".wrapper-with-transparent-header #header-container").addClass('transparent-header unsticky').removeClass("cloned");
                    }

                    // Sticky Logo
                    var transparentLogo = $('#header-container #logo img').attr('data-transparent-logo');
                    var stickyLogo = $('#header-container #logo img').attr('data-sticky-logo');

                    if( $('.wrapper-with-transparent-header #header-container').hasClass('cloned')) {
                        $("#header-container.cloned #logo img").attr("src", stickyLogo);
                    }

                    if( $('.wrapper-with-transparent-header #header-container').hasClass('transparent-header')) {
                        $("#header-container #logo img").attr("src", transparentLogo);
                    }


                }
            });
            $(window).on('load resize', function() {
                var headerOffset = $("#header-container").height();
                $("#wrapper").css({'padding-top': headerOffset});
            });
        }

        // Sticky Header Init
        if ($("#header-container").hasClass('sticky')) {
            stickyHeader();
        }else if ($("#header-container").hasClass('unsticky')) {
            var stickyLogo = $('#header-container #logo img').attr('data-sticky-logo');
            $("#header-container #logo img").attr("src", stickyLogo);
        }else{
            var transparentLogo = $('#header-container #logo img').attr('data-transparent-logo');
            if( $('.wrapper-with-transparent-header #header-container').hasClass('transparent-header')) {
                $("#header-container #logo img").attr("src", transparentLogo);
            }
        }
        stickyHeader();

        /*--------------------------------------------------*/
        /*  Transparent Header Spacer Adjustment
        /*--------------------------------------------------*/
        $(window).on('load resize', function() {
            var transparentHeaderHeight = $('.transparent-header').outerHeight();
            $('.transparent-header-spacer').css({
                height: transparentHeaderHeight,
            });
        });

        function backToTop() {
            $('body').append('<div id="backtotop"><a href="#"></a></div>');
        }
        if(!LIVE_CHAT) {
            backToTop();
        }
        var pxShow = 600;
        var scrollSpeed = 500;
        $(window).scroll(function () {
            if ($(window).scrollTop() >= pxShow) {
                $("#backtotop").addClass('visible');
            } else {
                $("#backtotop").removeClass('visible');
            }
        });
        $('#backtotop a').on('click', function () {
            $('html, body').animate({scrollTop: 0}, scrollSpeed);
            return false;
        });

        $('.ripple-effect, .ripple-effect-dark').on('click', function (e) {
            var rippleDiv = $('<span class="ripple-overlay">'), rippleOffset = $(this).offset(),
                rippleY = e.pageY - rippleOffset.top, rippleX = e.pageX - rippleOffset.left;
            rippleDiv.css({
                top: rippleY - (rippleDiv.height() / 2),
                left: rippleX - (rippleDiv.width() / 2),
            }).appendTo($(this));
            window.setTimeout(function () {
                rippleDiv.remove();
            }, 800);
        });

        $(".switch, .radio").each(function () {
            var intElem = $(this);
            intElem.on('click', function () {
                intElem.addClass('interactive-effect');
                setTimeout(function () {
                    intElem.removeClass('interactive-effect');
                }, 400);
            });
        });

        $(window).on('load', function () {
            $(".button.button-sliding-icon").not(".task-listing .button.button-sliding-icon").each(function () {
                var buttonWidth = $(this).outerWidth() + 30;
                $(this).css('width', buttonWidth);
            });

            $("img.lazy-load").each(function() {
                $(this).attr('src', $(this).attr('data-original')).removeClass('lazy-load');
            });
        });

        $('.bookmark-icon').on('click', function (e) {
            e.preventDefault();
            $(this).toggleClass('bookmarked');
        });
        $('.bookmark-button').on('click', function (e) {
            e.preventDefault();
            $(this).toggleClass('bookmarked');
        });
        $("a.close").removeAttr("href").on('click', function () {
            function slideFade(elem) {
                var fadeOut = {opacity: 0, transition: 'opacity 0.5s'};
                elem.css(fadeOut).slideUp();
            }

            slideFade($(this).parent());
        });
        $(".header-notifications").each(function () {
            var userMenu = $(this);
            var userMenuTrigger = $(this).find('.header-notifications-trigger a');
            $(userMenuTrigger).on('click', function (event) {
                event.preventDefault();
                if ($(this).closest(".header-notifications").is(".active")) {
                    close_user_dropdown();
                } else {
                    close_user_dropdown();
                    userMenu.addClass('active');
                }
            });
        });

        function close_user_dropdown() {
            $('.header-notifications').removeClass("active");
        }

        var mouse_is_inside = false;
        $(".header-notifications").on("mouseenter", function () {
            mouse_is_inside = true;
        });
        $(".header-notifications").on("mouseleave", function () {
            mouse_is_inside = false;
        });
        $("body").mouseup(function () {
            if (!mouse_is_inside) close_user_dropdown();
        });
        $(document).keyup(function (e) {
            if (e.keyCode == 27) {
                close_user_dropdown();
            }
        });
        if ($('.status-switch label.user-invisible').hasClass('current-status')) {
            $('.status-indicator').addClass('right');
        }
        $('.status-switch label.user-invisible').on('click', function () {
            $('.status-indicator').addClass('right');
            $('.status-switch label').removeClass('current-status');
            $('.user-invisible').addClass('current-status');
        });
        $('.status-switch label.user-online').on('click', function () {
            $('.status-indicator').removeClass('right');
            $('.status-switch label').removeClass('current-status');
            $('.user-online').addClass('current-status');
        });

        function wrapperHeight() {
            var headerHeight = $("#header-container").outerHeight();
            var windowHeight = $(window).outerHeight() - headerHeight;
            $('.full-page-content-container, .dashboard-content-container, .dashboard-sidebar-inner, .dashboard-container, .full-page-container').css({height: windowHeight});
            $('.dashboard-content-inner').css({'min-height': windowHeight});
        }

        function fullPageScrollbar() {
            $(".full-page-sidebar-inner, .dashboard-sidebar-inner").each(function () {
                var headerHeight = $("#header-container").outerHeight();
                var windowHeight = $(window).outerHeight() - headerHeight;
                var sidebarContainerHeight = $(this).find(".sidebar-container, .dashboard-nav-container").outerHeight();
                if (sidebarContainerHeight > windowHeight) {
                    $(this).css({height: windowHeight});
                } else {
                    $(this).find('.simplebar-track').hide();
                }
            });
        }

        $(window).on('load resize', function () {
            wrapperHeight();
            fullPageScrollbar();
        });
        wrapperHeight();
        fullPageScrollbar();

        // Show More toggle
        $('.show-more-button').on('click', function (e) {
            e.preventDefault();
            $(this).parent().toggleClass('visible');
        });
        // advance search toggle
        $('.enable-filters-button').on('click', function () {
            $('.sidebar-container').slideToggle();
            $(this).toggleClass("active");
        });
        /*----------------------------------------------------*/
        /*  Searh Form More Options
        /*----------------------------------------------------*/
        $('.more-search-options-trigger').on('click', function (e) {
            e.preventDefault();
            $('.more-search-options, .more-search-options-trigger').toggleClass('active');
            $('.more-search-options.relative').animate({height: 'toggle', opacity: 'toggle'}, 300);
        });

        /*----------------------------------------------------*/
        /*  Chosen Plugin
        /*----------------------------------------------------*/

        var config = {
            '.chosen-select': {disable_search_threshold: 10, width: "100%"},
            '.chosen-select-deselect': {allow_single_deselect: true, width: "100%"},
            '.chosen-select-no-single': {disable_search_threshold: 100, width: "100%"},
            '.chosen-select-no-single.no-search': {disable_search_threshold: 10, width: "100%"},
            '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-width': {width: "95%"}
        };

        for (var selector in config) {
            if (config.hasOwnProperty(selector)) {
                $(selector).chosen(config[selector]);
            }
        }
        /*----------------------------------------------------*/
        /*  Chosen Plugin
        /*----------------------------------------------------*/
        /*  Custom Input With Select
           /*----------------------------------------------------*/
        $('.select-input').each(function () {

            var thisContainer = $(this);
            var $this = $(this).children('select'), numberOfOptions = $this.children('option').length;

            $this.addClass('select-hidden');
            $this.wrap('<div class="select"></div>');
            $this.after('<div class="select-styled"></div>');
            var $styledSelect = $this.next('div.select-styled');
            $styledSelect.text($this.children('option').eq(0).text());

            var $list = $('<ul />', {
                'class': 'select-options'
            }).insertAfter($styledSelect);

            for (var i = 0; i < numberOfOptions; i++) {
                $('<li />', {
                    text: $this.children('option').eq(i).text(),
                    rel: $this.children('option').eq(i).val()
                }).appendTo($list);
            }

            var $listItems = $list.children('li');

            $list.wrapInner('<div class="select-list-container"></div>');


            $(this).children('input').on('click', function (e) {
                $('.select-options').hide();
                e.stopPropagation();
                $styledSelect.toggleClass('active').next('ul.select-options').toggle();
            });

            $(this).children('input').keypress(function () {
                $styledSelect.removeClass('active');
                $list.hide();
            });


            $listItems.on('click', function (e) {
                e.stopPropagation();
                $(thisContainer).children('input').val($(this).text()).removeClass('active');
                $this.val($(this).attr('rel'));
                $list.hide();
            });

            $(document).on('click', function (e) {
                $styledSelect.removeClass('active');
                $list.hide();
            });


            // Unit character
            var fieldUnit = $(this).children('input').attr('data-unit');
            $(this).children('input').before('<i class="data-unit">' + fieldUnit + '</i>');


        });
        $(window).on('load', function () {
            $('.filter-button-tooltip').css({left: $('.enable-filters-button').outerWidth() + 48}).addClass('tooltip-visible');
        });

        function avatarSwitcher() {
            var readURL = function (input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('.profile-pic').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            };
            $(".file-upload").on('change', function () {
                readURL(this);
            });
            $(".upload-button").on('click', function () {
                $(".file-upload").click();
            });
        }

        avatarSwitcher();
        $('.dashboard-nav ul li a').on('click', function (e) {
            if ($(this).closest("li").children("ul").length) {
                if ($(this).closest("li").is(".active-submenu")) {
                    $('.dashboard-nav ul li').removeClass('active-submenu');
                } else {
                    $('.dashboard-nav ul li').removeClass('active-submenu');
                    $(this).parent('li').addClass('active-submenu');
                }
                e.preventDefault();
            }
        });
        $('.dashboard-responsive-nav-trigger').on('click', function (e) {
            e.preventDefault();
            $(this).toggleClass('active');
            var dashboardNavContainer = $('body').find(".dashboard-nav");
            if ($(this).hasClass('active')) {
                $(dashboardNavContainer).addClass('active');
            } else {
                $(dashboardNavContainer).removeClass('active');
            }
            $('.dashboard-responsive-nav-trigger .hamburger').toggleClass('is-active');
        });

        function funFacts() {
            function hexToRgbA(hex) {
                var c;
                if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
                    c = hex.substring(1).split('');
                    if (c.length == 3) {
                        c = [c[0], c[0], c[1], c[1], c[2], c[2]];
                    }
                    c = '0x' + c.join('');
                    return 'rgba(' + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(',') + ',0.07)';
                }
            }

            $(".fun-fact").each(function () {
                var factColor = $(this).attr('data-fun-fact-color');
                if (factColor !== undefined) {
                    $(this).find(".fun-fact-icon").css('background-color', hexToRgbA(factColor));
                    $(this).find("i").css('color', factColor);
                }
            });
        }

        funFacts();

        $(window).on('load resize', function () {
            var winwidth = $(window).width();
            if (winwidth > 1199) {
                $('.row').each(function () {
                    var mbh = $(this).find('.main-box-in-row').outerHeight();
                    var cbh = $(this).find('.child-box-in-row').outerHeight();
                    if (mbh < cbh) {
                        var headerBoxHeight = $(this).find('.child-box-in-row .headline').outerHeight();
                        var mainBoxHeight = $(this).find('.main-box-in-row').outerHeight() - headerBoxHeight + 39;
                        $(this).find('.child-box-in-row .content').wrap('<div class="dashboard-box-scrollbar" style="max-height: ' + mainBoxHeight + 'px" data-simplebar></div>');
                    }
                });
            }
        });
        $('.buttons-to-right').each(function () {
            var btr = $(this).width();
            if (btr < 36) {
                $(this).addClass('single-right-button');
            }
        });
        $(window).on('load resize', function () {
            var smallFooterHeight = $('.small-footer').outerHeight();
            $('.dashboard-footer-spacer').css({'padding-top': smallFooterHeight + 45});
        });
        jQuery.each(jQuery('textarea[data-autoresize]'), function () {
            var offset = this.offsetHeight - this.clientHeight;
            var resizeTextarea = function (el) {
                jQuery(el).css('height', 'auto').css('height', el.scrollHeight + offset);
            };
            jQuery(this).on('keyup input', function () {
                resizeTextarea(this);
            }).removeAttr('data-autoresize');
        });

        function starRating(ratingElem) {
            $(ratingElem).each(function () {
                var dataRating = $(this).attr('data-rating');

                function starsOutput(firstStar, secondStar, thirdStar, fourthStar, fifthStar) {
                    return ('' +
                        '<span class="' + firstStar + '"></span>' +
                        '<span class="' + secondStar + '"></span>' +
                        '<span class="' + thirdStar + '"></span>' +
                        '<span class="' + fourthStar + '"></span>' +
                        '<span class="' + fifthStar + '"></span>');
                }

                var fiveStars = starsOutput('star', 'star', 'star', 'star', 'star');
                var fourHalfStars = starsOutput('star', 'star', 'star', 'star', 'star half');
                var fourStars = starsOutput('star', 'star', 'star', 'star', 'star empty');
                var threeHalfStars = starsOutput('star', 'star', 'star', 'star half', 'star empty');
                var threeStars = starsOutput('star', 'star', 'star', 'star empty', 'star empty');
                var twoHalfStars = starsOutput('star', 'star', 'star half', 'star empty', 'star empty');
                var twoStars = starsOutput('star', 'star', 'star empty', 'star empty', 'star empty');
                var oneHalfStar = starsOutput('star', 'star half', 'star empty', 'star empty', 'star empty');
                var oneStar = starsOutput('star', 'star empty', 'star empty', 'star empty', 'star empty');
                if (dataRating >= 4.75) {
                    $(this).append(fiveStars);
                } else if (dataRating >= 4.25) {
                    $(this).append(fourHalfStars);
                } else if (dataRating >= 3.75) {
                    $(this).append(fourStars);
                } else if (dataRating >= 3.25) {
                    $(this).append(threeHalfStars);
                } else if (dataRating >= 2.75) {
                    $(this).append(threeStars);
                } else if (dataRating >= 2.25) {
                    $(this).append(twoHalfStars);
                } else if (dataRating >= 1.75) {
                    $(this).append(twoStars);
                } else if (dataRating >= 1.25) {
                    $(this).append(oneHalfStar);
                } else if (dataRating < 1.25) {
                    $(this).append(oneStar);
                }
            });
        }

        starRating('.star-rating');

        function userMenuScrollbar() {
            $(".header-notifications-scroll").each(function () {
                var scrollContainerList = $(this).find('ul');
                var itemsCount = scrollContainerList.children("li").length;
                var notificationItems;
                if (scrollContainerList.children("li").outerHeight() > 140) {
                    var notificationItems = 2;
                } else {
                    var notificationItems = 3;
                }
                if (itemsCount > notificationItems) {
                    var listHeight = 0;
                    $(scrollContainerList).find('li:lt(' + notificationItems + ')').each(function () {
                        listHeight += $(this).height();
                    });
                    $(this).css({height: listHeight});
                } else {
                    $(this).css({height: 'auto'});
                    $(this).find('.simplebar-track').hide();
                }
            });
        }

        userMenuScrollbar();

        tippy('body', {
            target: '[data-tippy-placement]',
            dynamicTitle: true,
            delay: 100,
            arrow: true,
            arrowType: 'sharp',
            size: 'regular',
            duration: 200,
            animation: 'shift-away',
            animateFill: true,
            theme: 'dark',
            distance: 10,
        });

        var accordion = (function () {
            var $accordion = $('.js-accordion');
            var $accordion_header = $accordion.find('.js-accordion-header');
            var settings = {speed: 400, oneOpen: false};
            return {
                init: function ($settings) {
                    $accordion_header.on('click', function () {
                        accordion.toggle($(this));
                    });
                    $.extend(settings, $settings);
                    if (settings.oneOpen && $('.js-accordion-item.active').length > 1) {
                        $('.js-accordion-item.active:not(:first)').removeClass('active');
                    }
                    $('.js-accordion-item.active').find('> .js-accordion-body').show();
                }, toggle: function ($this) {
                    if (settings.oneOpen && $this[0] != $this.closest('.js-accordion').find('> .js-accordion-item.active > .js-accordion-header')[0]) {
                        $this.closest('.js-accordion').find('> .js-accordion-item').removeClass('active').find('.js-accordion-body').slideUp();
                    }
                    $this.closest('.js-accordion-item').toggleClass('active');
                    $this.next().stop().slideToggle(settings.speed);
                }
            };
        })();
        $(document).ready(function () {
            accordion.init({speed: 300, oneOpen: true});
        });
        $(window).on('load resize', function () {
            if ($(".tabs")[0]) {
                $('.tabs').each(function () {
                    var thisTab = $(this);
                    var activePos = thisTab.find('.tabs-header .active').position();

                    function changePos() {
                        activePos = thisTab.find('.tabs-header .active').position();
                        if(activePos) {
                            thisTab.find('.tab-hover').stop().css({
                                left: activePos.left,
                                width: thisTab.find('.tabs-header .active').width()
                            });
                        }
                    }

                    changePos();
                    var tabHeight = thisTab.find('.tab.active').outerHeight();

                    function animateTabHeight() {
                        tabHeight = thisTab.find('.tab.active').outerHeight();
                        thisTab.find('.tabs-content').stop().css({height: tabHeight + 'px'});
                    }

                    animateTabHeight();

                    function changeTab() {
                        var getTabId = thisTab.find('.tabs-header .active a').attr('data-tab-id');
                        thisTab.find('.tab').stop().fadeOut(300, function () {
                            $(this).removeClass('active');
                        }).hide();
                        thisTab.find('.tab[data-tab-id=' + getTabId + ']').stop().fadeIn(300, function () {
                            $(this).addClass('active');
                            animateTabHeight();
                        });
                    }

                    thisTab.find('.tabs-header a').on('click', function (e) {
                        e.preventDefault();
                        var tabId = $(this).attr('data-tab-id');
                        thisTab.find('.tabs-header a').stop().parent().removeClass('active');
                        $(this).stop().parent().addClass('active');
                        changePos();
                        tabCurrentItem = tabItems.filter('.active');
                        thisTab.find('.tab').stop().fadeOut(300, function () {
                            $(this).removeClass('active');
                        }).hide();
                        thisTab.find('.tab[data-tab-id="' + tabId + '"]').stop().fadeIn(300, function () {
                            $(this).addClass('active');
                            animateTabHeight();
                        });
                    });
                    var tabItems = thisTab.find('.tabs-header ul li');
                    var tabCurrentItem = tabItems.filter('.active');
                    thisTab.find('.tab-next').on('click', function (e) {
                        e.preventDefault();
                        var nextItem = tabCurrentItem.next();
                        tabCurrentItem.removeClass('active');
                        if (nextItem.length) {
                            tabCurrentItem = nextItem.addClass('active');
                        } else {
                            tabCurrentItem = tabItems.first().addClass('active');
                        }
                        changePos();
                        changeTab();
                    });
                    thisTab.find('.tab-prev').on('click', function (e) {
                        e.preventDefault();
                        var prevItem = tabCurrentItem.prev();
                        tabCurrentItem.removeClass('active');
                        if (prevItem.length) {
                            tabCurrentItem = prevItem.addClass('active');
                        } else {
                            tabCurrentItem = tabItems.last().addClass('active');
                        }
                        changePos();
                        changeTab();
                    });
                });
            }
        }).trigger('resize');
        $(".keywords-container").each(function () {
            var keywordInput = $(this).find(".keyword-input");
            var keywordsList = $(this).find(".keywords-list");

            function addKeyword() {
                var $newKeyword = $("<span class='keyword'><span class='keyword-remove'></span><span class='keyword-text'>" + keywordInput.val() + "</span></span>");
                keywordsList.append($newKeyword).trigger('resizeContainer');
                keywordInput.val("");
            }

            keywordInput.on('keyup', function (e) {
                if ((e.keyCode == 13) && (keywordInput.val() !== "")) {
                    addKeyword();
                }
            });
            $('.keyword-input-button').on('click', function () {
                if ((keywordInput.val() !== "")) {
                    addKeyword();
                }
            });
            $(document).on("click", ".keyword-remove", function () {
                $(this).parent().addClass('keyword-removed');

                function removeFromMarkup() {
                    $(".keyword-removed").remove();
                }

                setTimeout(removeFromMarkup, 500);
                keywordsList.css({'height': 'auto'}).height();
            });
            keywordsList.on('resizeContainer', function () {
                var heightnow = $(this).height();
                var heightfull = $(this).css({'max-height': 'auto', 'height': 'auto'}).height();
                $(this).css({'height': heightnow}).animate({'height': heightfull}, 200);
            });
            $(window).on('resize', function () {
                keywordsList.css({'height': 'auto'}).height();
            });
            $(window).on('load', function () {
                var keywordCount = $('.keywords-list').children("span").length;
                if (keywordCount > 0) {
                    keywordsList.css({'height': 'auto'}).height();
                }
            });
        });

        function ThousandSeparator(nStr) {
            nStr += '';
            var x = nStr.split('.');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        var avgValue = (parseInt($('.bidding-slider').attr("data-slider-min")) + parseInt($('.bidding-slider').attr("data-slider-max"))) / 2;
        if ($('.bidding-slider').data("slider-value") === 'auto') {
            $('.bidding-slider').attr({'data-slider-value': avgValue});
        }
        $('.bidding-slider').slider();
        $(".bidding-slider").on("slide", function (slideEvt) {
            $("#biddingVal").text(ThousandSeparator(parseInt(slideEvt.value)));
        });
        $("#biddingVal").text(ThousandSeparator(parseInt($('.bidding-slider').val())));
        var currencyAttr = $(".range-slider").attr('data-slider-currency');
        $(".range-slider").slider({
            formatter: function (value) {
                return currencyAttr + ThousandSeparator(parseInt(value[0])) + " - " + currencyAttr + ThousandSeparator(parseInt(value[1]));
            }
        });
        $(".range-slider-single").slider();
        var radios = document.querySelectorAll('.payment-tab-trigger > input');
        for (var i = 0; i < radios.length; i++) {
            radios[i].addEventListener('change', expandAccordion);
        }

        function expandAccordion(event) {
            var tabber = this.closest('.payment');
            var allTabs = tabber.querySelectorAll('.payment-tab');
            for (var i = 0; i < allTabs.length; i++) {
                allTabs[i].classList.remove('payment-tab-active');
            }
            event.target.parentNode.parentNode.classList.add('payment-tab-active');
        }

        $('.billing-cycle-radios').on("click", function () {
            if ($('.billed-yearly-radio input').is(':checked')) {
                $('.pricing-plans-container').addClass('billed-yearly').removeClass('billed-lifetime');

                $('.pricing-plan').show();
                $('.pricing-plan[data-annual-price="0"]').hide();
            }
            if ($('.billed-monthly-radio input').is(':checked')) {
                $('.pricing-plans-container').removeClass('billed-yearly').removeClass('billed-lifetime');

                $('.pricing-plan').show();
                $('.pricing-plan[data-monthly-price="0"]').hide();
            }
            if ($('.billed-lifetime-radio input').is(':checked')) {
                $('.pricing-plans-container').addClass('billed-lifetime').removeClass('billed-yearly');

                $('.pricing-plan').show();
                $('.pricing-plan[data-lifetime-price="0"]').hide();
            }
        });
        $('.billing-cycle-radios input').first().trigger('click');

        function qtySum() {
            var arr = document.getElementsByName('qtyInput');
            var tot = 0;
            for (var i = 0; i < arr.length; i++) {
                if (parseInt(arr[i].value))
                    tot += parseInt(arr[i].value);
            }
        }

        qtySum();
        $(".qtyDec, .qtyInc").on("click", function () {
            var $button = $(this);
            var oldValue = $button.parent().find("input").val();
            if ($button.hasClass('qtyInc')) {
                $button.parent().find("input").val(parseFloat(oldValue) + 1);
            } else {
                if (oldValue > 1) {
                    $button.parent().find("input").val(parseFloat(oldValue) - 1);
                } else {
                    $button.parent().find("input").val(1);
                }
            }
            qtySum();
            $(".qtyTotal").addClass("rotate-x");
        });

        function inlineBG() {
            $(".single-page-header, .intro-banner").each(function () {
                var attrImageBG = $(this).attr('data-background-image');
                if (attrImageBG !== undefined) {
                    $(this).append('<div class="background-image-container"></div>');
                    $('.background-image-container').css('background-image', 'url(' + attrImageBG + ')');
                }
            });
        }

        inlineBG();
        $(".intro-search-field").each(function () {
            var bannerLabel = $(this).children("label").length;
            if (bannerLabel > 0) {
                $(this).addClass("with-label");
            }
        });
        $(".photo-box, .photo-section, .video-container").each(function () {
            var photoBox = $(this);
            var photoBoxBG = $(this).attr('data-background-image');
            if (photoBox !== undefined) {
                $(this).css('background-image', 'url(' + photoBoxBG + ')');
            }
        });

        $(".share-buttons-icons a").each(function () {
            var buttonBG = $(this).attr("data-button-color");
            if (buttonBG !== undefined) {
                $(this).css('background-color', buttonBG);
            }
        });
        var $tabsNav = $('.popup-tabs-nav'), $tabsNavLis = $tabsNav.children('li');
        $tabsNav.each(function () {
            var $this = $(this);
            $this.next().children('.popup-tab-content').stop(true, true).hide().first().show();
            $this.children('li').first().addClass('active').stop(true, true).show();
        });
        $tabsNavLis.on('click', function (e) {
            var $this = $(this);
            $this.siblings().removeClass('active').end().addClass('active');
            $this.parent().next().children('.popup-tab-content').stop(true, true).hide().siblings($this.find('a').attr('href')).fadeIn();
            e.preventDefault();
        });
        var hash = window.location.hash;
        var anchor = $('.tabs-nav a[href="' + hash + '"]');
        if (anchor.length === 0) {
            $(".popup-tabs-nav li:first").addClass("active").show();
            $(".popup-tab-content:first").show();
        } else {
            anchor.parent('li').click();
        }
        $('.register-tab').on('click', function (event) {
            event.preventDefault();
            $(".popup-tab-content").hide();
            $("#register.popup-tab-content").show();
            $("body").find('.popup-tabs-nav a[href="#register"]').parent("li").click();
        });
        $('.popup-tabs-nav').each(function () {
            var listCount = $(this).find("li").length;
            if (listCount < 2) {
                $(this).css({'pointer-events': 'none'});
            }
        });
        $('.indicator-bar').each(function () {
            var indicatorLenght = $(this).attr('data-indicator-percentage');
            $(this).find("span").css({width: indicatorLenght + "%"});
        });

        $('.default-slick-carousel').slick({
            rtl: rtl,
            infinite: false,
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: false,
            arrows: true,
            adaptiveHeight: true,
            responsive: [{breakpoint: 1292, settings: {dots: true, arrows: false}}, {
                breakpoint: 993,
                settings: {slidesToShow: 2, slidesToScroll: 2, dots: true, arrows: false}
            }, {breakpoint: 769, settings: {slidesToShow: 1, slidesToScroll: 1, dots: true, arrows: false}}]
        });
        $('.testimonial-carousel').slick({
            rtl: rtl,
            centerMode: true,
            centerPadding: '30%',
            slidesToShow: 1,
            dots: false,
            arrows: true,
            adaptiveHeight: true,
            responsive: [{breakpoint: 1600, settings: {centerPadding: '21%', slidesToShow: 1,}}, {
                breakpoint: 993,
                settings: {centerPadding: '15%', slidesToShow: 1,}
            }, {breakpoint: 769, settings: {centerPadding: '5%', dots: true, arrows: false}}]
        });
        $('.logo-carousel').slick({
            rtl: rtl,
            infinite: true,
            slidesToShow: 5,
            slidesToScroll: 1,
            dots: false,
            arrows: true,
            responsive: [{breakpoint: 1365, settings: {slidesToShow: 5, dots: true, arrows: false}}, {
                breakpoint: 992,
                settings: {slidesToShow: 3, dots: true, arrows: false}
            }, {breakpoint: 768, settings: {slidesToShow: 1, dots: true, arrows: false}}]
        });
        $('.blog-carousel').slick({
            rtl: rtl,
            infinite: false,
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: false,
            arrows: true,
            responsive: [{breakpoint: 1365, settings: {slidesToShow: 3, dots: true, arrows: false}}, {
                breakpoint: 992,
                settings: {slidesToShow: 2, dots: true, arrows: false}
            }, {breakpoint: 768, settings: {slidesToShow: 1, dots: true, arrows: false}}]
        });
        $('.mfp-gallery-container').each(function () {
            $(this).magnificPopup({
                type: 'image',
                delegate: 'a.mfp-gallery',
                fixedContentPos: true,
                fixedBgPos: true,
                overflowY: 'auto',
                closeBtnInside: false,
                preloader: true,
                removalDelay: 0,
                mainClass: 'mfp-fade',
                gallery: {enabled: true, tCounter: ''}
            });
        });
        $('.popup-with-zoom-anim').magnificPopup({
            type: 'inline',
            fixedContentPos: false,
            fixedBgPos: true,
            overflowY: 'auto',
            closeBtnInside: true,
            preloader: false,
            midClick: true,
            removalDelay: 300,
            mainClass: 'my-mfp-zoom-in'
        });
        $('.mfp-image').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            mainClass: 'mfp-fade',
            image: {verticalFit: true}
        });
        $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false
        });

        // cookie consent
        if (localStorage.getItem('Quick-cookie') != '1') {
            $('.cookieConsentContainer').delay(2000).fadeIn();
        }
        $('.cookieAcceptButton').on('click', function () {
            localStorage.setItem('Quick-cookie', '1');
            $('.cookieConsentContainer').fadeOut();
        });

        // testimonial carousel
        $('.single-carousel').slick({
            rtl: rtl,
            centerMode: true,
            centerPadding: '0',
            slidesToShow: 1,
            dots: true,
            arrows: false,
            adaptiveHeight: true,
            autoplay: true,
        });

        // header icon
        $('.header-icon').on('click', function (e) {
            e.preventDefault();
            if($('.dashboard-sidebar').hasClass('hide-sidebar')){
                $('.dashboard-sidebar').removeClass('hide-sidebar');
                setTimeout(function () {
                    $('.dashboard-sidebar').css('width','auto');
                }, 200);
            } else {
                $('.dashboard-sidebar').css('width',0);
                setTimeout(function () {
                    $('.dashboard-sidebar').addClass('hide-sidebar');

                }, 200);
            }
        });

        $('.toggleFullScreen').on('click', function (e) {
            e.preventDefault();
            if ((document.fullScreenElement) ||
                (!document.mozFullScreen && !document.webkitIsFullScreen)) {
                if (document.documentElement.requestFullScreen) {
                    document.documentElement.requestFullScreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullScreen) {
                    document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
                }
                $(this).find('i').removeClass('icon-feather-maximize').addClass('icon-feather-minimize');
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
                $(this).find('i').removeClass('icon-feather-minimize').addClass('icon-feather-maximize');
            }
        });

        // ai template blocks
        $('.ai-templates-category').on('click', function (e) {
            e.preventDefault();
            // make active
            $('.template-categories li').removeClass('active');
            $(this).parents('li').addClass('active');

            if($(this).data('category') === 'all') {
                $('.ai-template-blocks > div').show();
                $('.ai-templates-category-title').show();
            } else {
                $('.ai-template-blocks > div').hide();
                $('.category-' + $(this).data('category')).show();
                $('.ai-templates-category-title').hide();

                // empty search
                $('#template-search').val('');
            }

            if($('.ai-template-blocks-toggle').length){
                if($('.ai-template-blocks').height() <= 690){
                    $('.ai-template-blocks-toggle').removeClass('show-blocks-toggle')
                    $('.ai-template-blocks-toggle-button').hide()
                } else {
                    $('.ai-template-blocks-toggle').addClass('show-blocks-toggle')
                    $('.ai-template-blocks-toggle-button').show()
                }
            }
        });

        $('#export_to_word').on('click', function (e) {
            e.preventDefault();

            var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
            var postHtml = "</body></html>";
            var html = preHtml + tinymce.activeEditor.getContent() + postHtml;

            var blob = new Blob(['\ufeff', html], {
                type: 'application/msword'
            });

            // Specify link url
            var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);

            // Specify file name
            var filename = 'document.doc';

            // Create download link element
            var downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);

            if (navigator.msSaveOrOpenBlob) {
                navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                // Create a link to the file
                downloadLink.href = url;

                // Setting the file name
                downloadLink.download = filename;

                //triggering the function
                downloadLink.click();
            }

            document.body.removeChild(downloadLink);
        });

        $('#export_to_txt').on('click', function (e) {
            e.preventDefault();

            var txt = tinymce.activeEditor.getContent();

            // replace br with \n
            var regex = /<br\s*[\/]?>/gi;
            txt = txt.replace(regex, "\n");

            // remove html tags
            txt = $('<div>'+txt+'</div>').text();

            var downloadableLink = document.createElement('a');
            downloadableLink.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(txt));
            downloadableLink.download = "Text File" + ".txt";
            document.body.appendChild(downloadableLink);
            downloadableLink.click();
            document.body.removeChild(downloadableLink);
        });

        $('#copy_text').on('click', function (e) {
            e.preventDefault();

            tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody());
            tinyMCE.activeEditor.execCommand("Copy");

            Snackbar.show({
                text: LANG_COPIED_SUCCESSFULLY,
                pos: 'bottom-center',
                    showAction: false,
                    actionText: "Dismiss",
                    duration: 3000,
                    textColor: '#fff',
                    backgroundColor: '#383838'
            });
        });

        // credit
        if(DEVELOPER_CREDIT){
            $('.footer-copyright-text').append('&nbsp; | &nbsp;Developed by <a href="https://bylancer.com/">Bylancer</a>')
        }

        // template search
        $(document).on('keyup', '#template-search', function () {
            $('[data-category="all"]').click();

            var searchTerm = $(this).val().toLowerCase();
            $('.ai-template-blocks').find('> div').each(function () {
                if ($(this).filter(function() {
                    return $(this).find('h4').text().toLowerCase().indexOf(searchTerm) > -1;
                }).length > 0 || searchTerm.length < 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // chatbot search
        $(document).on('keyup', '#chat-bot-search', function () {
            $('[data-category="all"]').click();

            var searchTerm = $(this).val().toLowerCase();
            $('.ai-template-blocks').find('> div').each(function () {
                if ($(this).filter(function() {
                    return $(this).data('search').toLowerCase().indexOf(searchTerm) > -1;
                }).length > 0 || searchTerm.length < 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // ai images
        $('.image-advance-settings-trigger').on('click', function (e) {
            e.preventDefault();
            $('.image-advance-settings').slideToggle();
            var $plus = $(this).find('strong');
            if($plus.text() === '+'){
                $plus.text('-')
            } else {
                $plus.text('+')
            }
        });

        $('.ai-template-blocks-toggle-button a').on('click', function (e) {
            e.preventDefault();
            $('.ai-template-blocks-toggle').toggleClass('show-blocks-toggle');
        });

    });
})(this.jQuery);

function readImageURL(input, id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#' + id).attr('src', e.target.result);
            $('#' + id).show();
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        $('#' + id).hide();
    }
}

var w = screen.width - (screen.width * 25 / 100);
var h = screen.height - (screen.height * 25 / 100);
var left = (screen.width / 2) - (w / 2);
var top = (screen.height / 2) - (h / 2);

function fblogin() {
    var newWin = window.open(siteurl + "includes/social_login/facebook/index.php", "fblogin", 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no,display=popup, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}

function gmlogin() {
    var newWin = window.open(siteurl + "includes/social_login/google/index.php", "gmlogin", 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}

// copy code
function copyAICode(button) {
    const pre = button.parentElement;
    const code = pre.querySelector('code');
    const range = document.createRange();
    range.selectNode(code);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    document.execCommand("copy");
    window.getSelection().removeAllRanges();
    Snackbar.show({
        text: LANG_COPIED_SUCCESSFULLY,
        pos: 'bottom-center',
        showAction: false,
        actionText: "Dismiss",
        duration: 3000,
        textColor: '#fff',
        backgroundColor: '#383838'
    });
}
