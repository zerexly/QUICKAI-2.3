(function ($) {
    "use strict";

    const $form = $('#ai-chat-form');
    const $error = $form.find('.form-error')
    const $msgInput = $('#ai-chat-textarea');
    const $msgChat = $(".message-content-inner");
    const $msgSendBtn = $("#chat-send-button");

    $(document).ready( function (){
        $msgChat.animate({
            scrollTop: $msgChat.prop("scrollHeight")
        }, 1);
    });

    // delete chats
    $('#delete-chats').on('click', function (e){
        e.preventDefault();
        e.stopPropagation();

        var $btn = $(this);

        if(confirm(LANG_ARE_YOU_SURE)) {
            $btn.addClass('button-progress').prop('disabled', true);
            $.ajax({
                type: "POST",
                url: ajaxurl + '?action=delete_ai_chats',
                dataType: 'json',
                success: function (response) {
                    $btn.removeClass('button-progress').prop('disabled', false);
                    if (response.success) {
                        window.location.reload();
                    }
                }
            });
        }
    });

    // export chat
    $('#export-chats').on('click', function (e){
        e.preventDefault();
        e.stopPropagation();

        var $btn = $(this);
        $btn.addClass('button-progress').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=export_ai_chats',
            dataType: 'json',
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                if (response.success) {

                    var downloadableLink = document.createElement('a');
                    downloadableLink.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(response.text));
                    downloadableLink.download = "Chats" + ".txt";
                    document.body.appendChild(downloadableLink);
                    downloadableLink.click();
                    document.body.removeChild(downloadableLink);
                }
            }
        });
    });

    // on send
    $form.on('submit', function (e) {
        e.preventDefault();

        const msgText = $msgInput.val();
        if (!msgText) return;

        // append user message
        appendMessage(PERSON_NAME, PERSON_IMG, "right", msgText);
        $msgInput.val('');

        // append bot message
        let id = $msgChat.find('.message-bubble').length;
        appendMessage(BOT_NAME, BOT_IMG, "left", "", id);

        let $div = $("#msg-"+id);
        let $msg_txt = $div.find('p');
        let $typing = $div.find('.typing-indicator');

        $msgSendBtn.prop('disabled', true);
        var formData = new FormData();
        formData.append('msg', msgText);

        $error.slideUp();
        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=send_ai_message',
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                $msgSendBtn.prop('disabled', false);

                if (response.success) {
                    $typing.hide();
                    $msg_txt.show();
                    $msg_txt.html(response.message);

                    $msgChat.animate({
                        scrollTop: $msgChat.prop("scrollHeight")
                    }, 500);

                    animate_value('quick-words-left', response.old_used_words, response.current_used_words, 4000);
                } else {
                    $msgSendBtn.prop('disabled', false);
                    $error.html(response.error).slideDown().focus();
                }
            }
        });
    });

    function appendMessage(name, img, side, text, id) {

        let typing = (side === 'left')
            ? `<div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>`
            : '';
        let msg_show = (side === 'left') ? 'style="display:none"' : '';

        side = (side === 'right') ? 'me' : '';
        //   Simple solution for small apps
        const msgHTML = `
            <div class="message-bubble ${side}">
                <div class="message-bubble-inner">
                    <div class="message-avatar"><img src="${img}" alt="${name}" /></div>
                    <div class="message-text" id="msg-${id}">
                        ${typing}
                        <p ${msg_show}>${text}</p>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
          `;

        $msgChat.append(msgHTML);
        $msgChat.animate({
            scrollTop: $msgChat.prop("scrollHeight")
        }, 500);
    }

    function animate_value(id, start, end, duration) {
        if (start === end) return;
        var range = end - start;
        var current = start;
        var increment = end > start? 1 : -1;
        var stepTime = Math.abs(Math.floor(duration / range));
        var obj = document.getElementById(id);
        var timer = setInterval(function() {
            current += increment;
            obj.innerHTML = current;
            if (current == end) {
                clearInterval(timer);
            }
        }, stepTime);
    }
})(jQuery);