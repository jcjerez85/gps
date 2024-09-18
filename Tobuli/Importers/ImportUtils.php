<?php

namespace Tobuli\Importers;

use Tobuli\Importers\Task\TaskImportManager;

class ImportUtils
{
    private static $modelImportManagers = [
        'task' => TaskImportManager::class,
    ];

    public function resolveImporterManager(string $model): ImportManager
    {
        return new self::$modelImportManagers[$model];
    }

    public function getDefaultValues(array $inputFields, array $fileFields): array
    {
        foreach ($inputFields as $inputField => &$value) {
            foreach ($fileFields as $fileField) {
                $fileField = (string)$fileField;

                if ($this->simplifyString($fileField) === $this->simplifyString($inputField)) {
                    $value = $fileField;

                    continue 2;
                }
            }

            $value = null;
        }

        return $inputFields;
    }

    /**
     * Makes string lowercase and removes special chars.
     * @param string $string
     * @return string
     */
    private function simplifyString(string $string): string
    {
        return preg_replace('/[^a-z0-9\-]/', '', strtolower($string));
    }

    public static function getModelImportManagers(): array
    {
        return self::$modelImportManagers;
    }
}