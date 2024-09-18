@extends('admin::Layouts.default')

@section('content')
    @if (Session::has('messages'))
        <div class="alert alert-success">
            <ul>
                @foreach (Session::get('messages') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel panel-default" id="table_secondary_credentials">

        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="secondary_credentials_create" data-url="{{ route('admin.secondary_credentials.create') }}">
                        <i class="icon user-add" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title"><i class="icon user"></i> {{ trans('front.secondary_credentials') }}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('admin::SecondaryCredentials.table')
        </div>
    </div>
@stop

@section('javascript')
<script>
    tables.set_config('table_secondary_credentials', {
        url: '{{ route("admin.secondary_credentials.table") }}',
        destroy: '{{ route('admin.secondary_credentials.destroy') }}',
    });

    function secondary_credentials_edit_modal_callback() {
        tables.get('table_secondary_credentials');
    }

    function secondary_credentials_create_modal_callback() {
        tables.get('table_secondary_credentials');
    }

    function secondary_credentials_delete_modal_callback() {
        tables.get('table_secondary_credentials');
    }

    function confirmed_action_modal_callback() {
        tables.get('table_secondary_credentials');
    }

</script>
@stop