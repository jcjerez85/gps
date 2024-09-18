<?php namespace Tobuli\Entities;


class TrackerPort extends AbstractEntity {
	protected $table = 'tracker_ports';

    protected $fillable = array('active', 'port', 'name', 'extra');

    public function scopeActive($query) {
        return $query->where('active', 1);
    }

    public function getDisplayAttribute()
    {
        $user = getActingUser();

        $canViewProtocol = $user && $user->perm('device.protocol', 'view') ? true : false;

        return $this->port . ($canViewProtocol ? " / {$this->name}" : "");
    }

}
