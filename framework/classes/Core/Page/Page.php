<?php
namespace Core\Page;

use Core\Template;

/**
 * Abstract Page
 *
 * Usage scenario in children classes:
 *
 *      PageTB::Current()->$javascripts[] = 'path_to_file'
 *      PageTB::Current()->$CSS[] = 'path_to_file'
 *      ...
 * 		Page::Start('...')  at the beginning of the page
 *      render content
 * 		Page::Finish()      at the end of the page
 *
 */

abstract class Page
{
    const PATH_TO_TPL  = '';
    
    const TPL_HTML_START  = '';
    const TPL_HTML_END    = '';
    const TPL_HTML_HEADER = '';
    const TPL_HTML_FOOTER = '';

    /**
     * Current page
     *
     * @var static class
     */
    protected static $_CurrentPage;

    /**
     * Title
     *
     * @var string
     */
    public $title = 'Title';

    /**
     * Encoding
     *
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * Mime-type content
     *
     * @var string
     */
    protected $contentType = 'text/html';

    /**
     * Included CSS files in HEAD
     *
     * @var array
     */
    public $CSS = array();

    /**
     * Included CSS files in BODY
     *
     * @var array
     */
    public $CSSBody = array();

    /**
     * Inline CSS
     *
     * @var string
     */
    public $CSSInline;

    /**
     * Included Javascript files in HEAD
     *
     * @var array
     */
    public $javascripts = array();

    /**
     * Included Javascript files in BODY
     *
     * @var array
     */
    public $javascriptsBody = array();

    /**
     * Inline Javascript
     *
     * @var string
     */
    public $javascriptsInline;

    /**
     * Additional content in HEAD
     *
     * @var string
     */
    public $headContent;

    /**
     * Additional attributes in HTML
     *
     * @var array
     */
    public $htmlAttributes = array();

    /**
     * Additional attributes in BODY
     *
     * @var array
     */
    public $bodyAttributes = array();

    /**
     * Array with breadcrambs - filled through AddBreadcrumb()
     *
     * @var array
     */
    protected $breadcrumbs;

    final private function __construct() {}

    final private function __clone() {}

    /**
     * Return current page
     *
     * @return static
     */
    public static function Current()
    {
        if (!isset(static::$_CurrentPage)) {
            static::$_CurrentPage = new static;
        }
        return static::$_CurrentPage;
    }

    /**
     * Render page start
     *
     * @param string $title
     */
    public static function Start($title = '')
    {
        if ($title) static::Current()->title = $title;

        static::Current()->RenderStart();
    }

    /**
     * Render:
     *  HTTP-headers
     *  and <html><head>...</head><body>
     */
    protected function RenderStart()
    {
        $this->OutputHeaders();
        $this->RenderHTMLStart();
        $this->RenderHeader();
    }

    /**
     * Render HTTP-headers
     */
    public function OutputHeaders()
    {
        header('Content-type: ' . $this->contentType . '; charset=' . $this->encoding);
    }

    /**
     * Render:
     *  <html><head>...</head><body>
     */
    public function RenderHTMLStart()
    {
        $page['html_attributes'] = $this->htmlAttributes;
        $page['encoding'] = $this->encoding;
        $page['title'] = htmlspecialchars($this->title);
        $page['css'] = $this->CSS;
        $page['css_inline'] = $this->CSSInline;
        $page['javascripts'] = $this->javascripts;
        $page['optional_head_content'] = trim($this->headContent);
        $page['body_attributes'] = $this->bodyAttributes;

        $template = Template::getTemplate(__DIR__ . '/' . static::PATH_TO_TPL, static::TPL_HTML_START);
        $template->display($page);
    }

    /**
     * Render header
     */
    public function RenderHeader()
    {
        $template = Template::getTemplate(__DIR__ . '/' . static::PATH_TO_TPL, static::TPL_HTML_HEADER);
        $template->display(array());

        if ($this->breadcrumbs)  {
            $this->RenderBreadcrumbs();
        }
    }

    /**
     * Render page finish
     */
    public static function Finish()
    {
        static::Current()->RenderFinish();
    }

    /**
     * Render:
     *  footer, js and </body></html>
     */
    protected function RenderFinish()
    {
        $this->RenderFooter();
        $this->RenderHTMLEnd();
    }

    /**
     * Render footer
     */
    protected function RenderFooter()
    {
        global $debug;

        if ($debug) {
            $footer_values['debug'] = $debug;
            $footer_values['time_exec'] = number_format(debug_execution_time(), 3, ",", " ");
            $footer_values['memory_usage'] = number_format(memory_get_peak_usage(true)/1024/1024, 1, ",", " ");

            if($GLOBALS["debug_db_queries_count"]){
                $footer_values['debug_db_queries_count'] = $GLOBALS['debug_db_queries_count'];
                $footer_values['debug_heaviest_query_time'] =  number_format($GLOBALS['debug_heaviest_query_time'], 3, ",", " ");
                $footer_values['debug_heaviest_query'] = $GLOBALS['debug_heaviest_query'];
            } else {
                $footer_values['debug_db_queries_count'] = 0;
                $footer_values['debug_heaviest_query_time'] = 0;
                $footer_values['debug_heaviest_query'] = '-';
            }

        } else {
            $footer_values = array();
        }

        $template = Template::getTemplate(__DIR__ . '/' . static::PATH_TO_TPL, static::TPL_HTML_FOOTER);
        $template->display($footer_values);
    }

    /**
     * Render HTML end
     */
    public function RenderHTMLEnd()
    {
        $page['javascripts_body'] = $this->javascriptsBody;
        $page['javascript_inline'] = $this->javascriptsInline;
        $page['css_body'] = $this->CSSBody;

        $template = Template::getTemplate(__DIR__ . '/' . static::PATH_TO_TPL, static::TPL_HTML_END);
        $template->display($page);
    }

    /**
     * Add breadcrumbs
     *
     * @param string $title
     * @param string $url
     * @param string $icon
     * @param string $hint
     * @param bool   $inactive if true, then color grey
     */
    public function AddBreadcrumb($title, $url = '', $icon = '', $hint = '', $inactive = false)
    {
        $this->breadcrumbs[] = array(
            'title'    => trim($title),
            'url'      => trim($url),
            'icon'     => trim($icon),
            'hint'     => trim($hint),
            'inactive' => trim($inactive),
        );
    }

    /**
     * Render breadcrumbs
     */
    protected function RenderBreadcrumbs()
    {
    }

}

