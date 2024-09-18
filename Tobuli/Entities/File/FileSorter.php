<?php
namespace Tobuli\Entities\File;

use App\Exceptions\ResourseNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;
use Tobuli\Entities\File\FileQuery;
use Auth;

class FileSorter
{
    private $sortBy;
    private $order;

    private $path;

    private $limit;
    private $offset;
    private $namePattern;
    private $from;
    private $to;

    private $totalCount;

    private $sortFields;
    private $sortDirections;

    public function __construct(string $path)
    {
        $this->sortBy = 'date_modified';
        $this->order = 'desc';
        $this->path = $path;
        $this->offset = 0;
        $this->namePattern = '*';
        $this->from = null;
        $this->to = null;
        $this->totalCount = 0;

        $this->sortDirections = [
            'asc',
            'desc',
        ];
        $this->sortFields = [
            'date_modified' => [
                'field_selector' => '%T@', // C - File's last status change time | @ - unixtime format in miliseconds
                'type' => 'n', //numeric
            ],
            'date_created' => [
                'field_selector' => '%C@', // C - File's last status change time | @ - unixtime format in miliseconds
                'type' => 'n', //numeric
            ],
            'filename' => [
                'field_selector' => '%f',
                'type' => 'f', //ignore case
            ],
            'file_size' => [
                'field_selector' => '%s',
                'type' => 'n', //numeric
            ],
        ];
    }

    public function sortBy($sortBy = 'date_modified', $order = 'desc')
    {
        if (! array_key_exists($sortBy, $this->sortFields)) {
            throw new \InvalidArgumentException("Cannot sort by: \"{$sortBy}\"");
        }

        if (! in_array($order, $this->sortDirections)) {
            throw new \InvalidArgumentException("Invalid order: \"{$order}\"");
        }

        $this->sortBy = $sortBy;
        $this->order = $order;

        return $this;
    }

    public function offset($offset)
    {
        if (! is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException("Offset must be a positive or zero integer");
        }

        $this->offset = $offset;

        return $this;
    }

    public function limit($limit)
    {
        if (! is_int($limit) || $limit <= 0) {
            throw new \InvalidArgumentException("Limit must be a positive integer");
        }

        $this->limit = $limit;

        return $this;
    }

    public function nameStartsWith(string $pattern)
    {
        $this->namePattern = $pattern . $this->namePattern;

        return $this;
    }

    public function nameEndsWith(string $pattern)
    {
        $this->namePattern .= $pattern;

        return $this;
    }

    public function namePattern(string $pattern)
    {
        $this->namePattern = $pattern;

        return $this;
    }

    public function from(string $from)
    {
        $this->from = $from;

        return $this;
    }

    public function to(string $to)
    {
        $this->to = $to;

        return $this;
    }

    public function get()
    {
        if (! file_exists($this->path)) {
            throw new ResourseNotFoundException("Device media directory doesn't exist.");
        }

        return $this->bash();
    }

    private function bash()
    {
        $datePredicates = $this->getDatePredicates();

        $totalCountCommand = "find {$this->path} -type f $datePredicates | wc -l";
        $process = Process::fromShellCommandline($totalCountCommand);
        $process->run();
        $process->wait();

        if (! $process->isSuccessful() || $process->getErrorOutput()) {
            throw new \Exception("Couldn't get file count in directory: {$this->path}");
        }

        $this->totalCount = str_replace(PHP_EOL, '', $process->getOutput());

        $offsetCommand = "";

        if ($this->offset) {
            $this->offset++; //needs to represent position of 1st element. $offset = 15 means that list starts at 16th position

            $offsetCommand = "| tail -n +{$this->offset}";
        }

        $sortCommand = "sort";

        if ($this->sortFields[$this->sortBy]['type'] || $this->order === 'desc') {
            $sortCommand .= " -{$this->sortFields[$this->sortBy]['type']}";
            $sortCommand .= $this->order === 'desc' ? 'r' : ''; //add r argument to reverse list
        }

        $limitCommand = "";

        if (isset($this->limit)) {
            $limitCommand = "| head -n {$this->limit} ";
        }

        $sortField = $this->sortFields[$this->sortBy]['field_selector'];
        $filesByDateCommand = "find {$this->path} "
            . "-type f "
            . "-name \"{$this->namePattern}\" "
            . $datePredicates
            . "-printf \"{$sortField}\t%p\\n\" | " //get sorting field and filename
            . "$sortCommand | " // sort
            . "cut -f 2- " // cut sort field by tab char. leave path only
            . $offsetCommand // skip by offset
            . $limitCommand; //take limit

        //using exec, because process breaks pipe and won't get results
        exec($filesByDateCommand, $files, $returnCode);

        if ($returnCode != 0) {
            throw new \Exception("Couldn't sort files: {$returnCode}");
        }

        return [
            'files' => $files,
            'total' => $this->totalCount,
        ];
    }
    
    private function getDatePredicates(): string
    {
        $predicates = '';

        if ($this->from) {
            $predicates .= '-newermt "' . $this->from . '" ';
        }

        if ($this->to) {
            $predicates .= '! -newermt "' . $this->to . '" ';
        }

        return $predicates;
    }
}
