<?php

if (!function_exists('getPHPBuiltInTypes')) {
    function getPHPBuiltInTypes(): array
    {
        // PHP 8.1
        if (PHP_VERSION_ID >= 80100) {
            return ['array', 'callable', 'string', 'int', 'bool', 'float', 'iterable', 'void', 'object', 'mixed', 'false', 'null', 'never'];
        }

        // PHP 8
        if (\PHP_MAJOR_VERSION === 8) {
            return ['array', 'callable', 'string', 'int', 'bool', 'float', 'iterable', 'void', 'object', 'mixed', 'false', 'null'];
        }

        // PHP 7
        switch (\PHP_MINOR_VERSION) {
            case 0:
                return ['array', 'callable', 'string', 'int', 'bool', 'float'];
            case 1:
                return ['array', 'callable', 'string', 'int', 'bool', 'float', 'iterable', 'void'];
            default:
                return ['array', 'callable', 'string', 'int', 'bool', 'float', 'iterable', 'void', 'object'];
        }
    }
}