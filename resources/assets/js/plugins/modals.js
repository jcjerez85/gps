$(document).ready(function() {
    $(document).on('hidden', '.modal', function(){
        // Todo
    });

    // Open on click
    $(document).on('click', '[data-modal]', function(){
        var data = $(this).data(),
            method = (typeof data.method == 'undefined' ? 'GET' : data.method),
            modal = $modal.initModal(data.modal);

        $(this).tooltip('hide');

        modal.on('hidden', function(){
            modal.remove();
        });

        $modal.getModalContent(data, method, modal);
    });

    $(document).on('click', '[data-submit="modal"],.modal .update:visible, .modal .update_hidden, .modal .update_with_files:visible', function(){
        var element = $(this);
        var modal = element.closest('.modal');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var withFiles = form.find('input[type="file"]').length ? true : false;

        if (withFiles) {
            var data = new FormData(form['0']);
        } else {
            var data = form.serializeArray();
        }

        /*
        form.find('.phone_number').each(function () {
            var _input = $(this),
                _name = _input.attr('name'),
                country = _input.intlTelInput("getSelectedCountryData");

            console.log(country);

            if (_name && country) {
                for (let i = 0; i < data.length; ++i) {
                    if (data[i].name === _name) {
                        data[i].value = '+' + country.dialCode + _input.val();
                        break;
                    }
                }
            }
            console.log( this);
        });
         */

        method = (typeof method != 'undefined' ? method : form.attr('method'));

        $modal.postData(url, method, modal, data, withFiles);
    });
});

var $modal = {
    initModal: function(modal) {
        var element = $('#' + modal);
        if (!element.length) {
            $('body').append('<div class="modal fade" id="' + modal + '"><div class="contents"></div></div>');
            element = $('#' + modal);
        }

        return element;
    },
    getModalContent: function(data, method, modal) {

        $.each(data, function(index,value)
        {
            if (value instanceof Array)
                return;

            if (typeof value === 'object')
                delete data[index];
        });


        $.ajax({
            type: method,
            dataType: "html",
            url: data.url,
            data: data,
            beforeSend: function() {
                loader.add( $('body') );
            },
            success: function(res){
                modal.find('.contents').html(res);
                modal.modal('show');

                initComponents( modal );

                $modal.initEvent(modal, 'content-success');
            },
                complete: function() {
                loader.remove( $('body') );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $modal.initEvent(modal, 'content-error');
                handlerFailModal(jqXHR, textStatus, errorThrown);
            }
        });
    },
    postData: function(url, method, modal, data, with_files, response_callback) {
        if (method === 'PUT' || method === 'DELETE') {

            if (data instanceof FormData)
                data.append('_method', method);

            method = 'POST';
        }

        var modal_content = modal.find('.modal-content').length ? modal.find('.modal-content') : modal;

        var ajax = {
            type: method,
            dataType: "json",
            url: url + (url.includes('?') ? '&' : '?') + '_=' + $.now(),
            data: data,
            beforeSend: function() {
                modal.find('.help-block.error').remove();
                modal.find('.has-error').removeClass('has-error');

                loader.add( modal_content );
            },
            success: function(res){
                if (res.status != 0) {
                    if (res.status == 1) {
                        modal.modal('hide');

                        if (response_callback) {
                            response_callback(res);
                        }
                    }

                    $modal.initEvent(modal, 'post-success');
                    $modal.initCallback(res, modal.attr('id'));
                }

                if (res.trigger) {
                    $(document).trigger(res.trigger, res);
                }

                $modal.executeActions(data);
            },
            complete: function(jqXHR, textStatus) {
                loader.remove( modal_content );

                if (typeof jqXHR.responseJSON.errors) {
                    $modal.initEvent(modal, 'post-error');
                    $modal.parseErrors(jqXHR.responseJSON.errors, modal);
                    $modal.initErrorCallback(jqXHR.responseJSON, modal.attr('id'));
                }

                if (typeof jqXHR.responseJSON.warnings != 'undefined') {
                    $modal.initEvent(modal, 'post-warning');
                    $modal.parseWarnings(jqXHR.responseJSON.warnings, modal);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $modal.initEvent(modal, 'post-error');
                handlerFail(jqXHR, textStatus, errorThrown);
            }
        };

        if (with_files === true) {
            $.extend(ajax, {
                processData: false,
                contentType: false
            });
        }

        $.ajax(ajax);
    },

    executeActions: function (data) {
        if (data instanceof FormData) {
            data = data.serializeArray();
        }

        data = normalizeSerializedArray(data);

        if (data.hasOwnProperty('_action_filters')) {
            actions.execute(data._action_filters ? data._action_filters : {});
        }
    },

    parseErrors: function(errors, modal) {
        $modal.defaultParseErrors(errors, modal);
    },

    parseWarnings: function (warnings, modal) {
        var el = modal.find('.alert-warning');

        if (el.length < 1) {
            modal.find('.modal-body').append(
                '<div class="alert alert-warning main-alert" role="alert" style="display: none;">\n' +
                '   <div id="warnings_accordion" role="tablist" aria-multiselectable="true" hidden>\n' +
                '      <a class="icon ico-arrow-down pull-right" role="button" data-toggle="collapse" data-parent="#warnings_accordion" href="#collapse_warnings" aria-controls="collapse_warnings"></a>\n' +
                '      <div id="collapse_warnings" class="collapse out" role="tabpanel" hidden></div>\n' +
                '   </div>\n' +
                '</div>'
            );

            el = modal.find('.alert-warning');
        } else {
            el.children().not('#warnings_accordion').remove();
            el.find('#collapse_warnings').empty();
        }

        $.each(warnings, function (key, value) {
            if (key == 3) {
                el = modal.find('#collapse_warnings');
                modal.find('#warnings_accordion').show();
            }

            el.append($("<span class='warning'>" + value + "<br></span>"));
        });
    },
    defaultParseErrors: function(errors, modal, errorContainer) {
        errorContainer = errorContainer || '.modal-body';

        $.each( errors, function( key, value ) {

            var name = key,
                parts = key.split('.');

            if (parts.length > 1)
                name = parts.shift()+ '[' + parts.join('][') + ']';

            var error_block = '<span class="help-block error input_error_' + name + '">' + value + '</span>',
                el = modal.find('input[name="' + name + '"]:not([type="radio"]), select[name="' + name + '"], select[name="' + name + '[]"], textarea[name="' + name + '"], input[name="' + name + '_fake"]:not([type="radio"]), select[name="' + name + '_fake"], textarea[name="' + name + '_fake"]');

            if (el.length > 0) {
                if (el.is(':visible') && el.attr('type') != 'hidden') {
                    el.after(error_block);
                    el.closest('.form-group').addClass('has-error');
                } else {
                    var $form_group = el.closest('.form-group');

                    if ($form_group.length > 0)
                        $form_group.append(error_block).addClass('has-error');
                    else
                        el.after(error_block);
                }

                var tab = el.closest('.tab-pane').attr('id');
                if (typeof tab != 'undefined') {
                    modal.find('a[href="#' + tab + '"]').addClass('has-error');
                }
            } else {
                $(errorContainer, modal).prepend(error_block);
            }
        });

        if (modal.find('.help-block.error').length > 0) {
            var fromTop = modal.find('.help-block.error').first().position().top;
            if (fromTop == 0)
                fromTop = modal.find('.help-block.error').first().offsetParent().position().top;
        }
    },
    initCallback: function(res, id) {
        var fnc = id.replace('-','_') + '_modal_callback';
        var callback_fnc = window[fnc];

        if (typeof callback_fnc == 'function') {
            callback_fnc(res);
        }
    },
    initErrorCallback: function(res, id) {
        var fnc = id.replace('-','_') + '_modal_error_callback';
        var callback_fnc = window[fnc];

        if (typeof callback_fnc == 'function') {
            callback_fnc(res);
        }
    },
    initEvent: function(modal, type) {
        $(document).trigger('modal-' + modal.attr('id').replace('_','-') + '-' + type);
    }
}
