<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            {!! tableHeader('validation.attributes.title') !!}
            {!! tableHeader('validation.attributes.price') !!}
            {!! tableHeader('validation.attributes.duration_value') !!}
            {!! tableHeader('validation.attributes.active') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @if ($items->count())
            @foreach ($items as $item)
                <tr>
                    <td>{!! $item->title !!}</td>
                    <td>{!! $item->price !!}</td>
                    <td>{!! $item->duration_text !!}</td>
                    <td>{!! trans('admin.'.($item->active ? 'yes' : 'no')) !!}</td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:"
                                       data-modal="device_plans_edit"
                                       data-url="{{ route("admin.device_plan.edit", $item->id) }}">
                                        {!! trans('global.edit') !!}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.device_plan.destroy') }}"
                                       class="js-confirm-link"
                                       data-confirm="{!! trans('front.do_delete') !!}"
                                       data-id="{{ $item->id }}"
                                       data-method="DELETE">
                                        {{ trans('global.delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="">
                <td class="no-data" colspan="5">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination")