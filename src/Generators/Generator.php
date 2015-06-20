<?php namespace TheSnackalicious\CombineWords\Generators;

use Closure;
use TheSnackalicious\CombineWords\Requirements\NotARequirementException;
use TheSnackalicious\CombineWords\Requirements\RequirementContract;

class Generator implements GeneratorContract
{
    /**
     * The pattern to extract holders from a format string.
     *
     * @var string
     */
    protected $holderPattern = '/(?<holder>{\w+})/';

    /**
     * The pattern to extract the holder name from a holder string.
     *
     * @var string
     */
    protected $holderNamePattern = '/{(?<name>\w+)}/';

    /**
     * An multidimensional array of loaded words.
     *
     * @var array
     */
    protected $words = [];

    /**
     * The directory to load word lists from.
     *
     * @var string
     */
    protected $directory;

    /**
     * @var array
     */
    protected $requirements = [];

    /**
     * @var null
     */
    private $maxAttempts;

    /**
     * Get all the holders that are in a format string.
     *
     * @param $format
     * @return array
     */
    protected function holders($format)
    {
        $matches = null;

        return preg_match_all($this->holderPattern, $format, $matches) ? $matches['holder'] : [];
    }

    /**
     * Get a random replacement for a holder.
     *
     * @param string $holder
     * @return string
     */
    protected function replacement($holder)
    {
        if ($name = $this->name($holder))
        {
            return $this->random($name);
        }
        else
        {
            return $holder;
        }
    }

    /**
     * Replaces the first occurrence of the holder string with the replacement.
     *
     * @param string $buffer
     * @param string $holder
     * @param string $replacement
     * @return string
     */
    protected function replace($buffer, $holder, $replacement)
    {
        return $this->replaceFirst($holder, $buffer, $replacement);
    }

    /**
     * Replace the first occurrence of $needle in $haystack with $replacement.
     *
     * @param string $needle
     * @param string $haystack
     * @param string $replacement
     * @return string
     */
    protected function replaceFirst($needle, $haystack, $replacement)
    {
        $pos = strpos($haystack, $needle);

        return $pos !== false ? substr_replace($haystack, $replacement ,$pos, strlen($needle)) : $haystack;
    }

    /**
     * Gets the name of a holder from a holder string.
     *
     * @param string $holder
     * @return string|null
     */
    protected function name($holder)
    {
        $matches = null;

        return preg_match($this->holderNamePattern, $holder, $matches) ? $matches['name'] : null;
    }

    /**
     * Gets a random word for the specified holder name.
     *
     * @param string $name
     * @return string
     */
    protected function random($name)
    {
        if (!array_key_exists($name, $this->words))
            $this->load($name);

        return $this->array_random_value($this->words[$name]);
    }

    /**
     * Get a random array value from an array.
     *
     * @param $array
     * @return mixed
     */
    protected function array_random_value($array)
    {
        shuffle($array);

        return $array[0];
    }

    /**
     * Loads the list of words for a holder name.
     *
     * @param $name
     * @return void
     */
    protected function load($name)
    {
        $values = json_decode(file_get_contents($this->filePath($name)));

        $this->words[$name] = $values;
    }

    /**
     * Gets the file path of a word list for a holder name.
     *
     * @param string $name
     * @return string
     */
    protected function filePath($name)
    {
        return $this->directory . DIRECTORY_SEPARATOR . $name .  '.json';
    }

    /**
     * Generate a string from random words using a format string.
     *
     * @param string $format
     * @return string
     */
    protected function generate($format)
    {
        $buffer = $format;

        foreach ($this->holders($format) as $holder)
        {
            $replacement = $this->replacement($holder);

            $buffer = $this->replace($buffer, $holder, $replacement);
        }

        return $buffer;
    }

    /**
     * Determines if a buffer satisfies our string requirements.
     *
     * @param string $buffer
     * @return bool
     */
    protected function satisfiesRequirements($buffer)
    {
        foreach ($this->requirements as $requirement)
            if (!$this->satisfiesRequirement($buffer, $requirement)) return false;

        return true;
    }

    /**
     * Determines if a number of attempts is too many.
     *
     * @param int $attempts
     * @return bool
     */
    protected function tooManyAttempts($attempts)
    {
        return $this->maxAttempts == null ? false : $attempts >= $this->maxAttempts;
    }

    /**
     * @param string $buffer
     * @param RequirementContract|Closure $requirement
     * @return bool
     */
    protected function satisfiesRequirement($buffer, $requirement)
    {
        if ($requirement instanceof RequirementContract)
            return $requirement->passes($buffer);

        if ($requirement instanceof Closure)
            return $requirement($buffer);

        return false;
    }

    /**
     * Construct a new Generator instance.
     *
     * @param string $directory
     * @param null $maxAttempts
     */
    public function __construct($directory, $maxAttempts = null)
    {
        $this->directory = $directory;

        $this->maxAttempts = $maxAttempts;
    }

    /**
     * Make a string from random words that meets the specified requirements using a format string.
     *
     * @param string $format
     * @param null|int $maxAttempts
     * @param bool $preserveRequirements
     * @return string
     */
    public function make($format, $maxAttempts = null, $preserveRequirements = false)
    {
        $this->maxAttempts = $maxAttempts !== null ? $maxAttempts : $this->maxAttempts;

        $buffer = null;

        $attempts = 0;
        do
        {
            if ($this->tooManyAttempts($attempts))
                return null;

            $buffer = $this->generate($format);

            $attempts++;
        } while (!$this->satisfiesRequirements($buffer));

        if (!$preserveRequirements)
            $this->requirements = [];

        return $buffer;
    }

    /**
     * Add a requirement to the generator.
     *
     * @param RequirementContract|Closure $requirement
     * @return GeneratorContract
     * @throws NotARequirementException
     */
    public function requirement($requirement)
    {
        if (!$requirement instanceof RequirementContract && !$requirement instanceof Closure)
            throw new NotARequirementException('The provided requirement cannot be used to evaluate the buffer.');

        array_push($this->requirements, $requirement);

        return $this;
    }

    /**
     * Add multiple requirements to the generator.
     *
     * @param array $requirements
     * @return GeneratorContract
     */
    public function requirements($requirements)
    {
        foreach ($requirements as $requirement)
            $this->requirement($requirement);

        return $this;
    }
}