<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.3.12
 * Time: 13.08
 */

namespace Tobuli\Entities;

use DB;
use App\Events\TaskCreate;
use App\Events\TaskStatusChange;

class Task extends AbstractEntity
{
    public static $priorities = [
        1 => 'front.priority_low',
        2 => 'front.priority_normal',
        3 => 'front.priority_high'
    ];

    protected $fillable = [
        'device_id', 'title', 'comment', 'priority', 'status', 'invoice_number',
        'pickup_address', 'pickup_address_lat','pickup_address_lng',  'pickup_time_from', 'pickup_time_to',
        'delivery_address', 'delivery_address_lat', 'delivery_address_lng', 'delivery_time_from', 'delivery_time_to'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->status)) {
                $model->status = TaskStatus::STATUS_NEW;
            }
        });

        static::created(function ($model) {
            event(new TaskCreate($model));
        });

        static::saved(function ($model) {
            if ($model->isDirty('status') || $model->wasRecentlyCreated) {

                event(new TaskStatusChange($model));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function statuses() {
        return $this->hasMany(TaskStatus::class, 'task_id', 'id');
    }

    public function lastStatus() {
        return $this->hasOne(TaskStatus::class, 'task_id')->orderBy('created_at', 'desc');
    }

    public function scopeAccessibleUser($query, $user_id) {
        return $query->join('user_device_pivot', function ($join) use ($user_id) {
            $join
                ->on('user_device_pivot.device_id', '=', 'tasks.device_id')
                ->on('user_device_pivot.user_id', '=', DB::raw("'$user_id'"));
        });
    }

    public function getDeviceNameAttribute() {
        return $this->device->name;
    }

    public function getStatusNameAttribute()
    {
        return trans(TaskStatus::$statuses[$this->status]);
    }

    public function getPriorityNameAttribute() {
        return trans(self::$priorities[$this->priority]);
    }



}