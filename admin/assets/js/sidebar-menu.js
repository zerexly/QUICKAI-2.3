(function ($) {
    var page = QuickMenu.page;

    $(".iconMenu-bar li[data-page='"+page+"'] a").removeClass('active');
    $(".iconMenu-bar li[data-page='"+page+"'] a[data-page='"+QuickMenu.subpage+"']").addClass('active');

    if($(".iconMenu-bar li[data-page='"+page+"'] ul").length) {
        $('.iconsidebar-menu').removeClass('iconbar-mainmenu-close');

        $('.iconbar-mainmenu a').on('shown.bs.tab', function(event){
            $('.iconbar-mainmenu li').removeClass('active');
            $(this).parents('li').addClass('active');
        });
    }
    // sidebar menu
    $(".iconMenu-bar li").on('click', function (e) {
        $(".iconMenu-bar li").removeClass("open");
        if ($(this).find('ul').length) {
            if ($('.iconsidebar-menu').hasClass('iconbar-mainmenu-close')) {
                $('.iconsidebar-menu').removeClass('iconbar-mainmenu-close');
            }
        } else {
            $(".iconsidebar-menu").addClass("iconbar-mainmenu-close");
        }
        $(this).addClass("open");
    });
    $('.iconMenu-bar li a[data-toggle="tab"]').on('click', function (e) {
        e.preventDefault();
    });


    // this will get the full URL at the address bar
    $(".iconMenu-bar li").removeClass("open");

    $(".iconMenu-bar > li").each(function () {
        // checks if its the same on the address bar
        if (page === $(this).data('page')) {
            $(this).addClass("open");
        }
    });


    $('.mobile-sidebar #sidebar-toggle').on('click', function () {
        var $this = $(".iconsidebar-menu");
        if ($this.hasClass('iconbar-second-close')) {
            $this.removeClass('iconbar-second-close');
            if(!$(".iconMenu-bar > li.open").find('ul').length)
                $this.addClass('iconbar-mainmenu-close');
        } else if ($this.hasClass('iconbar-mainmenu-close')) {
            $this.removeClass('iconbar-mainmenu-close').addClass('iconbar-second-close');
        } else {
            $this.addClass('iconbar-mainmenu-close');
        }
    });

    $(window)
        .on('resize', function (){
            if ($(window).width() <= 991) {
                $(".iconsidebar-menu").addClass("iconbar-mainmenu-close");
                $('.iconMenu-bar').removeClass("active");
                $('.iconsidebar-menu').addClass("iconbar-second-close");
                $('.iconsidebar-menu').removeClass("iconbar-mainmenu-close");
            }
        })
        .trigger('resize');

})(jQuery);