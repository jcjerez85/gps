<?php namespace Tobuli\Entities;


class ChecklistRow extends AbstractEntity
{
    const OUTCOME_PASS = 'pass';
    const OUTCOME_FAIL = 'fail';

    protected $table = 'checklist_row';

    protected $fillable = [
        'checklist_id',
        'template_row_id',
        'completed',
        'completed_at',
        'outcome',
    ];

    public $timestamps = false;

    public function checklist()
    {
        return $this->belongsTo('Tobuli\Entities\Checklist', 'checklist_id', 'id');
    }

    public function templateRow()
    {
        return $this->belongsTo('Tobuli\Entities\ChecklistTemplate', 'template_row_id', 'id');
    }

    public function images()
    {
        return $this
            ->hasMany('Tobuli\Entities\ChecklistImage', 'row_id')
            ->whereNull('checklist_history_id');
    }

    public function scopePassed($query)
    {
        return $query->where('outcome', self::OUTCOME_PASS);
    }

    public function scopeFailed($query)
    {
        return $query->where('outcome', self::OUTCOME_FAIL);
    }

    public function saveImage($path)
    {
        $image = new ChecklistImage([
            'checklist_id' => $this->checklist_id,
            'row_id' => $this->id,
            'path' => $path,
        ]);
        $image->save();

        return $image;
    }

    public function getFormattedOutcomeAttribute()
    {
        switch ($this->outcome) {
            case self::OUTCOME_FAIL:
            case self::OUTCOME_PASS:
                return trans("global.{$this->outcome}");
                break;
            default:
                return 'n/a';
        }
    }
}
