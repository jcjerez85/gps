@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon checklist"></i> {{ trans('front.checklist') }}
@stop

@section('body')
<div class="table-responsive">
    <table class="table table-list table-condensed">
        <thead>
            <tr>
                <th>
                    {{ trans('front.task_completed')}}
                </th>
                <th>
                    {{ trans('front.outcome')}}
                </th>
                <th>
                    {{ trans('front.activity')}}
                </th>
                <th>
                    {{ trans('front.photo')}}
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($checklist->rows as $row)
            <tr>
                <td>
                    {{ $row->completed ? trans('global.yes') : trans('global.no') }}
                </td>
                <td>{{ $row->formatted_outcome }}</td>
                <td>{{ $row->activity }}</td>
                <td>
                    @if ($row->images->count())
                        @foreach ($row->images as $image)
                            <a target="_blank" href="{{ url($image->path) }}">
                                {{ trans('front.preview') }}
                            </a>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop

@section('buttons')
    <a type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.cancel') }}</a>
@stop
