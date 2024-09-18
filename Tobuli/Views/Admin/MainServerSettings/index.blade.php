@extends('Admin.Layouts.default')

@section('content')
    @if (Session::has('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach (Session::get('errors')->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (Auth::User()->isReseller())
        <div class="alert alert-info">
            {{ trans('admin.your_branding_url') }}: {{ route('login', Auth::User()->id) }}
        </div>
    @endif

    <div class="row">
        <div class="col-sm-6">
            @if (Auth::User()->isAdmin())
                @include('Admin.MainServerSettings.partials.main')
            @else
                @include('Admin.MainServerSettings.partials.manager')
            @endif
        </div>

        <div class="col-sm-6">
            @include('Admin.MainServerSettings.partials.appearance')
        </div>
    </div>
@stop