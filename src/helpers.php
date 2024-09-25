<?php

if (!function_exists('console')) {
    /**
     * @return \Wilkques\Console\Console
     */
    function console()
    {
        return container(\Wilkques\Console\Console::class);
    }
}

if (!function_exists('dir_scan')) {
    /**
     * @param string $dir
     * 
     * @return \Generator<string>
     */
    function dir_scan($dir)
    {
        $stack = new SplStack();

        $stack->push($dir);

        while (!$stack->isEmpty()) {
            $currentDir = $stack->pop();

            foreach (scandir($currentDir) as $path) {
                if (!in_array($path, array(".", ".."))) {
                    $findPath = $currentDir . DIRECTORY_SEPARATOR . $path;

                    if (is_dir($findPath)) {
                        $stack->push($findPath);
                    } else {
                        yield $findPath;
                    }
                }
            }
        }
    }
}

if (!function_exists('is_a_to')) {
    /**
     * @param string $value
     * 
     * @return string|bool
     */
    function is_a_to(string $value, $callback = null)
    {
        if (in_array($value, ['false', 'true'])) {
            return $value == "true" ? true : ($value == "false" ? false : false);
        }

        $callback && $value = $callback($value);

        return $value;
    }
}