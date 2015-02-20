<?php
namespace Core\Page;
use Core\Template;

/**
 * Page on Twitter Bootstrap
 */
class PageBootstrap extends Page
{
    const TEMPLATE_PATH  = 'PageBootstrap/tpl';

    const TPL_HTML_START  = 'html_start.html';
    const TPL_HTML_END    = 'html_end.html';
    const TPL_HTML_HEADER = 'html_header.html';
    const TPL_HTML_FOOTER = 'html_footer.html';

    public $CSS = array(
        '/lib/bootstrap/css/bootstrap.min.css',
        '/classes/Core/Page/PageBootstrap/css/common.css',
    );

    public $javascriptsBody = array(
        "/lib/jquery/1.11.0/jquery-1.11.0.min.js",
        "/lib/bootstrap/js/bootstrap.min.js",
        "/classes/Core/Page/PageBootstrap/js/common.js",
    );

    /**
     * Render Page with status code message
     *
     * @param string $message
     */
    public static function renderStatusCodePage($message)
    {
        $template = Template::getTemplate(__DIR__ . '/' . static::TEMPLATE_PATH, 'status_code.html');

        static::start();
        $template->display(array('message' => $message));
        static::finish();
    }
}

