<?php namespace Tobuli\Helpers\Dashboard\Blocks;

interface BlockInterface
{
    public function buildFrame();
    public function buildContent();
}