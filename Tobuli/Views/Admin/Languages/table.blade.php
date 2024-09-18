<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            {!! tableHeader('validation.attributes.active', 'style="width: 1px;"') !!}
            {!! tableHeader('global.language') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @foreach ($languages as $language)
            <tr>
                <td>
                    <span class="label label-sm label-{!! $language['active'] ? 'success' : 'danger' !!}">
                        {!! trans('validation.attributes.active') !!}
                    </span>
                </td>
                <td>
                    <img src="{{ asset_flag($language['key']) }}" />
                    {!! $language['title'] !!}
                </td>
                <td class="actions">
                    <div class="btn-group dropdown droparrow" data-position="fixed">
                        <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:" data-modal="{!! $section !!}_edit" data-url="{!! route("admin.{$section}.edit", $language['key']) !!}">{!! trans('global.edit') !!}</a></li>
                            <li><a href="{!! route("admin.translations.show", $language['key']) !!}">{!! trans('admin.translate') !!}</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>