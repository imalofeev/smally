<?php

//require_once 'config/server.php';  // prod config
require_once 'config/develop.php';   // dev config

// Require and register Twig
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Twig/Autoloader.php';
spl_autoload_register(array('Twig_Autoloader', 'autoload'), true, true);

// register autoload function in spl, for framework classes
spl_autoload_register('autoload', true);


/**
 * Autoloader (PSR-0)
 *
 * @param string $class
 */
function autoload($class)
{
    global $config;
    
    $className     = $config['path']['classes'] . '/'                    . ltrim($class, '\\');
    $classNameApp  = $config['path']['classes'] . '/App/' . $class . '/' . ltrim($class, '\\');
    $classNameApp_ = $config['path']['classes'] . '/App/'                . ltrim($class, '\\');

    $fileName  = '';

    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', '/', $namespace) . '/';
    }

    $fileName    .= str_replace('_', '/', $className)     . '.php';
    $fileNameApp  = str_replace('_', '/', $classNameApp)  . '.php';
    $fileNameApp_ = str_replace('_', '/', $classNameApp_) . '.php';

    if (is_readable($fileName)) {
        require_once $fileName;
    } else if (is_readable($fileNameApp)) {
        require_once $fileNameApp;
    } else if (is_readable($fileNameApp_)) {
        require_once $fileNameApp_;
    } else {
        trigger_error("Unable to load class: $fileName", E_USER_WARNING);
    }
}