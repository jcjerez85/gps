<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\AbstractSidebarItemsController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceGroup;

/**
 * @property Device $itemModel
 */
class DevicesSidebarController extends AbstractSidebarItemsController
{
    protected string $repo = 'devices';
    protected string $viewDir = 'front::Objects';
    protected string $nextRoute = 'objects.sidebar';
    protected string $groupClass = DeviceGroup::class;

    private bool $filterAll = false;

    public function items()
    {
        $this->filterAll = true;

        return parent::items();
    }

    protected function getGroupItemsQuery($groupId, $search)
    {
        $query = $this->user
            ->devices()
            ->with(['traccar', 'sensors' => function (HasMany $query) {
                $query->whereIn('type', ['acc', 'engine', 'ignition']);
            }]);

        if ($search) {
            $query->search($search);
        }

        return $this->filterAll
            ? $query->filter(request()->all())
            : $query->filterGroupId($groupId);
    }
}
