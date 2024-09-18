@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon qrcode"></i> {{ trans('front.checklist_qr_code') }}
@stop

@section('body')
    <div class="action-block">
        <a href="{{ $downloadUrl }}"
           class="btn btn-action"
           type="button"
           target="_blank">
            <i class="icon download"></i> {{ trans('admin.download') }}
        </a>
    </div>
    <img class="center-block" src="{{ route('checklist.qr_code_image', $device_id) }}" />
    <a href="{{ $maintenanceUrl }}"
       class="center-block text-center"
       target="_blank">
        {{ $maintenanceUrl }}
    </a>
@stop

@section('buttons')
    <a type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.cancel') }}</a>
@stop
