<?php
namespace Core\Page;
use Core\Template;

/**
 * Abstract Page
 *
 * Usage scenario in children classes:
 *
 *      PageTB::getInstance()->$javascripts[] = 'path_to_file'
 *      PageTB::getInstance()->$CSS[] = 'path_to_file'
 *      ...
 * 		Page::start('...')  at the beginning of the page
 *      render content
 * 		Page::finish()      at the end of the page
 *
 */

abstract class Page
{
    const TEMPLATE_PATH  = '';
    
    const TPL_HTML_START  = '';
    const TPL_HTML_END    = '';
    const TPL_HTML_HEADER = '';
    const TPL_HTML_FOOTER = '';

    /**
     * Current page
     *
     * @var static class
     */
    protected static $_Instance;

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
     * Array with breadcrambs - filled through addBreadcrumb()
     *
     * @var array
     */
    protected $_breadcrumbs = array();

    final private function __construct() {}

    final private function __clone() {}

    /**
     * Return current page
     *
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(static::$_Instance)) {
            static::$_Instance = new static;
        }
        return static::$_Instance;
    }

    /**
     * Render page start
     *
     * @param string $title
     */
    public static function start($title = '')
    {
        if ($title) static::getInstance()->title = $title;

        static::getInstance()->renderStart();
    }

    /**
     * Render:
     *  HTTP-headers
     *  and <html><head>...</head><body>
     */
    protected function renderStart()
    {
        $this->sendOutputHeaders();
        $this->renderHTMLStart();
        $this->renderHeader();
    }

    /**
     * Render HTTP-headers
     */
    public function sendOutputHeaders()
    {
        header('Content-type: ' . $this->contentType . '; charset=' . $this->encoding);
    }

    /**
     * Render:
     *  <html><head>...</head><body>
     */
    public function renderHTMLStart()
    {
        $page['html_attributes'] = $this->htmlAttributes;
        $page['encoding'] = $this->encoding;
        $page['title'] = htmlspecialchars($this->title);
        $page['css'] = $this->CSS;
        $page['css_inline'] = $this->CSSInline;
        $page['javascripts'] = $this->javascripts;
        $page['optional_head_content'] = trim($this->headContent);
        $page['body_attributes'] = $this->bodyAttributes;

        $template = Template::getTemplate(__DIR__ . '/' . static::TEMPLATE_PATH, static::TPL_HTML_START);
        $template->display($page);
    }

    /**
     * Render header
     */
    public function renderHeader()
    {
        $template = Template::getTemplate(__DIR__ . '/' . static::TEMPLATE_PATH, static::TPL_HTML_HEADER);
        $template->display(array('breadcrumbs' => $this->_breadcrumbs));
    }

    /**
     * Render page finish
     */
    public static function finish()
    {
        static::getInstance()->renderFinish();
    }

    /**
     * Render:
     *  footer, js and </body></html>
     */
    protected function renderFinish()
    {
        $this->renderFooter();
        $this->renderHTMLEnd();
    }

    /**
     * Render footer
     */
    protected function renderFooter()
    {
        $footer_values = array();

        // $debug define in config develop.php
        global $debug;
        if ($debug) {
            $footer_values['debug'] = $debug;
            $footer_values['time_exec'] = number_format(microtime(true) - $GLOBALS['debug_execution_start'], 3, ",", " ");
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
        }

        $template = Template::getTemplate(__DIR__ . '/' . static::TEMPLATE_PATH, static::TPL_HTML_FOOTER);
        $template->display($footer_values);
    }

    /**
     * Render HTML end
     */
    public function renderHTMLEnd()
    {
        $page['javascripts_body'] = $this->javascriptsBody;
        $page['javascript_inline'] = $this->javascriptsInline;
        $page['css_body'] = $this->CSSBody;

        $template = Template::getTemplate(__DIR__ . '/' . static::TEMPLATE_PATH, static::TPL_HTML_END);
        $template->display($page);
    }

    /**
     * Add breadcrumbs
     *
     * @param string $title
     * @param string $url
     * @param string $icon twitter bootsrap glyphicon class for icon
     * @param string $hint
     */
    public function addBreadcrumb($title, $url = '', $glyphicon  = '', $hint = '')
    {
        $this->_breadcrumbs[] = array(
            'title' => trim($title),
            'url'   => trim($url),
            'glyphicon'  => trim($glyphicon),
            'hint'  => trim($hint),
        );
    }
}

