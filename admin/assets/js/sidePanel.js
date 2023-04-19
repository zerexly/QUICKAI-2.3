var $body = jQuery('body'),
    lastPanel = [];

var quick_show_sidepanel = function ($panel) {
    $body.css('overflow-y', 'hidden');
    $backdrop = jQuery('<div class="slidePanel-wrapper"/>');
    $body.append($backdrop);
    $panel.css('transition', 'transform 0.6s ease');
    $panel.addClass('slidePanel-show').css('transform', 'translate3d(0%, 0px, 0px)');
    lastPanel.push($panel);
};

var quick_hide_sidepanel = function ($panel) {
    if($panel){
        $panel.css('transform', $panel.hasClass('slidePanel-left') ? 'translate3d(-100%, 0px, 0px)' : 'translate3d(100%, 0px, 0px)');
        setTimeout(function () {
            $panel.removeClass('slidePanel-show');
        }, 600);
        $backdrop = jQuery('.slidePanel-wrapper');
        if ($backdrop.length > 1) {
            jQuery($backdrop[0]).fadeOut(600, function () {
                jQuery(this).remove();
            });
        } else {
            $backdrop.fadeOut(600, function () {
                jQuery(this).remove();
            });
        }
        $body.css('overflow-y', 'auto');
        lastPanel.splice(lastPanel.length - 1, 1);
        $panel.trigger('sidePanel.hide');
    }
};

jQuery(document).on("click", ".slidePanel-close", function (e) {
    quick_hide_sidepanel(jQuery(this).parents('.slidePanel'));
}).on("click", ".slidePanel-wrapper", function (e) {
    quick_hide_sidepanel(lastPanel[lastPanel.length - 1]);
});