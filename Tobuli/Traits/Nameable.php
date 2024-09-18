<?php

namespace Tobuli\Traits;

trait Nameable
{
    public function generateNewName($name)
    {
        $nameField = $this->nameField ?? 'name';

        if (array_search($nameField, \Illuminate\Support\Facades\Schema::getColumnListing($this->table)) === false) {
            throw new \Tobuli\Exceptions\ValidationException(trans(
                'validation.field_does_not_exist',
                ['attribute' => $nameField]
            ));
        }

        $number = $this->newQuery()
            ->select($nameField)
            ->where($nameField, 'like', "{$name} %")
            ->get()
            ->map(function($value) use($name, $nameField) {
                return intval(str_replace("{$name} ", '', $value[$nameField]));
            })
            ->filter(function($value) {
                return is_int($value) && $value;
            })
            ->max()
            ?? 0;

        $this->{$nameField} = "{$name} ".++$number;
    }
}
