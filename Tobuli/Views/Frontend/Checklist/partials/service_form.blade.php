<div class="group" data-id="{{ $item->id }}">
    <div class="group-heading">
        <div class="group-title collapsed" data-toggle="collapse" data-target="#checklist-{{ $item->id }}" aria-expanded="false" aria-controls="checklist-{{ $item->id }}">
            <i class="icon {{ $item->type == 1 ? 'pre-checklist' : 'checklist' }}"
                title="{{ $item->typeName }}"></i>{{ $item->name }}
            <span class="pull-right">{{ trans('front.last_completed') }}: <span class="time_completed">{{ Formatter::time()->human($item->completed_at) }}</span></span>
        </div>
        @if (Auth::user()->perm('checklist', 'remove'))
        <div class="btn-group dropleft droparrow pull-right" data-position="fixed">
            <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
            <ul class="dropdown-menu">
                <li>
                    <a href="javascript:" data-url="{{ route('checklists.do_destroy', $item->id) }}" data-modal="checklist_delete">
                        <span class="icon remove"></span>
                        <span class="text">{{ trans('global.delete') }}</span>
                    </a>
                </li>
            </ul>
        </div>
        @endif
    </div>

    <div id="checklist-{{ $item->id }}" class="group-collapse collapse" aria-expanded="false">
        <div class="group-body">
            <table class="table table-list table-condensed">
                <thead>
                    <tr>
                        <th class="table-checkbox">
                            {{ trans('front.task_completed')}}
                        </th>
                        <th class="table-checkbox">{{ trans('global.pass')}}</th>
                        <th class="table-checkbox">{{ trans('global.fail')}}</th>
                        <th>
                            {{ trans('front.activity')}}
                        </th>
                        <th class="text-right col-xs-2">
                            {{ trans('front.photo')}}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item->rows as $row)
                        @include('Frontend.Checklist.partials.service_row')
                    @endforeach
                </tbody>
            </table>
            @if($item->notes)
                <p>{{ $item->notes }}</p>
            @endif
        </div>

        <div class="form-group">
            {!! Form::label("notes[$item->id]", trans('validation.attributes.notes').':') !!}
            {!! Form::textarea("notes[$item->id]", $item->notes, ['class' => 'form-control']) !!}
        </div>

        <div class="signature-wrapper hidden">
            <div class="form-group">
                {!!Form::label("signature[$item->id]", trans('validation.attributes.signature').':')!!}
                <div class="input-group">
                    {!!Form::text("signature[$item->id]", $item->signature, ['class' => 'form-control signature'])!!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default sign">{{ trans('front.sign') }}</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
