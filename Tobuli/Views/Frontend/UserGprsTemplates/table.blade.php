<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                {!! tableHeader('validation.attributes.title') !!}
                {!! tableHeader('validation.attributes.adapted') !!}
                @if(auth()->user()->perm('device.protocol', 'view'))
                {!! tableHeader('validation.attributes.protocol') !!}
                @endif
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($user_gprs_templates as $user_gprs_template)
            <tr>
                <td>
                    {{ $user_gprs_template->title }}
                </td>
                <td>
                    {{ $user_gprs_template->adapted_title }}
                </td>
                @if(auth()->user()->perm('device.protocol', 'view'))
                    <td>
                        {{ $user_gprs_template->protocol }}
                    </td>
                @endif
                <td class="actions">
                    <a href="javascript:"
                       class="btn icon edit"
                       data-url="{!! route('user_gprs_templates.edit', $user_gprs_template->id) !!}"
                       data-modal="user_gprs_templates_edit">
                    </a>

                    <a href="javascript:"
                       class="btn icon delete"
                       data-url="{!! route('user_gprs_templates.do_destroy', $user_gprs_template->id) !!}"
                       data-modal="user_gprs_templates_destroy">
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td class="no-data" colspan="2">{!!trans('front.no_templates')!!}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $user_gprs_templates->setPath(route('user_gprs_templates.table'))->render() !!}
</div>