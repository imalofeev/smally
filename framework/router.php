<?php
use Core\Access;

/**
 * Router HTTP-requests to classes. Call when trying to access a non-existent script or
 * If the relative URL begins with a capital letter (mod_rewrite-rule in .htaccess).
 * Implemented the following scheme HTTP-requests:
 * (1) /ClassName — invoke static method Index_HTTP or Index (or other, which set in class tag @http_default_static_method)
 *     Parameters are not supported.
 * (2) /ClassName/MethodName/param1/param2/param3... — invoke static method MethodName_HTTP or MethodName
 *     and passes parameters as method parameters param1, param2, param3
 * (3) /ClassName/number — create ClassName object, use the method byID(number) and invoke its
 *     method Display_HTTP or Display (or other, which set in class tag @http_default_method). Parameters are not supported.
 * (4) /ClassName/number/MethodName/param1/param2/param3... — create ClassName object, use the method byID(number)
 *     and invoke its method MethodName_HTTP or MethodName and passes parameters as method parameters param1, param2, param3
 * All methods which invokes through a browser, must have PHPDocTag @http_callable
 */
require_once 'site.php';

define('HTTP_CALLABLE_SUFFIX',       '_HTTP');
define('HTTP_DEFAULT_STATIC_METHOD', 'Index');
define('HTTP_DEFAULT_OBJECT_METHOD', 'Display');

list($url) = explode('?', $_SERVER['REQUEST_URI']);
$methodParameters = explode('/', trim($url, '//'));

$class = array_shift($methodParameters);

if (!class_exists($class, true)) {
    Core\HTTP::NotFound404("Class $class not found.");
} else {
    $ReflectionClass = new \ReflectionClass($class);
    $ClassDocBlock = Core\DocBlock::fromString($ReflectionClass->getDocComment());

    $method = array_shift($methodParameters);

    if (intval($method)) {
        $objectID = $method;
        $method = array_shift($methodParameters);

        if (!$Object = call_method($class, 'byID', array($objectID))) {
            Core\HTTP::NotFound404("Object with ID $objectID not found.");
        } else {
            $classOrObject = $Object;
            if (!$method) {
                if ($ClassDocBlock->hasTag('http_default_method')) {
                    $method = $ClassDocBlock->getTagValue('http_default_method');
                } else {
                    $method = HTTP_DEFAULT_OBJECT_METHOD;
                }
            }
        }
    } else {
        $classOrObject = $class;
        if (!$method) {
            if ($ClassDocBlock->hasTag('http_default_static_method')) {
                $method = $ClassDocBlock->getTagValue('http_default_static_method');
            } else {
                $method = HTTP_DEFAULT_STATIC_METHOD;
            }
        }
    }

    if (method_exists($classOrObject, $method . HTTP_CALLABLE_SUFFIX)) {
        $method = $method . HTTP_CALLABLE_SUFFIX;
    }

    call_method($classOrObject, $method, $methodParameters);
}

unset($url, $methodParameters, $class, $method);
unset($Object, $ReflectionClass, $ClassDocBlock);


/**
 * Invoke method if it can else return 403 or 404
 *
 * @param string or object $classOrObject
 * @param string $method
 * @param array $methodParameters
 *
 * @return mixed
 */
function call_method($classOrObject, $method, $methodParameters)
{
    $ReflectionClass = new \ReflectionClass($classOrObject);

    if (!$ReflectionClass->hasMethod($method)) {
        if (is_object($classOrObject)) {
            Core\HTTP::NotFound404("Object method $method not found.");
        } else {
            Core\HTTP::NotFound404("Method $method class $classOrObject not found.");
        }
    } else {
        $ReflectionMethod = new \ReflectionMethod($classOrObject, $method);
        if (!Core\DocBlock::fromString($ReflectionMethod->getDocComment())->hasTag('http_callable')) {
            if (is_object($classOrObject)) {
                Core\HTTP::Forbidden403("Access to method $method of object denied.");
            } else {
                Core\HTTP::Forbidden403("Access to method $method class $classOrObject denied.");
            }
        } else {
            return call_user_func_array(array($classOrObject, $method), $methodParameters);
        }
    }
}

