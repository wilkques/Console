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