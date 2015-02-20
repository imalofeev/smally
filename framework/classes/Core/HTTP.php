<?php
namespace Core;
use Core\Page\PageBootstrap;

/**
 * Class for work with HTTP
 */
class HTTP
{
    /**
     * Send HTTP status code to client
     *
     * @param string $code
     * @param string $message
     */
    public static function sendStatusCode($code, $message)
    {
        header($code);
        PageBootstrap::renderStatusCodePage($message);
        exit;
    }

    /**
     * Send JSON to client
     *
     * @param mixed $value
     */
    public static function sendJSON($value)
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache');
        echo json_encode($value);
        exit;
    }

    /**
     * Send HTML to client
     *
     * @param mixed $value
     */
    public static function sendHTML($value)
    {
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: no-store, no-cache');
        echo $value;
        exit;
    }
}

