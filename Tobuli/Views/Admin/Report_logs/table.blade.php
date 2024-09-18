<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall([
                'prompt_delete_url' => trans('admin.delete_selected'),
                'prompt_delete_all_url' => trans('admin.delete_all'),
            ]) !!}
            {!! tableHeader('validation.attributes.name') !!}
            {!! tableHeader('validation.attributes.type') !!}
            {!! tableHeader('validation.attributes.format') !!}
            {!! tableHeader('admin.size') !!}
            {!! tableHeader('global.user') !!}
            {!! tableHeader('validation.attributes.send_to_email') !!}
            {!! tableHeader('global.is_send') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>
        <tbody>
        @forelse ($logs as $log)
            <tr>
                <td>
                    <div class="checkbox">
                        <input type="checkbox" value="{!! $log->id !!}">
                        <label></label>
                    </div>
                </td>
                <td>
                    {{ $log->title }}
                </td>
                <td>
                    {{ $log->type_text }}
                </td>
                <td>
                    {{ $log->format_text }}
                </td>
                <td>
                    {{ formatBytes( $log->size ) }}
                </td>
                <td>
                    {{ $log->user->email ?? '' }}
                </td>
                <td>
                    {{ $log->email }}
                </td>
                <td>
                    <span title="{{ $log->error }}">{{ $log->is_send ? trans('global.yes') : trans('global.no') }}</span>
                </td>
                <td class="actions">
                    <div class="btn-group dropdown droparrow" data-position="fixed">
                        <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('admin.report_logs.edit', $log->id) }}">{{ trans('admin.download') }}</a></li>
                            <li>
                                <a href="{{ route('admin.report_logs.destroy') }}"
                                   class="js-confirm-link"
                                   data-confirm="{{ trans('admin.do_delete') }}"
                                   data-id="{{ $log->id }}"
                                   data-method="DELETE">
                                    {{ trans('global.delete') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9">
                    {{ trans('admin.no_data') }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination", ['items' => $logs])