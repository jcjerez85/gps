@forelse ($groups as $group)
    <div class="group" data-toggle="multiCheckbox" >
        <div class="group-heading">
            <div class="checkbox">
                {!! Form::checkbox(null, $group['id'], $group['active'], ['data-toggle' => 'checkbox']) !!}
                <label></label>
            </div>

            <div class="group-title {{ $group['open'] ? '' : 'collapsed' }}" data-toggle="collapse" data-target="#device-group-{{ $group['id'] }}" data-parent="#objects_tab" aria-expanded="{{ $group['open'] ? 'true' : 'false' }}" aria-controls="device-group-{{ $group['id'] }}">
                {{ $group['title'] }} <span class="count">{{ $group['count'] }}</span>
            </div>

            <div class="btn-group">
                @if ($group['id'])
                    <i class="btn icon options" data-url="{{ route('devices_groups.edit', $group['id']) }}" data-modal="devices_groups_edit"></i>
                @else
                    <i class="btn icon options" data-url="{{ route('devices_groups.create') }}" data-modal="devices_groups_create"></i>
                @endif
            </div>
        </div>

        <div id="device-group-{{ $group['id'] }}" class="group-collapse collapse {{ ! $group['open'] ? '' : 'in' }}" data-id="{{ $group['id'] }}" role="tabpanel" aria-expanded="{{ $group['open'] ? 'true' : 'false' }}">
            <div class="group-body">
                @if(($group['open']))
                    @include('front::Objects.items', ['items' => $group['items']])
                @else
                    <div data-toggle="scroll" data-parent=".tab-pane-body" data-url="{{ $group['next'] }}"></div>
                @endif
            </div>
        </div>
    </div>
@empty
    <p class="no-results">{!! trans('front.no_devices') !!}</p>
@endforelse

@if ($groups->nextPageUrl())
    <div data-toggle="scroll" data-parent=".tab-pane-body" data-url="{{ $groups->nextPageUrl() }}"></div>
@endif