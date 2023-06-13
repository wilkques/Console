<?php

namespace Wilkques\Console;

use Wilkques\Console\Contracts\Commandable;

abstract class Command implements Commandable
{
    /**
     * command flag
     * 
     * @var array
     */
    protected $options = [];

    /**
     * command arguments
     * 
     * @var array
     */
    protected $arguments = [];

    /**
     * Command Explanation
     * 
     * @var string
     */
    public $signature;

    /**
     * Command Description
     * 
     * @var string
     */
    public $description;

    /**
     * @param array $options
     * 
     * @return static
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function options()
    {
        return $this->options;
    }

    /**
     * @param string|int $key
     * 
     * @return mixed
     */
    public function option($key)
    {
        return $this->options()[$key];
    }

    /**
     * @param array $arguments
     * 
     * @return static
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @return array
     */
    public function arguments()
    {
        return $this->arguments;
    }

    /**
     * @param string|int $key
     * 
     * @return mixed
     */
    public function argument($key)
    {
        return $this->arguments()[$key];
    }

    /**
     * @return string
     */
    public function getSignaturet()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getHelper()
    {
        $description = $this->getCommandDescription();

        $command = array_shift($description);

        $signature = join(PHP_EOL, $description);

        $description = $this->getDescription();

        return compact('command', 'description', 'signature');
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $matches = $this->getFlagOrArgument();

        array_shift($matches);

        return \Wilkques\Console\Parser::parser($matches);
    }

    /**
     * @return array
     */
    public function regexMatches()
    {
        preg_match_all(
            '/^([^\s]+)|\{([^\}:]+)(?::[^}]+)?\}|\-\-([^\s]+)(?:\=([^\s]+))?\s*:\s*([^\n]+)/', 
            $this->getSignaturet(), 
            $matches
        );

        return $matches;
    }

    /**
     * @return array
     */
    public function getFlagOrArgument()
    {
        $matches = $this->regexMatches();

        next($matches);

        return next($matches);
    }

    /**
     * @return array
     */
    public function getCommandDescription()
    {
        return array_map("trim", current($this->regexMatches()));
    }
}
