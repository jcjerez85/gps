<?php

use Illuminate\Support\Arr;

function getNavigation() {

    $stats = Cache::remember(Auth::User()->id. '_online_cache', 60, function() {

        $total_devices = Auth::User()
            ->accessibleDevices()
            ->count('devices.id');

        $online_devices = Auth::User()
            ->accessibleDevices()
            ->online(config('tobuli.device_offline_minutes'))
            ->count('devices.id');

        $total_users = \Tobuli\Entities\User::userAccessible(Auth::User())->count();

        return [
            'total_devices'  => $total_devices,
            'online_devices' => $online_devices,
            'total_users'    => $total_users
        ];
    });

    $currentRoute = Route::getCurrentRoute()->getName();
    $childs = [];

    $items = [
        [
            'title' => '<i class="icon map"></i> ' . '<span class="text">' .  trans('admin.map') . '</span>',
            'route' => 'objects.index',
            'childs' => []
        ],
    ];

    #Users
    if (Auth::User()->can('view', new \Tobuli\Entities\User())) {
        $items[] = [
            'title' => '<i class="icon users"></i> ' . '<span class="text">' . trans('admin.users') . ' (' . Arr::get($stats, 'total_users', 0) . ')</span>',
            'route' => 'admin.clients.index',
            'childs' => []
        ];
    }

    #Objects
    if (Auth::User()->can('view', new \Tobuli\Entities\Device())) {
        $items[] = [
            'title' => '<i class="icon device"></i> ' . '<span class="text">' . trans('admin.objects') . ' (' . Arr::get($stats, 'online_devices', 0) . '/' . Arr::get($stats, 'total_devices', 0) . ')</span>',
            'route' => 'admin.objects.index',
            'childs' => []
        ];
    }

    if (Auth::user()->canChangeAppearance()) {
        $items['content'] = [
            'title' => '<i class="icon content"></i> ' . '<span class="text">' .  trans('admin.content') . '</span>',
            'route' => '',
            'childs' => [
                [
                    'title' =>  '<span class="text">' . trans('admin.email_templates') . '</span>',
                    'route' => 'admin.email_templates.index',
                    'childs' => ''
                ],
                [
                    'title' =>  '<span class="text">' . trans('front.sms_templates') . '</span>',
                    'route' => 'admin.sms_templates.index',
                    'childs' => ''
                ],
            ]
        ];

        if (config('addon.notification_popops')) {
            $items['content']['childs'][] = [
                'title' =>  '<span class="text">' . trans('admin.popups') . '</span>',
                'route' => 'admin.popups.index',
                'childs' => ''
            ];
        }

        $items[] = [
            'title' => '<i class="icon setup"></i> ' . '<span class="text">' .  trans('validation.attributes.logo') . '</span>',
            'route' => 'admin.main_server_settings.index',
            'childs' => []
        ];
    }

    if (Auth::user()->isAdmin()) {
        $items[] = [
            'title' => '<i class="icon events"></i> ' . '<span class="text">' .  trans('admin.events') . '</span>',
            'route' => 'admin.events.index',
            'childs' => []
        ];

        $items['content'] = [
            'title' => '<i class="icon content"></i> ' . '<span class="text">' .  trans('admin.content') . '</span>',
            'route' => '',
            'childs' => [
                [
                    'title' =>  '<span class="text">' . trans('admin.email_templates') . '</span>',
                    'route' => 'admin.email_templates.index',
                    'childs' => ''
                ],
                [
                    'title' =>  '<span class="text">' . trans('front.sms_templates') . '</span>',
                    'route' => 'admin.sms_templates.index',
                    'childs' => ''
                ],
                [
                    'title' =>  '<span class="text">' . trans('admin.map_icons') . '</span>',
                    'route' => 'admin.map_icons.index',
                    'childs' => ''
                ],
                [
                    'title' =>  '<span class="text">' . trans('admin.device_icons') . '</span>',
                    'route' => 'admin.device_icons.index',
                    'childs' => ''
                ],
                [
                    'title' =>  '<span class="text">' . trans('admin.expenses_types') . '</span>',
                    'route' => 'admin.device_expenses_types.index',
                    'childs' => ''
                ],
                [
                    'title' =>  '<span class="text">' . trans('admin.pages') . '</span>',
                    'route' => 'admin.pages.index',
                    'childs' => ''
                ],
                [
                    'title' => '<span class="text">' . trans('front.device_configuration') . '</span>',
                    'route' => 'admin.device_config.index',
                    'childs' => '',
                ],
                [
                    'title' => '<span class="text">' . trans('front.apn_configuration') . '</span>',
                    'route' => 'admin.apn_config.index',
                    'childs' => '',
                ],
                [
                    'title' => '<span class="text">' . trans('admin.diem_rates') . '</span>',
                    'route' => 'admin.diem_rates.index',
                    'childs' => '',
                ],
                [
                    'title' => '<span class="text">' . trans('admin.companies') . '</span>',
                    'route' => 'admin.companies.index',
                    'childs' => '',
                ],
                [
                    'title' => '<span class="text">' . trans('front.command_templates') . '</span>',
                    'route' => 'admin.command_templates.index',
                    'childs' => '',
                ],
            ]
        ];

        if (Auth::user()->perm('checklist_template', 'view')) {
            $items['content']['childs'][] = [
                'title' =>  '<span class="text">' . trans('admin.checklist_templates') . '</span>',
                'route' => 'admin.checklist_template.index',
                'childs' => ''
            ];
        }

        if (config('addon.custom_fields')) {
            $items['content']['childs'][] = [
                'title' => '<span class="text">' . trans('admin.device_custom_fields') . '</span>',
                'route' => 'admin.custom_fields.device.index',
                'childs' => '',
            ];
            $items['content']['childs'][] = [
                'title' => '<span class="text">' . trans('admin.user_custom_fields') . '</span>',
                'route' => 'admin.custom_fields.user.index',
                'childs' => '',
            ];
        }

        if (config('addon.forwards')) {
            $items['content']['childs'][] = [
                'title' => '<span class="text">' . trans('admin.forwards') . '</span>',
                'route' => 'admin.forwards.index',
                'childs' => '',
            ];
        }

        if (config('addon.notification_popops')) {
            $items['content']['childs'][] = [
                'title' =>  '<span class="text">' . trans('admin.popups') . '</span>',
                'route' => 'admin.popups.index',
                'childs' => ''
            ];
        }

        if (config('auth.secondary_credentials') && Auth::user()->isMainLogin()) {
            $items['content']['childs'][] = [
                'title' =>  '<span class="text">' . trans('front.secondary_credentials') . '</span>',
                'route' => 'admin.secondary_credentials.index',
                'childs' => ''
            ];
        }

        $items['setup'] = [
                'title' => '<i class="icon setup"></i>' . '<span class="text">' .  trans('front.setup') . '</span>',
                'route' => '',
                'childs' => [
                    [
                        'title' =>  '<span class="text">' . trans('validation.attributes.email') . '</span>',
                        'route' => 'admin.email_settings.index',
                        'childs' => ''
                    ],
                    [
                        'title' =>  '<span class="text">' . trans('front.main_server_settings') . '</span>',
                        'route' => 'admin.main_server_settings.index',
                        'childs' => ''
                    ],
                    [
                        'title' =>  '<span class="text">' . trans('validation.attributes.user') . '</span>',
                        'route' => 'admin.billing.index',
                        'childs' => ''
                    ],
                    [
                        'title' => '<span class="text">' .  trans('admin.billing_gateway') . '</span>',
                        'route' => 'admin.billing.gateways',
                        'childs' => []
                    ],
                    [
                        'title' =>  '<span class="text">' . trans('admin.tracking_ports') . '</span>',
                        'route' => 'admin.ports.index',
                        'childs' => ''
                    ],
                    [
                        'title' =>  '<span class="text">' . trans('admin.languages') . '</span>',
                        'route' => 'admin.languages.index',
                        'childs' => ''
                    ],
                    [
                        'title' =>  '<span class="text">' . trans('admin.blocked_ips') . '</span>',
                        'route' => 'admin.blocked_ips.index',
                        'childs' => ''
                    ],
                    [
                        'title' =>  '<span class="text">' . trans('front.tools') . '</span>',
                        'route' => 'admin.tools.index',
                        'childs' => ''
                    ],
                    [
                        'title' =>  '<span class="text">' . trans('admin.plugins') . '</span>',
                        'route' => 'admin.plugins.index',
                        'childs' => ''
                    ],
                ]
            ];

            $items['setup']['childs'][] = [
                'title' =>  '<span class="text">' . trans('admin.sensor_groups') . '</span>',
                'route' => 'admin.sensor_groups.index',
                'childs' => ''
            ];

            $items['setup']['childs'][] = [
                'title' =>  '<span class="text">' . trans('front.sms_gateway') . '</span>',
                'route' => 'admin.sms_gateway.index',
                'childs' => ''
            ];

            $items['setup']['childs'][] = [
                'title' => '<span class="text">' .  trans('admin.device_plans') . '</span>',
                'route' => 'admin.device_plan.index',
                'childs' => []
            ];

            if (config('addon.external_url')) {
                $items['setup']['childs'][] = [
                    'title' => '<span class="text">' . trans('front.external_url') . '</span>',
                    'route' => 'admin.external_url.index',
                    'childs' => []
                ];
            }

            if (config('addon.device_type')) {
                $items['setup']['childs'][] = [
                    'title' => '<span class="text">' . trans('admin.device_types') . '</span>',
                    'route' => 'admin.device_type.index',
                    'childs' => []
                ];
            }
            if (config('addon.custom_device_add')) {
                $items['setup']['childs'][] = [
                    'title' => '<span class="text">' . trans('admin.device_type_imei') . '</span>',
                    'route' => 'admin.device_type_imei.index',
                    'childs' => []
                ];
            }

//            $items['setup']['childs'][] = [
//                'title' => '<span class="text">' . trans('front.media_categories') . '</span>',
//                'route' => 'admin.media_category.index',
//                'childs' => []
//            ];

            $childs[] = [
                'title' =>  '<span class="text">' . trans('admin.tracker_logs') . '</span>',
                'route' => 'admin.logs.index',
                'childs' => ''
            ];

            $childs[] = [
                    'title' =>  '<span class="text">' . trans('admin.unregistered_devices_log') . '</span>',
                    'route' => 'admin.unregistered_devices_log.index',
                    'childs' => ''
                ];
				
			$childs[] = [
                    'title' =>  '<span class="text">' . trans('admin.report_log') . '</span>',
                    'route' => 'admin.report_logs.index',
                    'childs' => ''
            ];

            $items[] = [
                'title' => '<i class="icon logs"></i>' . '<span class="text">' . trans('admin.logs') . '</span>',
                'route' => '',
                'childs' => $childs
            ];
    }

    $childs = [];

    if ( Auth::User()->isAdmin() ) {
        $childs[] = [
            'title' => '<i class="icon restart"></i> ' . '<span class="text">' .  trans('admin.restart_tracking_service') . '</span>',
            'route' => 'admin.restart_tracker',
            'childs' => '',
            'attribute' => 'class="js-confirm-link" data-confirm="'.trans('admin.do_restart_tracking_service').'"'
        ];
    }
    $childs[] = [
        'title' => '<i class="icon logout"></i>' . '<span class="text">' .  trans('global.log_out') . '</span>',
        'route' => 'logout',
        'childs' => ''
    ];

    $items[] = [
        'title' => Auth::User()->email . ' (' . trans('admin.group_'.Auth::User()->group_id) . ') <i class="caret"></i>',
        'route' => '',
        'childs' => $childs
    ];

    return parseNavigation($items, $currentRoute);
}

/**
 * @param $env
 * @param $items
 * @param $currentRoute
 * @param int $active
 * @param int $level
 * @return string
 */
function parseNavigation($items, $currentRoute, &$active = 0, $level = 1) {
    $html = '';
    if (!empty($items)) {
        foreach ($items as $item) {
            ($level == 1) && $active = 0;
            $childs = !empty($item['childs']);
            $innerLevel = $level + 1;
            //Sets active item
            ($currentRoute == Arr::get($item, 'route', '')) && $active = 1;

            // Gets childs html
            $innerHtml = parseNavigation(Arr::get($item, 'childs'), $currentRoute, $active, $innerLevel);

            $html .= '<li class="' . Arr::get($item, 'class', '')
                .($active && $level == 1 ? ' active' : '')
                .($childs && $level > 1 ? ' dropdown-submenu' : '')
                . '">

            <a ' . ($level > 1 ? '' : ($childs ? 'data-hover="dropdown" data-toggle="dropdown"' : '')) . ' href="' . (!empty($item['route']) ? route($item['route']) : 'javascript:;') . '"' . (!empty($item['attribute']) ? $item['attribute'] : '') . '>
                ' . Arr::get($item, 'title', '').
                ($level == 1 && $childs ? '<i class="' . ($active ? 'selected' : '') . '"></i>' : '').'
            </a>';

            $html .= ($childs ? '<ul class="dropdown-menu">' . $innerHtml . '</ul>' : '');

            $html .= '</li>';
        }

        return $html;
    }
    return $html;
}
