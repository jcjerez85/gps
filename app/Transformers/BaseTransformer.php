<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use League\Fractal\TransformerAbstract;
use Tobuli\Entities\User;

class BaseTransformer extends TransformerAbstract {

    /**
     * @var User
     */
    protected $user;

    public function __construct()
    {
        $this->user = getActingUser();
    }

    protected function canView($entity, $property, $default = null)
    {
        if (is_null($this->user))
            return $default;

        if ( ! $this->user->can('view', $entity, $property))
            return $default;

        return $entity->{$property};
    }

    /**
     * @return array
     */
    protected static function requireLoads()
    {
        return [];
    }

    /**
     * @param Collection|LengthAwarePaginator $data
     */
    public static function loadRelations(&$data)
    {
        if (!($data instanceof Collection || $data instanceof LengthAwarePaginator || $data instanceof CursorPaginator))
            return;

        $class = get_called_class();
        $loads = $class::requireLoads();

        if ($loads) {
            $data->load($loads);
        }
    }
}