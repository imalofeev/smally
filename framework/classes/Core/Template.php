<?php

namespace Core;

class Template
{

    /**
     * Returns an object of Twig
     *
     * @param $templatePath
     * @param $templateName
     * @param array $environmentParams
     *
     * @return \Twig_TemplateInterface
     */
    public static function getTemplate($templatePath, $templateName, $environmentParams = array())
    {
        $loader = new \Twig_Loader_Filesystem($templatePath);
        $twig = new \Twig_Environment($loader, $environmentParams);

        return $twig->loadTemplate($templateName);
    }
}