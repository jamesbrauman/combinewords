<?php namespace TheSnackalicious\CombineWords\Requirements;

class MinimumLengthRequirement implements RequirementContract
{
    /**
     * @var string
     */
    protected $length;

    /**
     * Construct a new MinimumLengthRequirement instance.
     * @param $length
     */
    public function __construct($length)
    {
        $this->length = $length;
    }

    /**
     * Determines if a string passes the requirement.
     *
     * @param string $string The string to test.
     * @return bool True if the string passes the requirement, false otherwise.
     */
    public function passes($string)
    {
        return strlen($string) >= $this->length;
    }
}