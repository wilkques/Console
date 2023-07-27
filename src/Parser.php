<?php

namespace Wilkques\Console;

class Parser
{
    /**
     * command parser
     * 
     * @param array $tokens
     * 
     * @return array
     */
    public static function parser($tokens)
    {
        $options = [];

        $arguments = [];

        foreach ($tokens as $arg) {
            if (strpos($arg, '-') === 0) {
                $key = preg_replace_callback(
                    '/(--)?\w+(?:-?\w*)?(?:_?\w*)?(?:[=|\s][\w-]+)?|(-)+\w+(?:[=|\s][\w-]+)?/',
                    function ($matches) {
                        return str_replace($matches[1], '', $matches[0]);
                    },
                    $arg
                );

                $value = 'true';

                if (strpos($key, '=') !== false) {
                    [$key, $value] = explode('=', $key, 2);
                }

                $options[$key] = is_a_to($value);
            } else {
                $arguments[] = $arg;
            }
        }

        return compact('options', 'arguments');
    }
}