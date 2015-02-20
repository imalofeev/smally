<?php

use Core\Template;
use Core\Page\PageBootstrap;

/**
 * Main class
 *
 * Default invoked class, if URI haven't got ClassName
 */
class Main
{

    /**
     * Main page
     *
     * @http_callable
     */
    public static function index()
    {
        $template = Template::getTemplate(__DIR__ . '/tpl', 'index.html');

        PageBootstrap::start();
        $template->display(array());
        PageBootstrap::finish();
    }
}