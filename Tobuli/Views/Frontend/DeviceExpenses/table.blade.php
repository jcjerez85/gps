<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            <th>{{ trans('validation.attributes.name') }}</th>
            <th>{{ trans('validation.attributes.type') }}</th>
            <th>{{ trans('global.quantity') }}</th>
            <th>{{ trans('validation.attributes.unit_cost') }}</th>
            <th>{{ trans('front.total') }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @if ($expenses->isEmpty())
            <tr>
                <td colspan="7">{!!trans('front.nothing_found_request')!!}</td>
            </tr>
        @else
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->name }}</td>
                    <td>{{ $expense->type->name ?? '' }}</td>
                    <td>{{ $expense->quantity }}</td>
                    <td>{{ $expense->unit_cost }}</td>
                    <td>{{ $expense->total }}</td>
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
        @endif
        </tbody>
    </table>
</div>

@if (!$expenses->isEmpty())
    <div class="nav-pagination">
        {!! $expenses->setPath(route('device_expenses.table', $device_id))->render() !!}
    </div>
@endif

<div class="total" style="margin-top: 20px">
    {{ trans('front.total') }}: {{ $total ?? '-' }}
</div>
