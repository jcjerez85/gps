<?php namespace Tobuli\Helpers\Dashboard;

use Illuminate\Support\Str;

class DashboardManager
{
    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getFrame($name)
    {
        return $this->newBlockClass($name)->buildFrame();
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getContent($name)
    {
        return $this->newBlockClass($name)->buildContent();
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    private function newBlockClass($name)
    {
        $class = 'Tobuli\Helpers\Dashboard\Blocks\\' . ucfirst(Str::camel($name)) . 'Block';

        if ( ! class_exists($class, true))
            throw new \Exception('Dashboard class found ' . $class);

        return new $class;
    }
}