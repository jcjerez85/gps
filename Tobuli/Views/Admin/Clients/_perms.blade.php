<table class="table">
    <thead>
    <th style="text-align: left">{{ trans('front.permission') }}</th>
    <th style="text-align: center">{{ trans('front.view') }}</th>
    <th style="text-align: center">{{ trans('global.edit') }}</th>
    <th style="text-align: center">{{ trans('global.delete') }}</th>
    </thead>
    <tbody>
    @foreach($grouped_permissions as $group => $permissions)
        @if($group !== 'main')
            <tr>
                <th colspan="4">
                    <a href="javascript:" data-toggle="collapse" data-target="{{ ".group-$group" }}">
                        {{ ucfirst($group) }}
                        <i class="fa fa-angle-down"></i>
                    </a>
                </th>
            </tr>
        @endif
        @foreach($permissions as $permission => $modes)
            <tr class="{{ "group-$group" }} {{ ($group !== 'main') ? 'collapse' : '' }}">
                <td>
                    @if($group !== 'main')
                        {{ trans('validation.attributes.' . explode('.', $permission)[1]) }}
                    @else
                        {{ trans('front.' . $permission) }}
                    @endif
                </td>
                <td style="text-align: center">
                    <div class="checkbox">
                        @if ($modes['view'])
                            {!! Form::checkbox("perms[$permission][view]", 1, ! empty($permission_values[$permission]['view']), ['class' => 'perm_checkbox perm_view'] + (!empty($plan) ? ['disabled' => 'disabled'] : [])) !!}
                        @else
                            {!! Form::checkbox(null, 0, 0, ['disabled' => 'disabled']) !!}
                        @endif
                        {!! Form::label(null, null) !!}
                    </div>
                </td>
                <td style="text-align: center">
                    <div class="checkbox">
                        @if ($modes['edit'])
                            {!! Form::checkbox("perms[$permission][edit]", 1, ! empty($permission_values[$permission]['edit']), ['class' => 'perm_checkbox perm_edit'] + (!empty($plan) ? ['disabled' => 'disabled'] : [])) !!}
                        @else
                            {!! Form::checkbox(null, 0, 0, ['disabled' => 'disabled']) !!}
                        @endif
                        {!! Form::label(null, null) !!}
                    </div>
                </td>
                <td style="text-align: center">
                    <div class="checkbox">
                        @if ($modes['remove'])
                            {!! Form::checkbox("perms[$permission][remove]", 1, ! empty($permission_values[$permission]['remove']), ['class' => 'perm_checkbox perm_remove'] + (!empty($plan) ? ['disabled' => 'disabled'] : [])) !!}
                        @else
                            {!! Form::checkbox(null, 0, 0, ['disabled' => 'disabled']) !!}
                        @endif
                        {!! Form::label(null, null) !!}
                    </div>
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>

<script>
    $(document).on('change', 'input.perm_checkbox', function () {
        checkPerm($(this));
    });
</script>