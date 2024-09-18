<?php namespace Tobuli\Entities;

use Illuminate\Support\Facades\DB;

class UnregisteredDevice extends AbstractEntity
{
	protected $table = 'unregistered_devices_log';

    protected $primaryKey = 'imei';

    protected $fillable = [
        'imei',
        'port',
        'times',
        'ip',
    ];

    public $timestamps = false;
    public $incrementing = false;

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'imei', 'imei');
    }

    public function scopeLastest($query)
    {
        return $query->orderBy('date', 'desc');
    }

    public static function increase($imei, $protocol, $ip, $times = 1)
    {
        DB::statement(
            DB::raw("
          INSERT INTO `unregistered_devices_log` (imei, port, times, ip) 
          VALUES (
            :imei, 
            (SELECT COALESCE(MAX(port), 80) FROM tracker_ports WHERE `name` = :protocol LIMIT 1), 
            $times, 
            :ip)
          ON DUPLICATE KEY UPDATE times = (times + $times), ip = VALUES(ip), port = VALUES(port)"),
            [
                'ip' => $ip,
                'imei' => $imei,
                'protocol' => $protocol,
            ]
        );
    }
}
