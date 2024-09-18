<?php namespace Tobuli\Helpers\Dashboard\Blocks;

use Illuminate\Support\Arr;

abstract class Block implements BlockInterface
{
    protected $user;

    abstract protected function getContent();
    abstract protected function getName();

    
    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function buildFrame()
    {
        $name = $this->getName();
        $settings = getUserDashboardSettings($this->user);

        return view("front::Dashboard.Blocks.$name.block")->with([
            'name'   => $name,
            'config' => Arr::get($settings, "blocks.$name"),
        ])->render();
    }

    public function buildContent()
    {
        if (is_null($content = $this->getContent()))
            return null;

        return view('front::Dashboard.Blocks.' . $this->getName() . '.content' )
            ->with($content)
            ->render();
    }
}