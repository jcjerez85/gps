<div class="row">
    <div class="col-xs-10 col-xs-offset-1">

        <div class="content-heading">
            Finish creating your account
        </div>

        {!! Form::open(['route' => ['register.step.store', 'user'], 'method' => 'POST']) !!}
        <div class="form-group">
            {!! Form::label('phone_number', trans('validation.attributes.phone_number').'*', ['class' => 'control-label']) !!}
            <div>
            {!! Form::text('phone_number', Auth::user()->phone_number, ['class' => 'form-control phone_number']) !!}
            </div>
        </div>

        @include('Frontend.CustomFields.panel')

        <hr>

        <button type="submit" class="btn btn-sm btn-primary pull-right">
            {!! trans('global.continue') !!}
        </button>
        {!! Form::close() !!}
    </div>
</div>