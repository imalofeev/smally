<?php
$debug_execution_start = microtime(true);

//require_once 'config/main.php'; // main site config
require_once 'config/develop.php'; // your develop config

session_start();

/**
 * Autoloader (PSR-0)
 *
 * @param string $class
 */
function __autoload($class)
{
    global $config;
    
    // clasess don't include in __autoload (e.g. libs)
    $classesExcluded = array('Twig');
    $classPieces = explode('_', $class);
    if (in_array($classPieces[0], $classesExcluded, true)) {
        return;
    }

    $className     = $config['path']['classes'] . '/' . ltrim($class, '\\');
    $classNameApp  = $config['path']['classes'] . '/App/' . $class . '/' . ltrim($class, '\\');
    $classNameApp_ = $config['path']['classes'] . '/App/' . ltrim($class, '\\');

    $fileName  = '';
    $namespace = '';

    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', '/', $namespace) . '/';
    }

    $fileName     .= str_replace('_', '/', $className)     . '.php';
    $fileNameApp  .= str_replace('_', '/', $classNameApp)  . '.php';
    $fileNameApp_ .= str_replace('_', '/', $classNameApp_) . '.php';

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

/**
 * print_r() in tags <pre>$var</pre>
 *
 * @param array $var
 */
function print_r_ex($var)
{
    echo '<pre style="margin-top: 0em; margin-bottom: 0em; padding: .5em; border: 1px solid #ace; background-color: #def;">';
    $bt = debug_backtrace();
    printf('<b>print_r_ex() called from </b>%s (line %d):', $bt[0]['file'], $bt[0]['line']);
    echo '</pre>';
    echo '<pre style=" margin-top: -1px; margin-bottom: 0em; padding: 1em; border: 1px solid #ace;">';
    print_r($var);
    echo '</pre>';
}

/**
 * var_dump() in tags <pre>$var</pre>
 *
 * @param array $var
 */
function var_dump_ex($var)
{
    echo '<pre style="margin-top: 0em; margin-bottom: 0em; padding: .5em; border: 1px solid #ace; background-color: #def;">';
    $bt = debug_backtrace();
    printf('<b>var_dump_ex() called from </b>%s (line %d):', $bt[0]['file'], $bt[0]['line']);
    echo '</pre>';
    echo '<pre style=" margin-top: -1px; margin-bottom: 0em; padding: 1em; border: 1px solid #ace;">';
    var_dump($var);
    echo '</pre>';
}


/**
 * Return debug execution time
 *
 * @return float time in microseconds
 */
function debug_execution_time()
{
    return microtime(true) - $GLOBALS['debug_execution_start'];
}

