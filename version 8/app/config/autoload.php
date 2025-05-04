<?php
spl_autoload_register(function ($class) {
    // Convert namespace separators to directory separators
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    // Get the base path for the application
    $basePath = dirname(__DIR__, 2);
    
    // Build the full path to the class file
    $file = $basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $class . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});
