<?php namespace Tobuli\Validation;

class AdminLogoUploadValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'login_page_logo' => 'image|mimes:jpeg,jpg,png,gif,svg|max:20000|dimensions:min_width=50',
            'frontpage_logo' => 'image|mimes:jpeg,jpg,png,gif,svg|dimensions:max_height=60|max:20000',
            'favicon' => 'file|mimetypes:image/x-icon,image/vnd.microsoft.icon|dimensions:width=16,height=16|max:2000',
            'background' => 'image|mimes:jpeg,jpg,png,gif|max:20000|dimensions:min_width=1024',

            'welcome_text' => 'max:255',
            'bottom_text' => 'max:2000',
            'apple_store_link' => 'url',
            'google_play_link' => 'url',
        ]
    ];

}
