@extends('Frontend.Layouts.modal')

@section('title')
    {!!trans('front.choose_language')!!}
@stop

@section('body')
<div class="lang-list">
    @foreach ($languages as $language)
    <div class="lang-item">
        <a href="{{ route('my_account_settings.change_lang', $language['key']) }}" class="btn btn-default btn-block">
            <img src="{{ asset_flag($language['key']) }}" /> {{ $language['title'] }}
        </a>
    </div>
    @endforeach
</div>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.close')!!}</button>
@stop