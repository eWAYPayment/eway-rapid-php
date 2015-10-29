<?php

// Check a compatible PHP version is being used (5.4+)
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    throw new Exception('Minimum PHP version of 5.4.0 required');
}

/**
 * Basic autoloader using the PSR-4 standard - for use when a proper, global
 * autoloader isn't being used (such as Composer's).
 * 
 * Based on the example in
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 *      
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {

    $prefix = 'Eway\\';
    $base_dir = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

    // Check if the class being loaded is eWAY's
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators and append with .php
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

    // If the file exists, require it
    if (is_file($file)) {
        require_once $file;
    }
});
