<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Tobuli\Entities\AbstractGroup;

abstract class AbstractSidebarItemsController extends Controller
{
    const LOAD_LIMIT = 100;

    protected string $repo;
    protected string $viewDir;
    protected string $nextRoute;
    protected string $groupClass;

    protected AbstractGroup $groupModel;
    protected Model $itemModel;

    public function __construct()
    {
        parent::__construct();

        $this->groupModel = new $this->groupClass();
        $this->itemModel = $this->groupModel->items()->getModel();
    }

    public function index()
    {
        return request()->filled('oPage') ? $this->items() : $this->groups();
    }

    public function groups()
    {
        $this->checkException($this->repo, 'view');

        $groups = $this->getGroups();

        return view("$this->viewDir.groups")->with(compact('groups'));
    }

    public function items()
    {
        $this->checkException($this->repo, 'view');

        $items = $this->getGroupItems(
            request()->group_id,
            request()->s
        );

        return view("$this->viewDir.items")->with(['items' => $items]);
    }

    protected function getGroups()
    {
        $search = request()->get('s');

        $groups = $this->groupModel->userOwned($this->user)
            ->whereHas('items', function (Builder $q) use ($search) {
                $q->when($search, function ($q) use ($search) {
                    $q->search($search);
                });
            })
            ->withCount(['items', 'itemsVisible'])
            ->orderBy('title')
            ->paginate();

        if (request()->get('page', 1) <= 1) {
            $ungrouped = $this->groupModel->makeUngroupedWithCount($this->user);

            if ($ungrouped->items_count) {
                $groups->prepend($ungrouped);
            }
        }

        $groups->setCollection($groups->getCollection()->transform(function(AbstractGroup $group) use ($search) {
            $open = $group->open || $search;

            $items = $open ? $this->getGroupItems($group->id, $search) : collect()->paginate(1);

            return [
                'id'        => $group->id,
                'title'     => $group->title,
                'open'      => $open,
                'count'     => $open ? $items->total() : $group->items_count,
                'active'    => $group->items_count === $group->items_visible_count,
                'next'      => route($this->nextRoute, [
                    'group_id'  => $group->id,
                    's'         => $search,
                    'oPage'     => 1,
                ]),
                'items'     => $items
            ];
        })->filter(function($group) {
            return $group['count'];
        }));

        return $groups;
    }

    /**
     * @return Builder|Relation
     */
    protected function getGroupItemsQuery($groupId, $search)
    {
        $query = $this->itemModel->userOwned($this->user);

        if ($search) {
            $query->search($search);
        }

        if ($groupId === null) {
            return $query;
        }

        if ($groupId) {
            return $query->where('group_id', $groupId);
        }

        return $query->whereNull('group_id');
    }

    protected function getGroupItems($groupId, $search)
    {
        return $this->getGroupItemsQuery($groupId, $search)
            ->paginate(self::LOAD_LIMIT, ['*'], 'oPage')
            ->setPath(route($this->nextRoute))
            ->appends([
                'group_id' => $groupId,
                's' => $search
            ]);
    }
}
