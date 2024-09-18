@extends('Frontend.CustomRegistration.layout')

@section('content')
    @include('Frontend.CustomRegistration.partials.panel')
@endsection

@section('scripts')
    <script>
        $('#custom-registration form:not(#payment-form)',).on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);

            $.ajax({
                type: 'POST',
                dataType: "json",
                data: $form.serializeArray(),
                url: $form.attr('action'),
                beforeSend: function () {
                    $form.find('.help-block.error').remove();
                    $form.find('.has-error').removeClass('has-error');
                    loader.add($form);
                },
                success: function (response) {
                    window.location.href = response.next;
                },
                error: function(jqXHR, textStatus) {
                    loader.remove($('.tab-content'));

                    if (typeof jqXHR.responseJSON.errors) {
                        $modal.defaultParseErrors(jqXHR.responseJSON.errors, $form, $form);
                    }
                }
            });
        });

        $('#billing-type-tabs .plan').on('click', function() {
            $(this).find('input[type="radio"]').prop('checked', true).change();
        });

        $('#billing-type-tabs input[name="device_plan_id"]').on('change', function() {
            $("#billing-type-tabs .plan").removeClass('active');
            $(this).closest('.plan').addClass('active');
        });

        $('#billing-type-nav li:first a').trigger('click');
    </script>
@stop