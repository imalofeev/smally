<?php

namespace Core;

/**
 * Router HTTP-requests to classes.
 * Call when trying to access a non-existent script or if the relative URL begins with a capital letter (mod_rewrite rule)
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
 *
 */
class Router
{
    const HTTP_DEFAULT_CLASS = 'Main';

    const HTTP_DEFAULT_STATIC_METHOD = 'index';
    const HTTP_DEFAULT_OBJECT_METHOD = 'display';

    const HTTP_CALLABLE_SUFFIX       = '_HTTP';

    /**
     * Start routing
     *
     */
    public static function start()
    {
        list($url) = explode('?', $_SERVER['REQUEST_URI']);
        $methodParameters = explode('/', trim($url, '//'));

        $class = array_shift($methodParameters);

        if (empty($class)) {
            $class = self::HTTP_DEFAULT_CLASS;
        }

        if (!class_exists($class, true)) {
            HTTP::sendStatusCode('HTTP/1.1 404 Not Found', "Class $class not found.");
        } else {
            $ReflectionClass = new \ReflectionClass($class);
            $ClassDocBlock = DocBlock::fromString($ReflectionClass->getDocComment());

            $method = array_shift($methodParameters);

            if (intval($method)) {
                $objectID = $method;
                $method = array_shift($methodParameters);

                if (!$Object = self::callMethod($class, 'byID', array($objectID))) {
                    HTTP::sendStatusCode('HTTP/1.1 404 Not Found', "Object with ID $objectID not found.");
                } else {
                    $classOrObject = $Object;
                    if (!$method) {
                        if ($ClassDocBlock->hasTag('http_default_method')) {
                            $method = $ClassDocBlock->getTagValue('http_default_method');
                        } else {
                            $method = self::HTTP_DEFAULT_OBJECT_METHOD;
                        }
                    }
                }
            } else {
                $classOrObject = $class;
                if (!$method) {
                    if ($ClassDocBlock->hasTag('http_default_static_method')) {
                        $method = $ClassDocBlock->getTagValue('http_default_static_method');
                    } else {
                        $method = self::HTTP_DEFAULT_STATIC_METHOD;
                    }
                }
            }

            if (method_exists($classOrObject, $method . self::HTTP_CALLABLE_SUFFIX)) {
                $method = $method . self::HTTP_CALLABLE_SUFFIX;
            }

            self::callMethod($classOrObject, $method, $methodParameters);
        }
    }

    /**
     * Invoke method if it can be invoked else return 403 or 404
     *
     * @param string or object $classOrObject
     * @param string $method
     * @param array $methodParameters
     *
     * @return mixed
     */
    private static function callMethod($classOrObject, $method, $methodParameters)
    {
        $ReflectionClass = new \ReflectionClass($classOrObject);

        if (!$ReflectionClass->hasMethod($method)) {
            if (is_object($classOrObject)) {
                HTTP::sendStatusCode('HTTP/1.1 404 Not Found', "Object method $method not found.");
            } else {
                HTTP::sendStatusCode('HTTP/1.1 404 Not Found', "Method $method class $classOrObject not found.");
            }
        } else {
            $ReflectionMethod = new \ReflectionMethod($classOrObject, $method);
            if (!DocBlock::fromString($ReflectionMethod->getDocComment())->hasTag('http_callable')) {
                if (is_object($classOrObject)) {
                    HTTP::sendStatusCode('HTTP/1.1 403 Forbidden', "Access to method $method of object denied.");
                } else {
                    HTTP::sendStatusCode('HTTP/1.1 403 Forbidden', "Access to method $method class $classOrObject denied.");
                }
            } else {
                return call_user_func_array(array($classOrObject, $method), $methodParameters);
            }
        }
    }
}