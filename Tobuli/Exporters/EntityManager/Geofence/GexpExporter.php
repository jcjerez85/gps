<?php

namespace Tobuli\Exporters\EntityManager\Geofence;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Exporters\Downloader\JsonDownloader;
use Tobuli\Exporters\EntityManager\ExporterInterface;

class GexpExporter implements ExporterInterface
{
    public function generateReport(Builder $query, array $attributes, string $filename): BinaryFileResponse
    {
        $data = $query->get($attributes)->toArray();

        $this->decorate($data);

        return (new JsonDownloader())
            ->download($data, $filename);
    }

    private function decorate(array &$data)
    {
        $groupIds = [];
        $data = [
            'groups' => [],
            'geofences' => $data,
        ];

        foreach ($data['geofences'] as &$geofence) {
            $group = $this->formatGroup($geofence['group'] ?? null);
            $groupId = $group['id'];

            if (!in_array($groupId, $groupIds, true)) {
                $groupIds[] = $groupId;
                $data['groups'][] = $group;
            }

            unset($geofence['group']);
        }
    }

    private function formatGroup($payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        return ['id' => null];
    }
}