<?php

if (!function_exists('collectInjectable')) {
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param array $array
     * @return \Infira\Collection\InjectableCollection
     */
    function collectInjectable($array)
    {
        return new \Infira\Collection\InjectableCollection(new \Illuminate\Support\Collection($array));
    }
}