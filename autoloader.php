<?php
/**
 * Check PHP version and autoload classes 
 */
if(version_compare(phpversion(), '5.3.0', '>=') === true) {
    spl_autoload_register(function ($class) {
        $filePath = "classes/"
                    . str_replace('\\', DIRECTORY_SEPARATOR, $class)
                    . '.php';
            
        if(is_file($filePath)) {
            include_once $filePath;     
        }
    });
}
else {
    echo '<div style="color: red; font-weight: bold;">PHP Version must be 5.3.0 or higher!</div>';
    exit();
}