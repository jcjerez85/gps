<?php namespace Tobuli\Entities;


class CustomValue extends AbstractEntity
{
    protected $table = 'custom_values';

    protected $fillable = [
        'custom_field_id',
        'customizable_id',
        'customizable_type',
        'value',
    ];

    public function customizable()
    {
      return $this->morphTo();
    }

    public function custom_field()
    {
      return $this->belongsTo('Tobuli\Entities\CustomField', 'custom_field_id');
    }

    public function scopeWhereSlug($query, $slug)
    {
      return $query
            ->whereHas('custom_field', function ($relationQuery) use($slug) {
                $relationQuery->where('slug', $slug);
            });
    }
}
