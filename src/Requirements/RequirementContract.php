<?php namespace TheSnackalicious\CombineWords\Requirements;

interface RequirementContract
{
    /**
     * Determines if a string passes the requirement.
     *
     * @param string $string The string to test.
     * @return bool True if the string passes the requirement, false otherwise.
     */
    public function passes($string);
}