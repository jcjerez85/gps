<?php namespace Tobuli\Entities;


class DeviceExpense extends AbstractEntity
{
    protected $table = 'device_expenses';

    protected $fillable = [
        'user_id',
        'device_id',
        'type_id',
        'name',
        'quantity',
        'unit_cost',
        'supplier',
        'buyer',
        'additional',
        'date',
    ];

    protected $guarded = [];

    protected $appends = ['total'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->hasOne(DeviceExpensesType::class, 'id', 'type_id');
    }

    public function getTotalAttribute()
    {
        return $this->quantity * $this->unit_cost;
    }

    public function scopeUserAccessible($query, User $user)
    {
        return $query->whereIn('device_id', function ( $query) use ($user) {
            $query->select('device_id')
                ->from('user_device_pivot')
                ->where('user_id', $user->id);
        });
    }
}
