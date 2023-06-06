# Console for PHP

[![Latest Stable Version](https://poser.pugx.org/wilkques/console/v/stable)](https://packagist.org/packages/wilkques/console)
[![License](https://poser.pugx.org/wilkques/console/license)](https://packagist.org/packages/wilkques/console)

````
composer require wilkques/console
````

## How to use
1. Add PHP command file (path default ./Console)
    ```php
    <?php

    use Wilkques\Console\Command;

    class DoSomethingCommand extends Command
    {
        /**
         * signature
         * 
         * @var string
         */
        public $signature = "do:something
                            {--debug=false: debug mode (default false)}
                            {--list=false: get list (default false)}";

        /**
         * description
         * 
         * @var string
         */
        public $description = "do something";

        public function handle()
        {
            // do something
        }
    }
    ```

1. in terminal run touch artisan & Add PHP code
    ```php
    require_once 'vendor/autoload.php';

    $command = $argv;

    array_shift($command);

    console()
    ->setCommandRootPath("<set console dir path>") // if you want change path
    ->build()
    ->handle($command);  
    ```

1. in terminal run `php artisan do:something --debug=true`