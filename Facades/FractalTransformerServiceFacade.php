<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.3.28
 * Time: 11.07
 */

namespace CustomFacades;


use Illuminate\Support\Facades\Facade;

class FractalTransformerServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'Tobuli\Services\FractalTransformerService';
    }

}