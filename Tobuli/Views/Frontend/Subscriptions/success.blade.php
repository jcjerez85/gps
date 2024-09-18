@extends('Frontend.Layouts.default')

@section('header-menu-items')
    @if ( Auth::User() )
        <li>
            <a href="{{ route('logout') }}">
                <i class="icon logout"></i> <span class="text">{{ trans('global.log_out') }}</span>
            </a>
        </li>
    @endif
@stop

@section('content')

    <div class="center-vertical">
        <div class="container">
            <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <div class="panel">
                    <div class="panel-body">
                        <div class="alert alert-success">
                            {!! $message !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function () {
            window.location.href = "{{ route('objects.index') }}";
        }, 2000);
    </script>
@stop