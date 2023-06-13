<?php

namespace Wilkques\Console;

class Console
{
    /**
     * commands
     * 
     * @var array
     */
    public $commands = [];

    /**
     * depiction
     * 
     * @var array
     */
    public $depiction = [];

    /**
     * instance
     * 
     * @var array
     */
    static $instance = [];

    /**
     * helpers
     * 
     * @var array
     */
    static $helpers = [];

    /**
     * command mapping
     * 
     * @var array
     */
    protected $commandMapping = [];

    /**
     * console path
     * 
     * @var string
     */
    protected $commandRootPath = './Console';

    /**
     * composer.json path
     * 
     * @var string
     */
    protected $composerPath = './composer.json';

    /**
     * @param string $root
     * 
     * @return static
     */
    public function setCommandRootPath(string $commandRootPath)
    {
        $this->commandRootPath = $commandRootPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommandRootPath()
    {
        return $this->commandRootPath;
    }

    /**
     * @param string $abstract
     * @param \Console\Contracts\Commandable|\Willis\Console\Command|null $object
     * 
     * @return static
     */
    public function register($abstract, $object = null)
    {
        !$object && $object = container($abstract);

        static::$helpers = $this->fireCommand($object, $abstract);

        return $this;
    }

    /**
     * @return static
     */
    public function build()
    {
        static::$helpers = array_reduce(
            static::scanConsoleDir(
                $this->getCommandRootPath()
            ),
            function ($item, $path) {
                $item[] = $this->fireCommand($this->getCommandClass($path));

                return $item;
            }
        );

        return $this;
    }

    /**
     * @param string $abstract
     * 
     * @return array
     */
    protected function fireCommand($abstract)
    {
        /** @var \Willis\Console\Contracts\Commandable|\Willis\Console\Command */
        $concrete = container($abstract);

        $helpers = $concrete->getHelper();

        $this->setCommandMapping($helpers['command'], $abstract);

        return $helpers;
    }

    /**
     * command mapping
     * 
     * @param string $command
     * @param string $class
     * 
     * @return static
     */
    public function setCommandMapping(string $command, string $class)
    {
        $this->commandMapping[$command] = $class;

        return $this;
    }

    /**
     * handle commands
     * 
     * @param array $command
     */
    public function handle($commands)
    {
        $type = array_shift($commands);

        if (!$type) {
            echo $this->getHelpers();
        }

        !array_key_exists($type, $this->commandMapping) && die("no command");

        /** @var \Console\Contracts\Commandable|\Willis\Console\Command */
        $abstract = container($this->commandMapping[$type]);

        $signaturet = $abstract->toArray();

        $commandFlagArgument = $this->handleCommands($commands);

        $abstract->setOptions(
            $this->handleOptions($commandFlagArgument['options'], $signaturet['options'])
        )->setArguments(
            $this->handleArguments($commandFlagArgument['params'], $signaturet['params'])
        )->handle();
    }

    /**
     * print helper
     */
    public function getHelpers()
    {
        $climate = new \League\CLImate\CLImate;

        $climate->table(static::$helpers);

        die;
    }

    /**
     * scan console dir
     * 
     * @param string $dir
     * 
     * @return array
     */
    protected static function scanConsoleDir($dir)
    {
        return array_reduce(scandir($dir), function ($item, $path) use ($dir) {
            if (!in_array($path, array(".", ".."))) {
                $findPath = $dir . DIRECTORY_SEPARATOR;
                if (is_dir($findPath . $path)) {
                    return static::scanConsoleDir($findPath . $path);
                } else {
                    if (preg_match('/php/i', $path)) {
                        $item[] = $dir . DIRECTORY_SEPARATOR . $path;
                    }
                }

                return $item;
            }
        });
    }

    /**
     * @param string $composerPath
     * 
     * @return static
     */
    public function setComposerPath($composerPath = './composer.json')
    {
        $this->composerPath = $composerPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getComposerPath()
    {
        return $this->composerPath;
    }

    /**
     * @param string $path
     * 
     * @return string
     */
    protected function psr4($path)
    {
        $composerPath = $this->getComposerPath();

        if (file_exists($composerPath)) {
            $jsonString = file_get_contents($composerPath);

            $json = json_decode($jsonString, true);

            if (array_key_exists('autoload', $json) && array_key_exists('psr-4', $json['autoload'])) {
                foreach ($json['autoload']['psr-4'] as $reNamespace => $namespace) {
                    if (strtok($path, $namespace)) {
                        $path = str_replace($namespace, $reNamespace, $path);
                    }
                }
            }
        }

        return $path;
    }

    /**
     * Get the full command class name for a given command.
     *
     * @param  string  $path
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getCommandClass($path)
    {
        $path = $this->psr4($path);

        $namespace = $this->getNamespace($path);

        $class = str_replace(
            ['./', '.php', '/'],
            ['\\', '', '\\'],
            $path
        );

        return $namespace . str_replace($namespace, '', $class);
    }

    /**
     * Get the namespace for the command.
     *
     * @param  string  $path
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getNamespace($path)
    {
        $namespace = trim(implode('\\', array_slice(explode('/', $path), -3, -1)), '\\');

        if (!$namespace) {
            throw new \InvalidArgumentException("Namespace not found in file: {$path}. Please add a namespace to your command and try again.");
        }

        return str_replace('.', '', $namespace);
    }

    /**
     * Parsing Command-Line Arguments and Options
     * 
     * @param array $commands
     * 
     * @return array
     */
    protected function handleCommands(array $commands)
    {
        return \Wilkques\Console\Parser::parser($commands);
    }

    /**
     * @param array $commandFlag
     * @param array $signaturetFlag
     * 
     * @return array
     */
    protected function handleOptions($commandFlag, $signaturetFlag)
    {
        return array_merge($signaturetFlag, $commandFlag);
    }

    /**
     * @param array $commandArgument
     * @param array $signaturetArgument
     * 
     * @return array
     */
    protected function handleArguments(array $commandArgument, array $signaturetArgument)
    {
        return empty($commandArgument) ? [] :
            array_combine(
                $signaturetArgument,
                $commandArgument
            );
    }
}
