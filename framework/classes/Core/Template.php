<?php

namespace Core;

class Template
{

    /**
     * Returns an object of Twig
     * @param string $template_name
     */
    public static function getTemplate($template_path, $template_name)
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem($template_path);
        $twig = new \Twig_Environment($loader, array());

        return $twig->loadTemplate($template_name);
    }
}