<?php

namespace Tobuli\Services\Cleaner;

use Illuminate\Support\Str;

abstract class AbstractCleaner implements DateCleanerInterface
{
    /**
     * @var \Closure
     */
    protected $output;
    protected $date;
    protected $dateField = 'created_at';
    protected $limit = 10000;

    public function __construct(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $setMethod = 'set' . ucfirst(Str::camel($key));

            if (method_exists($this, $setMethod)) {
                $this->$setMethod($value);
            }
        }
    }

    public function setDate($date): self
    {
        $this->date = $date;

        return $this;
    }

    public function setDateField(string $dateField): self
    {
        $this->dateField = $dateField;

        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function setOutput(\Closure $output): self
    {
        $this->output = $output;

        return $this;
    }
}