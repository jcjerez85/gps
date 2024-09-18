<?php namespace Tobuli\Entities;

use Illuminate\Support\Facades\Cache;
use Tobuli\Helpers\Templates\TemplateManager;

class SmsTemplate extends AbstractEntity {
	protected $table = 'sms_templates';

    protected $fillable = array('title', 'note');

    protected $searchable = [
        'title'
    ];

    public $timestamps = false;

    public function buildTemplate($data, $user = null)
    {
        $template_builder = (new TemplateManager())->loadTemplateBuilder($this->name);

        return $template_builder->setUser($user)->buildTemplate($this, $data);
    }

    public function scopeNotOwn($query, $user_id)
    {
        if ($user_id instanceof User)
            $user_id = $user_id->id;

        return $query
            ->select("{$this->table}.*")
            ->leftJoin("{$this->table} AS {$this->table}_tmp", function ($join) use ($user_id) {
                $join
                    ->on("{$this->table}.name", '=', "{$this->table}_tmp.name")
                    ->where("{$this->table}_tmp.user_id", $user_id);
            })
            ->whereNull("{$this->table}_tmp.id");
    }

    public function scopeUserBy($query, $user_id)
    {
        return $query->where(function($query) use ($user_id) {
            $query->whereNull("{$this->table}.user_id");
            $query->orWhere("{$this->table}.user_id", $user_id);
        });
    }

    /**
     * @param $name
     * @param User|null $user
     */
    public static function getTemplate($name, $user = null)
    {
        $manager_id = null;

        if ($user) {
            $manager_id = $user->isReseller() ? $user->id : $user->manager_id;
        }

        $key = "sms_template.$name.$manager_id";

        return Cache::store('array')->rememberForever($key, function() use ($name, $manager_id) {
            if ($manager_id && $manager = User::getManagerTopFirst($manager_id))
                $manager_id = $manager->id;

            return self::where("name", $name)
                ->userBy($manager_id)
                ->orderBy('user_id', 'desc')
                ->first();
        });
    }
}
