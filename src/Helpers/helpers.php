<?php
/**
 * @file defines a set of helper functions.
 */

if (! function_exists('factory')) {
    /**
     * A helper function to create and run factories.
     *
     * @param string $table Table name preceded by the schema if not public.
     *                       Example: "node", "public.node" or "chado.feature"
     * @param int $times The number of times to repeat the operation.
     * @return \StatonLab\TripalTestSuite\Database\Factory
     */
    function factory($table, $times = 1)
    {
        return new \StatonLab\TripalTestSuite\Database\Factory($table, $times);
    }
}

if (! function_exists('class_uses_recursive')) {
    /**
     * Returns all traits used by a class, its subclasses and trait of their traits.
     *
     * @author https://github.com/illuminate/support/blob/master/helpers.php#L391
     * @param  object|string $class
     * @return array
     */
    function class_uses_recursive($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $results = [];
        foreach (array_reverse(class_parents($class)) + [$class => $class] as $class) {
            $results += trait_uses_recursive($class);
        }

        return array_unique($results);
    }
}

if (! function_exists('trait_uses_recursive')) {
    /**
     * Returns all traits used by a trait and its traits.
     *
     * @author https://github.com/illuminate/support/blob/master/helpers.php#L1121
     * @param  string $trait
     * @return array
     */
    function trait_uses_recursive($trait)
    {
        $traits = class_uses($trait);
        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}
