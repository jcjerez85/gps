<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            <th>{{ trans('global.device') }}</th>
            <th>{{ trans('validation.attributes.name') }}</th>
            <th>{{ trans('validation.attributes.type') }}</th>
            <th>{{ trans('global.quantity') }}</th>
            <th>{{ trans('validation.attributes.unit_cost') }}</th>
            <th>{{ trans('front.total') }}</th>
            <th>{{ trans('global.date') }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @if (count($expenses))
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->device->name }}</td>
                    <td>{{ $expense->name }}</td>
                    <td>{{ $expense->type->name ?? '' }}</td>
                    <td>{{ $expense->quantity }}</td>
                    <td>{{ $expense->unit_cost }}</td>
                    <td>{{ $expense->total }}</td>
                    <td>{{ Formatter::time()->format($expense->date) }}</td>
                    <td class="actions">
                        <a href="javascript:" class="btn icon edit"
                           data-url="{!!route('device_expenses.edit', [$expense->id])!!}"
                           data-modal="expenses_edit"></a>

                        <a href="{!!route('device_expenses.destroy', [$expense->id])!!}"
                           class="js-confirm-link btn icon delete"
                           data-confirm="{{ trans('admin.do_delete') }}"
                           data-method="DELETE">
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7">{!!trans('front.nothing_found_request')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@if (count($expenses))
    <div class="nav-pagination">
        {!! $expenses->setPath(route('device_expenses.table'))->render() !!}
    </div>
@endif
