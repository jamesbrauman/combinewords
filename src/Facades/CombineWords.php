<?php namespace TheSnackalicious\CombineWords\Facades;

use Illuminate\Support\Facades\Facade;
use TheSnackalicious\CombineWords\Generators\GeneratorContract;

class CombineWords extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return GeneratorContract::class;
    }
}