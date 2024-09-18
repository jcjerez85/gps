@extends('Admin.Layouts.default')

@section('content')
    <style>
        textarea.error {border-color: #a94442 !important;}
    </style>

    <div class="panel panel-default" id="table_translations">
        <div class="panel-heading">
            <div class="panel-title"><img src="{{ asset_flag($lang) }}" alt="{{ $lang }}"> {{ $language['title'] }}</div>

            <div class="panel-form">
                <div class="form-group">
                    {!! Form::select('tfile', $files, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it')]) !!}
                </div>
            </div>
        </div>

        <div class="panel-body">
            {!! Form::hidden('lang', $lang) !!}

            <table class="table table-striped" id="table-trans" style="table-layout: fixed;">
                <thead>
                <th>English</th>
                <th>{{ trans('front.original') }}</th>
                <th>{{ trans('front.current') }}</th>
                </thead>
                <tbody id="trans-ajax" class="form"></tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $(document).on('click', '.save-changes', function () {
            var controlsWrapper = $(this).closest('.controls-wrapper');
            var input = $(controlsWrapper).siblings('textarea');

            if (input.length > 0) {
                var lang = $('input[name="lang"]').val();
                var data = $(input).serializeArray();

                $.ajax({
                    type: 'POST',
                    dataType: "json",
                    url: '{{ route('admin.translations.save') }}?lang=' + lang,
                    data: data,
                    beforeSend: function () {
                        var parent = input.closest('td');

                        $(parent).find('.help-block.error').remove();
                        $(input).removeClass('error');
                        controlsWrapper.attr('disabled', 'disabled').hide();
                    },
                    success: function (res) {
                        if (res.status) {
                            toastr.success(res.message);
                        } else {
                            var parent = input.closest('td');
                            parent.append('<span class="help-block error"></span>');
                            var errorBlock = $(parent).find('.help-block.error');

                            $(errorBlock).append(res.message+'<br>');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        handlerFail(jqXHR, textStatus, errorThrown);

                        if ('responseJSON' in jqXHR) {
                            var parent = input.closest('td');
                            parent.append('<span class="help-block error"></span>');
                            var errorBlock = $(parent).find('.help-block.error');

                            $.each(jqXHR.responseJSON.errors, function(index, errors) {
                                if (Array.isArray(errors)) {
                                    $.each(errors, function(index, error) {
                                        $(errorBlock).append(error+'<br>');
                                    });
                                } else {
                                    $(errorBlock).append(errors+'<br>');
                                }
                            });
                        }
                    },
                    complete: function () {
                        $(input).text($(input).val());
                        $(controlsWrapper).find('.cancel-changes').removeData('default');
                        controlsWrapper.removeAttr('disabled');
                    }
                });
            }
        });

        $(document).on('click', '.cancel-changes', function () {
            var controlsWrapper = $(this).closest('.controls-wrapper');
            var input = $(controlsWrapper).siblings('textarea');
            var defaultValue = $(this).data('default');

            if (typeof defaultValue !== 'undefined') {
                $(input).val(defaultValue);
                $(this).removeData('default');
                $(controlsWrapper).hide();
            }
        });

        $(document).on('keyup', 'textarea', function() {
            var defaultValue = this.defaultValue;
            var currValue = this.value;
            var controlsWrapper = $(this).siblings('.controls-wrapper');
            var cancelButton = $(controlsWrapper).find('.cancel-changes');
            var savedValue = $(cancelButton).data('default');

            if (typeof savedValue === 'undefined') {
                $(cancelButton).data('default', defaultValue);
                $(controlsWrapper).show();
            } else if (savedValue == currValue) {
                $(cancelButton).removeData('default');
                $(controlsWrapper).hide();
            }
        });

        $(document).on('change', 'select[name="tfile"]', function () {
            var el = $(this);
            var lang = $('input[name="lang"]').val();
            var file = el.val();
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.translations.file_trans') }}',
                data: {
                    lang: lang,
                    file: file
                },
                beforeSend: function () {
                    el.attr('disabled', 'disabled');
                },
                success: function (res) {
                    $('#trans-ajax').html(res);
                },
                complete: function () {
                    el.removeAttr('disabled');
                    el.selectpicker('refresh');
                }
            });
        });

        $(document).on('keyup', 'input[name="search_phrase"]', function () {
            var phrase = $(this).val().toLowerCase();
            var rows = $('#trans-ajax tr');

            if (phrase.length > 0) {
                $.each(rows, function(index, row) {
                    var show = false,
                        $row = $(row);

                    $.each($row.find('td'), function(index, cell) {
                        var $cell = $(cell);

                        if ($cell.has('textarea').length) {
                            if ($cell.find('textarea').val().toLowerCase().includes(phrase)) {
                                show = true;

                                return false;
                            }

                            if ($cell.find('textarea').attr('data-key').toLowerCase().includes(phrase)) {
                                show = true;

                                return false;
                            }

                        } else {
                            if ($cell.text().toLowerCase().includes(phrase)) {
                                show = true;

                                return false;
                            }
                        }
                    });

                    if (show) {
                        $row.show();
                    } else {
                        $row.hide();
                    }
                });
            } else {
                rows.show();
            }
        });

        $('select[name="tfile"]').trigger('change');

    </script>
@stop