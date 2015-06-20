<?php namespace TheSnackalicious\CombineWords\Generators;

use Closure;
use TheSnackalicious\CombineWords\Requirements\RequirementContract;

interface GeneratorContract
{
    /**
     * Make a string from random words that meets the specified requirements using a format string.
     *
     * @param string $format
     * @param null|int $maxAttempts
     * @param bool $preserveRequirements
     * @return string
     */
    public function make($format, $maxAttempts = null, $preserveRequirements = false);

    /**
     * Add a requirement to the generator.
     *
     * @param RequirementContract|Closure $requirement
     * @return GeneratorContract
     */
    public function requirement($requirement);

    /**
     * Add multiple requirements to the generator.
     *
     * @param array $requirements
     * @return GeneratorContract
     */
    public function requirements($requirements);
}