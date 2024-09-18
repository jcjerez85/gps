<?php

namespace Tobuli\Exporters\Util;

class ExportTypesUtil
{
    const EXPORT_TYPE_SINGLE = 'export_single';
    const EXPORT_TYPE_GROUPS = 'export_groups';
    const EXPORT_TYPE_ACTIVE = 'export_active';
    const EXPORT_TYPE_INACTIVE = 'export_inactive';

    public static function getTranslations(array $exclusions = []): array
    {
        $translations = [
            self::EXPORT_TYPE_SINGLE => trans('front.export_single'),
            self::EXPORT_TYPE_GROUPS => trans('front.export_groups'),
            self::EXPORT_TYPE_ACTIVE => trans('front.export_active'),
            self::EXPORT_TYPE_INACTIVE => trans('front.export_inactive'),
        ];

        foreach ($exclusions as $exclusion) {
            unset($translations[$exclusion]);
        }

        return $translations;
    }
}