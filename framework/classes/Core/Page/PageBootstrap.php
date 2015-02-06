<?php
namespace Core\Page;

/**
 * Page on Twitter Bootstrap
 */
class PageBootstrap extends Page
{
    const PATH_TO_TPL  = 'PageBootstrap/tpl';

    const TPL_HTML_START  = 'html_start.html';
    const TPL_HTML_END    = 'html_end.html';
    const TPL_HTML_HEADER = 'html_header.html';
    const TPL_HTML_FOOTER = 'html_footer.html';

    public $CSS = array(
        '/lib/bootstrap/css/bootstrap.min.css',
        '/classes/Core/Page/PageBootstrap/css/common.css',
    );

    public $javascriptsBody = array(
        "/lib/jquery/2.1.3/jquery-2.1.3.min.js",
        "/lib/bootstrap/js/bootstrap.min.js",
        "/classes/Core/Page/PageBootstrap/js/common.js",
    );
}

