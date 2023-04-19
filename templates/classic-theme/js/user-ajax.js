jQuery(function ($) {
    "use strict";

    // email resend
    $('.resend').on('click', function (e) { 						// Button which will activate our modal
        var the_id = $(this).attr('id');						//get the id
        // show the spinner
        $(this).html("<i class='fa fa-spinner fa-pulse'></i>");
        $.ajax({											//the main ajax request
            type: "POST",
            data: "action=email_verify&id=" + $(this).attr("id"),
            url: ajaxurl,
            success: function (data) {
                $("span#resend_count" + the_id).html(data);
                //fadein the vote count
                $("span#resend_count" + the_id).fadeIn();
                //remove the spinner
                $("a.resend_buttons" + the_id).remove();

            }
        });
        return false;
    });

    // user login
    $("#login-form").on('submit', function (e) {
        e.preventDefault();
        $("#login-status").slideUp();
        $('#login-button').addClass('button-progress').prop('disabled', true);
        var form_data = {
            action: 'ajaxlogin',
            username: $("#username").val(),
            password: $("#password").val(),
            is_ajax: 1
        };
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: form_data,
            dataType: 'json',
            success: function (response) {
                $('#login-button').removeClass('button-progress').prop('disabled', false);
                if (response.success) {
                    $("#login-status").addClass('success').removeClass('error').html('<p>' + LANG_LOGGED_IN_SUCCESS + '</p>').slideDown();
                    window.location.href = response.message;
                } else {
                    $("#login-status").removeClass('success').addClass('error').html('<p>' + response.message + '</p>').slideDown();
                }
            }
        });
        return false;
    });

    // blog comment with ajax
    $('.blog-comment-form').on('submit', function (e) {
        e.preventDefault();

        var action = 'submitBlogComment';
        var data = $(this).serialize();
        var $parent_cmnt = $(this).find('#comment_parent').val();
        var $cmnt_field = $(this).find('#comment-field');
        var $btn = $(this).find('.button');
        $btn.addClass('button-progress').prop('disabled', true);

        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=' + action,
            data: data,
            dataType: 'json',
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                if (response.success) {
                    if ($parent_cmnt == 0) {
                        $('.latest-comments > ul').prepend(response.html);
                    } else {
                        $('#li-comment-' + $parent_cmnt).after(response.html);
                    }
                    $('html, body').animate({
                        scrollTop: $("#li-comment-" + response.id).offset().top
                    }, 2000);
                    $cmnt_field.val('');
                } else {
                    $('#respond > .widget-content').prepend('<div class="notification error"><p>' + response.error + '</p></div>');
                }
            }
        });
    });

    /* generate content */
    $('#ai_form').on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var action = 'generate_content';
        var data = new FormData(this),
            $form = $(this);

        var $btn = $(this).find('.button'),
            $error = $(this).find('.form-error');
        $btn.addClass('button-progress').prop('disabled', true);

        $error.slideUp();
        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=' + action,
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                if (response.success) {
                    let old_content = tinymce.activeEditor.getContent();
                    if (old_content) {
                        old_content += '<br><br>'
                    }
                    tinymce.activeEditor.setContent(old_content + response.text);
                    tinymce.activeEditor.focus();

                    tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
                    tinyMCE.activeEditor.selection.collapse(false);

                    $('.simplebar-scroll-content').animate({
                        scrollTop: $("#content-focus").offset().top
                    }, 500);

                    animate_value('quick-words-left', response.old_used_words, response.current_used_words, 4000)
                } else {
                    $error.html(response.error).slideDown().focus();
                }
            }
        });
    });

    /* generate speech to text */
    $('#speech_to_text').on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var action = 'speech_to_text';
        var data = new FormData(this),
            $form = $(this);

        var $btn = $(this).find('.button'),
            $error = $(this).find('.form-error');
        $btn.addClass('button-progress').prop('disabled', true);

        $error.slideUp();
        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=' + action,
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                if (response.success) {
                    tinymce.activeEditor.setContent(response.text);
                    tinymce.activeEditor.focus();

                    tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
                    tinyMCE.activeEditor.selection.collapse(false);

                    $('.simplebar-scroll-content').animate({
                        scrollTop: $("#content-focus").offset().top
                    }, 500);

                    animate_value('quick-speech-left', response.old_used_speech, response.current_used_speech, 1000)
                } else {
                    $error.html(response.error).slideDown().focus();
                }
            }
        });
    });

    /* generate code */
    $('#ai_code').on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var action = 'ai_code';
        var data = new FormData(this),
            $form = $(this);

        var $btn = $(this).find('.button'),
            $error = $(this).find('.form-error');
        $btn.addClass('button-progress').prop('disabled', true);

        $error.slideUp();
        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=' + action,
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                if (response.success) {
                    tinymce.activeEditor.setContent(response.text);
                    tinymce.activeEditor.focus();

                    tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
                    tinyMCE.activeEditor.selection.collapse(false);

                    $('.simplebar-scroll-content').animate({
                        scrollTop: $("#content-focus").offset().top
                    }, 500);

                    animate_value('quick-words-left', response.old_used_words, response.current_used_words, 4000)
                } else {
                    $error.html(response.error).slideDown().focus();
                }
            }
        });
    });

    /* save ai document */
    $('#ai_document_form').on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var action = 'save_document';
        var data = new FormData(this),
            $form = $(this);

        var $btn = $(this).find('.button'),
            $error = $(this).find('.form-error');
        $btn.addClass('button-progress').prop('disabled', true);

        $error.slideUp();
        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=' + action,
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                if (response.success) {
                    $form.find('#post_id').val(response.id);
                    Snackbar.show({
                        text: response.message,
                        pos: 'bottom-center',
                        showAction: false,
                        actionText: "Dismiss",
                        duration: 3000,
                        textColor: '#fff',
                        backgroundColor: '#383838'
                    });
                } else {
                    $error.html(response.error).slideDown().focus();
                }
            }
        });
    });

    /* ai images */
    $('#ai_images').on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var action = 'generate_image';
        var data = new FormData(this),
            $form = $(this);

        var $btn = $(this).find('.button'),
            $error = $(this).find('.form-error');
        $btn.addClass('button-progress').prop('disabled', true);

        $error.slideUp();
        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=' + action,
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                if (response.success) {
                    $('#generated_images_notice').hide();

                    $.each(response.data, function( index, value ) {
                        $("#generated_images_wrapper").prepend('<div class="col-sm-4 col-md-2 col-6 margin-bottom-30"><a href="'+ value.large +'" target="_blank"><img class="rounded" src="'+ value.small +'" alt="" data-tippy-placement="top" title="'+ response.description +'"></a></div>')
                    });

                    animate_value('quick-images-left', response.old_used_images, response.current_used_images, 1000)

                    $('.simplebar-scroll-content').animate({
                        scrollTop: $("#content-focus").offset().top
                    }, 500);
                } else {
                    $error.html(response.error).slideDown().focus();
                }
            }
        });
    });

    /* delete ajax */
    $('.quick-delete').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $btn = $(this);
        var action = $btn.data('action');

        if(confirm(LANG_ARE_YOU_SURE)) {
            $btn.addClass('button-progress').prop('disabled', true);
            $.ajax({
                type: "POST",
                url: ajaxurl + '?action=' + action,
                data: {
                    'id': $btn.data('id')
                },
                dataType: 'json',
                success: function (response) {
                    $btn.removeClass('button-progress').prop('disabled', false);
                    if (response.success) {
                        $btn.closest('tr').fadeOut("slow", function(){
                            $(this).remove();
                        })

                        Snackbar.show({
                            text: response.message,
                            pos: 'bottom-center',
                            showAction: false,
                            actionText: "Dismiss",
                            duration: 3000,
                            textColor: '#fff',
                            backgroundColor: '#383838'
                        });
                    }
                }
            });
        }
    });

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
});