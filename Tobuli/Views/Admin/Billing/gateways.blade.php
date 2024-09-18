@extends('Admin.Layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            @if (Session::has('billing_success'))
                <div class="alert alert-success">
                    {!! Session::get('billing_success') !!}
                </div>
            @endif
            @if (Session::has('billing_errors'))
                <div class="alert alert-danger">
                    <ul>
                        <?php $errors = is_array(Session::get('billing_errors')) ?
                            Session::get('billing_errors') : Session::get('billing_errors')->all(); ?>

                        @foreach ($errors as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    @foreach(array_chunk($gateways, 2, true) as $chunk)
        <div class="row">
            @foreach($chunk as $gateway => $active)
                @include('Admin.Billing.Gateways.' . $gateway, [
                    'gateway'   => $gateway,
                    'active'    => $active
                 ])
            @endforeach
        </div>
    @endforeach
@stop

@section('javascript')
    <script>
        var endpoint = '{{ route('payments.config_check', 'gateway') }}';

        $('.config-test').click(function (e) {
            var gateway = e.target.id.replace('_test', '');
            $.ajax({
                method: 'GET',
                data: $('#' + gateway).serialize(),
                url: endpoint.replace('gateway', gateway),
                beforeSend: function () {
                    loader.add('div.content');
                },
                success: function (response) {
                    if (response.status === 1) {
                        toastr.success('Config is valid.');
                    } else {
                        toastr.error('Config is invalid. ' + response.error);
                    }
                },
                complete: function () {
                    loader.remove('div.content');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        });

        $('#plan_group_add').click(function () {
            var plan_group = $('.plan_group'),
                clone = plan_group.last().clone(),
                count = plan_group.length;

            clone.find('input').attr('name', 'plan_ids[' + count + ']');
            clone.find('select').attr('name', 'billing_plans[' + count + ']');
            clone.find('select').selectpicker();
            clone.insertBefore('#plan_group_add');

            $('.plan_group_remove').unbind('click').click(removeAction);
        });

        $('.plan_group_remove').click(removeAction);

        function removeAction() {
            if ($('.plan_group_remove').length === 1)
                return;

            $(this).closest('.form-group').remove();
        }
    </script>
@stop