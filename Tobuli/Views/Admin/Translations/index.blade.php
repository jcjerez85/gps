@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_translations">

        <div class="panel-heading">
            <div class="panel-title"><i class="icon globe"></i> {!! trans('admin.translations') !!}</div>
        </div>

        <div class="panel-body" data-table>
            <table class="table table-striped">
                @foreach ($languages as $key => $language)
                    <tr>
                        <td><a href="{{ route('admin.translations.show', $key) }}"><img src="{{ asset_flag($key) }}" alt="{{ $key }}"> {{ $language['title'] }}</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop