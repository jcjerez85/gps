<?php namespace Tobuli\Entities;

class Checklist extends AbstractEntity
{
    const TYPE_PRE_START = 1;
    const TYPE_SERVICE = 2;

    protected $table = 'checklist';

    protected $fillable = [
        'template_id',
        'service_id',
        'name',
        'signature',
        'archived',
    ];

    public function template()
    {
        return $this->belongsTo('Tobuli\Entities\ChecklistTemplate', 'template_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo('Tobuli\Entities\DeviceService', 'service_id', 'id');
    }

    public function rows()
    {
        return $this->hasMany('Tobuli\Entities\ChecklistRow', 'checklist_id');
    }

    public function getTypeNameAttribute()
    {
        return ChecklistTemplate::getTypes($this->type);
    }

    public function scopeComplete($query)
    {
        return $query->whereNotNull('checklist.completed_at');
    }

    public function scopeIncomplete($query)
    {
        return $query->whereNull('checklist.completed_at');
    }

    public function scopeFailed($query)
    {
        return $query->whereHas('rows', function ($q) {
            $q->failed();
        });
    }

    public function scopeType($query, $type)
    {
        return $query->where('checklist.type', $type);
    }

    public function scopeByUser($query, \Tobuli\Entities\User $user)
    {
        return $query->select('checklist.*')
            ->join('device_services', 'device_services.id', '=', 'checklist.service_id')
            ->join('devices', 'devices.id', '=', 'device_services.device_id')
            ->join('user_device_pivot', 'user_device_pivot.device_id', '=', 'devices.id')
            ->where('user_device_pivot.user_id', $user->id)
            ->groupBy('checklist.id');
    }

    public function incompleteRows()
    {
        return $this->rows()
            ->where('completed', 0)
            ->get();
    }

    public static function getAvailableTemplates($serviceId)
    {
        $used = self::where('service_id', $serviceId)->get()->pluck('template_id');

        return ChecklistTemplate::whereNotIn('id', $used)->get();
    }
}
