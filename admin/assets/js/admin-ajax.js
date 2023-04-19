jQuery(function ($) {
    "use strict";

    /* Datatable Basic */
    var $datatable = $('#basic_datatable'),
        order_by = $datatable.data('order-col') || 0,
        order = $datatable.data('order-dir') || 'asc';

    if($datatable.length) {
        var dt = $datatable.DataTable({
            rowReorder: {
                selector: 'tr .quick-reorder-icon',
                update: false
            },
            order: [[order_by, order]],
            processing: true,
            responsive: true,
            columnDefs: [
                {orderable: false, targets: 'no-sort'}
            ],
            pageLength: 25
        });
        $('div.dataTables_filter input').addClass('form-control');
        $('div.dataTables_length select').addClass('form-control');
    }

    /* Datatable ajax */
    var $datatable = $('#ajax_datatable'),
        order_by = $datatable.data('order-col') || 0,
        order = $datatable.data('order-dir') || 'desc',
        datatable_ajax_url = "datatable-json/" + $datatable.data('jsonfile');

    if($datatable.length) {
        var dt = $datatable.DataTable({
            rowReorder: {
                selector: 'tr .quick-reorder-icon',
                update: false
            },
            order: [[order_by, order]],
            processing: true,
            serverSide: true,
            responsive: true,
            columnDefs: [
                {orderable: false, targets: 'no-sort'}
            ],
            pageLength: 25,
            ajax: {
                url: datatable_ajax_url,
                type: "post",
                error: function () {
                    $("#ajax_datatable_processing").css("display", "none");
                }
            }
        });
        $('div.dataTables_filter input').addClass('form-control');
        $('div.dataTables_length select').addClass('form-control');

        // custom filters for datatable
        $('.quick-datatable-filter').on('change', function () {
            if (datatable_ajax_url.indexOf('?') !== -1) {
                datatable_ajax_url = datatable_ajax_url + "&" + $(this).attr('name') + '=' + $(this).val();
            } else {
                datatable_ajax_url = datatable_ajax_url + "?" + $(this).attr('name') + '=' + $(this).val();
            }
            dt.ajax.url(datatable_ajax_url);
            dt.ajax.reload();
        });

        dt.on('row-reorder', function (e, diff, edit) {
            var data = [];
            $datatable.find('tbody').children('tr').each(function () {
                var $this = $(this);
                var position = $this.attr('id');
                data.push(position);
            });
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {action: $datatable.data('reorder-action'), position: data}
            });
        });
    }

    // Child Datatable - Add event listener for opening and closing details
    $('#ajax_datatable tbody').on('click', 'td .details-row-button', function () {
        var table = $('#ajax_datatable').DataTable();
        var tr = $(this).closest('tr');
        var btn = $(this);
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            $(this).removeClass('icon-feather-minus-square').addClass('icon-feather-plus-square');
            $('div.table_row_slider', row.child()).slideUp( function () {
                row.child.hide();
                tr.removeClass('shown');
            } );
        }
        else {
            // Open this row
            $(this).removeClass('icon-feather-plus-square').addClass('icon-feather-minus-square');
            // Get the details with ajax
            var $jsonfile = $( '#ajax_datatable').data('jsonfile');
            var id = btn.data('entry-id');
            var action = btn.data('entry-action');
            var data = {action: action, id: id};

            $.ajax({
                type: "POST",
                url :"datatable-json/"+$jsonfile,
                data: data
                // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
                // data: {param1: 'value1'},
            })
                .done(function(data) {
                    //console.log("-- success getting table extra details row with AJAX");
                    row.child("<div class='table_row_slider'>" + data + "</div>", 'no-padding').show();
                    tr.addClass('shown');
                    $('div.table_row_slider', row.child()).slideDown();
                })
                .fail(function(data) {
                    // console.log("-- error getting table extra details row with AJAX");
                    row.child("<div class='table_row_slider'>"+LANG_ERROR_LOADING_DETAILS+"</div>").show();
                    tr.addClass('shown');
                    $('div.table_row_slider', row.child()).slideDown();
                })
                .always(function(data) {
                    // console.log("-- complete getting table extra details row with AJAX");
                });
        }
    } );

    /* Sidepanel ajax */
    $(document).on("click", "#post_sidePanel_data", function (e) {
        var $button = $(this),
            $form = $('#sidePanel_form'),
            action = $form.data('ajax-action');

        $button.addClass('quick-loader').prop('disabled',true);
        $form.ajaxSubmit({
            url:  sidepanel_ajaxurl + '?action='+action,
            dataType:  'json',
            success:   function (response) {
                $button.removeClass('quick-loader').prop('disabled',false);
                if (response == 0) {
                    quick_alert("Unknown Error generated.", 'error');
                } else {
                    if (response.status == 'success') {
                        quick_alert(response.message);

                        if(typeof dt !== 'undefined')
                            dt.ajax.reload();
                        else
                            location.reload();

                        $.slidePanel.hide();
                    } else {
                        quick_alert(response.message, 'error');
                    }
                }
            }
        });
    });
    $(document).on("submit", "#sidePanel_form", function (e) {
        // prevent form submit on enter
        e.preventDefault();
        return false;
    });

    /* Site action delete */
    var $delete_button = $('#quick-delete-button');
    $delete_button.on('click', function () {
        if (confirm(LANG_ARE_YOU_SURE)) {
            var $this = $(this),
                action = $this.data('action'),
                data = [],
                $row = [],
                $checkboxes = $('tbody input:checked');

            if ($($checkboxes).length ) {
                $checkboxes.each(function () {
                    var row = $(this).parents('tr');
                    $row.push(row);
                    data.push(this.value)
                });
            }else{
                $checkboxes = $('.quick-accordion input:checked');
                $checkboxes.each(function () {
                    var row = $(this).parents('.quick-accordion-card');
                    $row.push(row);
                    data.push(this.value);
                });
            }


            $this.addClass('quick-loader').prop('disabled',true);
            $.ajax({
                url: ajaxurl + '?action=' + action,
                type: 'POST',
                data: {
                    ids: data
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        quick_alert(response.message);
                        if(typeof dt !== 'undefined'){
                            dt.rows($checkboxes.closest('td')).remove().draw();
                        }else{
                            $.each($row.reverse(), function (index) {
                                $(this).delay(500 * index).fadeOut(200, function () {
                                    $(this).remove();
                                });
                            });
                        }
                    } else {
                        quick_alert(response.message,'error');
                    }
                    $this.removeClass('quick-loader').prop('disabled',false);
                    // hide action button
                    $(".site-action").removeClass('active');
                }
            });
        }
    });

    $(document).on('click','.item-ajax-button', function (e) {
        e.preventDefault();
        var $this = $(this),
            action = $this.data('ajax-action'),
            $item = $this.closest('tr'),
            alert_mesg = $(this).data('alert-message'),
            data = {action: action, id: $item.attr('id')};

        if (confirm(alert_mesg)) {
            $this.addClass('quick-loader').prop('disabled', true);
            $.ajax({
                url: ajaxurl + '?action=' + action,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        if(typeof dt !== 'undefined')
                            dt.ajax.reload();
                        else
                            location.reload();
                        quick_alert(response.message);
                    } else {
                        quick_alert(response.message, 'error');
                    }
                    $this.removeClass('quick-loader').prop('disabled',false);
                }
            });
        }
    });

    /* delete single row */
    $(document).on('click','.item-js-delete', function (e) {
        e.preventDefault();
        var $this = $(this),
            action = $this.data('ajax-action'),
            $item = $this.closest('tr'),
            data = { action: action, id: $item.attr('id') };

        swal({
            title: LANG_ARE_YOU_SURE,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#6b76ff",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function(){
            $('.confirm').addClass('quick-loader').prop('disabled',true);
            $.ajax({
                url: ajaxurl + '?action=' + action,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $item.remove();
                        quick_alert(response.message);
                    } else {
                        quick_alert(response.message,'error');
                    }
                    swal.close();
                    $('.confirm').removeClass('quick-loader').prop('disabled',false);
                }
            });
        });
    });

    /* Position reorder */
    var $reorder_body = $('.quick-reorder-body');
    if($reorder_body.length) {
        $reorder_body.sortable({
            helper: function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            },
            axis: 'y',
            handle: '.quick-reorder-icon',
            update: function (event, ui) {
                var data = [];

                $reorder_body.children('.quick-reorder-element').each(function () {
                    var $this = $(this);
                    var position = $this.data('id');
                    data.push(position);
                });
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {action: $reorder_body.data('action'), position: data},
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        if (response.success) {
                            quick_alert(response.message);
                        } else {
                            quick_alert(response.message, 'error');
                        }
                    }
                });
            }
        });
    }

    /* Post ajax form */
    $(document).on("submit", "#ajax_submit_form, .ajax_submit_form", function (e) {
        e.preventDefault();
        var $form = $(this),
            loader = $form.find('[type="submit"]'),
            sidepanel_ajax = $form.data('ajax-sidepanel') || false,
            options = {
                url:  (sidepanel_ajax ? sidepanel_ajaxurl : ajaxurl) + '?action=' + $form.data('action'),
                dataType:  'json',
                success:   function(response){
                    if(response != 0){
                        quick_alert(response.message);
                        $('#post_id').val(response.id);
                        if ($('#quick-dynamic-modal').length) {
                            $('#quick-dynamic-modal').modal('hide');
                        }
                    } else {
                        quick_alert(response.message, 'error');
                    }
                    loader.removeClass('quick-loader').prop('disabled',false);
                }
            };
        loader.addClass('quick-loader').prop('disabled',true);
        $form.ajaxSubmit(options);
    });

    $(document).on('click','.login-as-user',function (e) {
        e.stopPropagation();
        e.preventDefault();

        //Parameter
        if ($(this).data('redirect-url')) {
            var url = $(this).data('redirect-url');
        }else{
            var url = '';
        }
        var $id = $(this).data('user-id');
        var data = { action: 'loginAsUser', id: $id };
        var $btn = $(this);
        $btn.addClass('quick-loader').prop('disabled',true);
        $.ajax({
            type: 'POST',
            data: data,
            url: ajaxurl,
            success: function (response) {
                $btn.removeClass('quick-loader').prop('disabled',false);
                if(url!=''){
                    var win = window.open(url, '_blank');
                    win.focus();
                }else{
                    if(response != 0) {
                        var win = window.open(response, '_blank');
                        win.focus();
                    }
                }
            }
        });
    });

    /* Payment Install */
    $(document).on('click', '.install-payment', function(e) {
        e.stopPropagation();
        e.preventDefault();

        var action = $(this).data('ajax-action'),
            folder = $(this).data('folder'),
            $item = $(this).closest('tr'),
            data = { action: action, id: $item.attr('id'), folder: folder};

        swal({
            title: LANG_ARE_YOU_SURE,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#6b76ff",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function(){
            $('.confirm').prop('disabled',true);
            $.ajax({
                type: 'POST',
                data: data,
                dataType:  'json',
                url: ajaxurl+'?action='+action,
                success: function (response) {
                    if(response.success) {
                        dt.ajax.reload();
                        quick_alert(response.message);
                        swal.close();
                    }else{
                        quick_alert(response.message, 'error');
                    }
                    $('.confirm').prop('disabled',false);
                },
                error: function () {
                    swal("Error!", LANG_PROBLEM_INSTALLATION, "error");
                    $('.confirm').prop('disabled',false);
                }
            });
        });
    });

    /* Payment Uninstall */
    $(document).on('click', '.uninstall-payment', function(e) {
        e.stopPropagation();
        e.preventDefault();

        //Parameter
        var action = $(this).data('ajax-action'),
            $item = $(this).closest('tr'),
            data = { action: action, id: $item.attr('id') };
        swal({
            title: LANG_ARE_YOU_SURE,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#6b76ff",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function(){
            $('.confirm').prop('disabled',true);
            $.ajax({
                type: 'POST',
                data: data,
                dataType:  'json',
                url: ajaxurl+'?action='+action,
                success: function (response, textStatus, jqXHR) {
                    if(response.success) {
                        dt.ajax.reload();
                        quick_alert(response.message);
                        swal.close();
                    }else{
                        quick_alert(response.message, 'error');
                    }
                    $('.confirm').prop('disabled',false);
                },
                error: function () {
                    swal("Error!", LANG_PROBLEM_UNINSTALL, "error");
                    $('.confirm').prop('disabled',false);
                }
            });
        });

    });

    /*Language File text editor*/
    $("#refresh_list").on('click',function(){
        $('#refresh_list').addClass('bookme-progress');
    });
    $('#js-table-list').on('click', '.editbutton', function(e) {
        var $item = $(this).siblings('div');
        var id = $item.data('id');
        $item.show();
        $(this).hide();
        $('.langtitle_'+id).hide();
    });

    $('#js-table-list').on('click', '.cancelbutton', function(e) {
        var $item = $(this).closest('div');
        var id = $item.data('id');
        $item.hide();
        $('.editbutton').show();
        $('.langtitle_'+id).show();
    });

    $('#js-table-list').on('click', '.savebutton', function(e) {
        var $item = $(this).closest('div');
        var id = $item.data('id');

        var key = $item.find("input[name='newlang_key']").val();
        var file_name = $item.find("input[name='langfile_name']").val();
        var value = $item.find("input[name='newlang_value']").val();
        var action = 'editLanguageFile';
        var data = { action: action, key: key, value: value, file_name: file_name };

        $('.savebutton').addClass('bookme-progress');

        $.post(ajaxurl+'?action='+action, data, function(response) {
            // Remove Ads item from DOM.
            if(response != 0) {
                $item.hide();
                $('.editbutton').show();
                $('.langtitle_'+id).html(value).show();
                quick_alert(LANG_VARIABLE_EDITED);
            }else{
                quick_alert(LANG_PROBLEM_EDIT,'error');
            }
            $('.savebutton').removeClass('bookme-progress');
        });
    });


    /* Modal content */
    $(document).on('click', '.quick-modal-trigger', function(e) {
        e.stopPropagation();
        e.preventDefault();

        $('#quick-dynamic-modal #displayData').html('');
        $('#quick-dynamic-modal').modal('show');
        $('#quick-dynamic-modal .loader').show();
        var id = $(this).data('type-id'),
            data = { id: id},
            action = $(this).data('action');

        $.post(ajaxurl+'?action='+action, data, function(response) {
            if(response != "") {
                $('#quick-dynamic-modal #displayData').html(response);
                $('#quick-dynamic-modal .loader').hide();
            }else{
                quick_alert(LANG_ERROR_LOADING_DETAILS, 'error');
            }
        });
        return false;
    });

    /* popover */
    var $quick_popover = $('.quick-popover'),
        $popover_form = $('#' + $quick_popover.data('form'));
    $quick_popover.popover({
        html: true,
        placement: 'bottom',
        content: $popover_form.show().detach(),
        trigger: 'manual'
    }).on('click', function () {
        $(this).popover('toggle');
    }).on('shown.bs.popover', function () {
        // focus input
        $popover_form.find('input, textarea').first().focus();
    });
    $popover_form.on('click', '.cancel-popover', function (e) {
        $quick_popover.popover('hide');
    });
    $popover_form.on('submit', function (e) {
        e.preventDefault();
        var $form = $(this),
            loader = $('.submit-form'),
            options = {
                url:  ajaxurl + '?action=' + $form.data('action'),
                dataType:  'json',
                success:   function(response){
                    if(response != 0){
                        if(typeof dt !== 'undefined')
                            dt.ajax.reload();
                        else
                            location.reload();
                        quick_alert(response.message);
                        popover_reload();
                    } else {
                        quick_alert(LANG_UNEXPECTED_ERROR, 'error');
                    }
                    $quick_popover.popover('hide');
                    loader.removeClass('quick-loader').prop('disabled',false);
                }
            };

        loader.addClass('quick-loader').prop('disabled',true);
        $form.ajaxSubmit(options);
    });
    function popover_reload(){
        var $popover_reload = $('.popover-reload');
        if($popover_reload.length) {
            $popover_reload.addClass('quick-loader').addClass('quick-loader-dark');
            $.get('accordions/' + $popover_reload.data('url'), function (response) {
                    $popover_reload.removeClass('quick-loader').removeClass('quick-loader-dark');
                    $popover_reload.html(response);
                }
            );
        }
    }
    popover_reload();

});