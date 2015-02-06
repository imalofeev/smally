<?php
namespace Core;

/**
 * Class for work with HTTP
 */
class HTTP
{
    /**
     * 403 Forbidden
     *
     * @param string $message
     */
    public static function Forbidden403($message = '403 Forbidden')
    {
        header('HTTP/1.0 403 Forbidden');
        echo $message;
    }

    /**
     * 404 Not Found
     *
     * @param string $message
     */
    public static function NotFound404($message = '404 Not Found')
    {
        header('HTTP/1.0 404 Not Found');
        echo $message;
    }

    /**
     * 503 Service Unavailable
     *
     * @param string $message
     */
    public static function ServiceUnavailable503($message = '503 Service Unavailable')
    {
        header('HTTP/1.0 503 Service Unavailable');
        echo $message;
    }

    /**
     * Redirect to static method
     *
     * @param string $class class name
     * @param string $method method name
     * @param array $parameters
     */
    public static function redirectToClass($class, $method = '', $parameters = null)
    {
        $URL = '/' . $class;
        if (strlen(trim($method))>0) {
            $URL .= '/' . $method;
        }
        if ($parameters) {
            foreach ($parameters as $parameter) {
                $URL .= '/' . urlencode($parameter);
            }
        }
        $URL .= '/';
        self::redirectToURL($URL);
    }

    /**
     * Redirect to Object method
     *
     * @param string $object object name
     * @param integer $instance object ID
     * @param string $method method name
     * @param array $parameters
     */
    public static function redirectToObject($object, $instance, $method = null, $parameters = null)
    {
        if ($method) {
            $URL = "/$object/$instance/$method";
            if ($parameters) {
                foreach ($parameters as $parameter) {
                    $URL .= '/' . urlencode($parameter);
                }
            }
            self::redirectToURL($URL);
        } else {
            self::redirectToURL('/' . $object . '/' . $instance);
        }

    }

    /**
     * Build JSON form PHP array and send it to client
     *
     * @param array $array
     */
    public static function sendJSONArray($array = array())
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache');
        echo json_encode($array);
        exit;
    }
}

