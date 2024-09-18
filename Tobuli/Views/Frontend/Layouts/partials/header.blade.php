<div id="header" class="folded">
    <nav class="navbar main-navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-header-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                @if ( Appearance::assetFileExists('logo') )
                <a class="navbar-brand" href="/" title="{{ Appearance::getSetting('server_name') }}"><img src="{{ Appearance::getAssetFileUrl('logo') }}"></a>
                @endif
            </div>

            <div class="collapse navbar-collapse" id="bs-header-navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    @if (isAdmin())
                        <li>
                            <a href="{!!route('admin')!!}" role="button" rel="tooltip" data-placement="bottom" title="{!!trans('global.admin')!!}">
                                <span class="icon admin"></span>
                                <span class="text">{!!trans('global.admin')!!}</span>
                            </a>
                        </li>
                    @endif

                    @if (config('addon.external_url') && settings('external_url.enabled') && Auth::user()->perm('external_url', 'view'))
                        <li>
                            <a href="{!! (new \Tobuli\Helpers\TextBuilder\UserExternalUrlBuilder())
                                            ->build(settings('external_url.external_url'), Auth::user()) !!}"
                               target="_blank" role="button" rel="tooltip" data-placement="bottom"
                               title="{!! trans('front.external_url') !!}">
                                <span class="icon external-link"></span>
                                <span class="text">{!!trans('front.external_url')!!}</span>
                            </a>
                        </li>
                    @endif
                   

                    

                    <li>
                        <a href="javascript:void(0)" data-url="{!!route('tools.menu')!!}" data-modal="tools_menu" role="button" rel="tooltip" data-placement="bottom" title="{!!trans('front.tools')!!}">
                            <span class="icon tools"></span>
                            <span class="text">{!!trans('front.tools')!!}</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript::void(0)" data-url="{!!route('my_account_settings.edit')!!}" data-modal="my_account_settings_edit" role="button" rel="tooltip" data-placement="bottom" title="{!!trans('front.setup')!!}">
                            <span class="icon setup"></span>
                            <span class="text">{!!trans('front.setup')!!}</span>
                        </a>
                    </li>

                    @if ( Auth::User()->perm('chat', 'view') )
                    <li>
                        <a href="javascript:" data-url="{!!route('chat.index')!!}" data-modal="chat" role="button" rel="tooltip" data-placement="bottom" title="{!!trans('front.chat')!!}">
                            <span class="icon chat"></span>
                            <span class="text">{!!trans('front.chat')!!}</span>
                            <span id="unread-msg-count" class="badge"></span>
                        </a>
                    </li>
                    @endif

                    <li class="dropdown">
                        <a href="javascript:" class="dropdown-toggle" role="button" id="dropMyAccount" data-toggle="dropdown" rel="tooltip" data-placement="bottom" title="{!!trans('front.my_account')!!}">
                            <span class="icon account"></span>
                            <span class="text">{!!trans('front.my_account')!!}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropMyAccount">
                            <li>
                                <a href="javascript:" data-url="{{ route('subscriptions.index') }}" data-modal="subscriptions_edit">
                                    <span class="icon membership"></span>
                                    <span class="text">{!!trans('front.subscriptions')!!}</span>
                                </a>
                            </li>
                            @if (Auth::User()->perm('custom_device_add', 'view'))
                                <li>
                                    <a href="javascript:" data-url="{{ route('devices.subscriptions') }}" data-modal="device_subscriptions_index">
                                        <span class="icon device"></span>
                                        <span class="text">{!!trans('admin.device_plans')!!}</span>
                                    </a>
                                </li>
                            @elseif (settings('main_settings.enable_device_plans') ?? false)
                            <li>
                                <a href="javascript:" data-url="{{ route('device_plans.index') }}" data-modal="device_plans_index">
                                    <span class="icon device"></span>
                                    <span class="text">{!!trans('admin.device_plans')!!}</span>
                                </a>
                            </li>
                            @endif
                            <li>
                                @if (isPublic())
                                <a href="{{ config('tobuli.frontend_change_password').auth()->user()->email }}">
                                    <span class="icon password"></span>
                                    <span class="text">{!!trans('front.change_password')!!}</span>
                                </a>
                                @else
                                <a href="javascript:" data-url="{{ route('my_account.edit') }}" data-modal="subscriptions_edit">
                                    <span class="icon password"></span>
                                    <span class="text">{!!trans('front.change_password')!!}</span>
                                </a>
                                @endif
                            </li>
                            <li>
                                <a href="{!!route('logout')!!}">
                                    <span class="icon logout"></span>
                                    <span class="text">{!!trans('global.log_out')!!}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="language-selection">
                        <a href="javascript:" data-url="{{ route('languages.index') }}" data-modal="language-selection">
                            <span class="icon">
                                <img src="{{ Language::flag() }}" alt="Language" class="img-thumbnail">
                            </span>
                            <span class="text">{!!trans('global.language')!!}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
