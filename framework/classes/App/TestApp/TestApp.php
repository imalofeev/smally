<?php

use Core\Page\PageBootstrap;
use Core\Template;
use Core\Application;

/**
 * Class TestApp
 *
 * If method haven't got @http_callable, it isn't called by browser
 *
 * Static methods:
 * /TestApp - call method Index
 * /TestApp/StaticMethod_1 - call method StaticMethod_1
 * /TestApp/StaticMethod_2/param1/param2 - call method StaticMethod_1 with params(param1 and param2)
 *
 * If you want to create object from DB via methods Display, ObjectMethod_1, ObjectMethod_2,
 * you must configure connection in class DBPgConnect and create field in DB in table public.test_table: ID (with value = 1), property_1(any value), property_2(any value).
 * And then you can create object via address lines in browser:
 *  /TestApp/1 - create object and call method Display, "1" is ID object in DB
 *  /TestApp/1/ObjectMethod_1 - create object and call method ObjectMethod_1, "1" is ID object in DB
 *  /TestApp/1/ObjectMethod_2/param1/param2 - create object and call method ObjectMethod_2 with params(param1 and param2), "1" is ID object in DB
 *
 * If you are going to create objects from DB, you must add tag "db_table" with table name in PHPDocTag:
 * @db_table public.test_table
 */
class TestApp extends Application
{

    // Object property = field in DB:
    /**
     * ID object in DB
     * Field in DB with name "id"
     *
     * If you are going to create objects from DB, you must add tag "db_primary_key" in PHPDocTag,
     * who is identifier of object:
     * @db_primary_key
     *
     * @db_type serial
     * @var integer
     */
    public $id;

    /**
     * Property_1
     * Field in DB with name "_property_1"
     *
     * @db_type text
     * @var integer
     */
    protected $_property_1;

    /**
     * Property_2
     * Field in DB with name "_property_2"
     *
     * @db_type integer
     * @var integer
     */
    private $_property_2;

    // ... and other properties = fields in DB


    // Other property for object (not in DB):
    /**
     * Property_3
     *
     * @var integer
     */
    public $property_3;

    /**
     * Property_4
     *
     * @var string
     */
    protected $_property_4;

    /**
     * Property_5
     *
     * @var boolean
     */
    private $_property_5;

    // ... and other properties only for class or object (not in DB)


	/**
     * @http_callable
	 */
	public static function index()
	{
        $template = Template::getTemplate(__DIR__ . '/tpl', 'index.html');

        PageBootstrap::getInstance()->addBreadcrumb('Main', '/', 'glyphicon-home', 'Go to main page');
        PageBootstrap::getInstance()->addBreadcrumb('TestApp', '', '', 'TestApp page');
        PageBootstrap::start();
        $template->display(array());
        PageBootstrap::finish();

	}

    /**
     * @http_callable
     */
    public static function staticMethod_1()
    {
        $template = Template::getTemplate(__DIR__ . '/tpl', 'static_method_1.html');

        PageBootstrap::getInstance()->addBreadcrumb('Main', '/', 'glyphicon-home', 'Go to main page');
        PageBootstrap::getInstance()->addBreadcrumb('TestApp', '/TestApp');
        PageBootstrap::getInstance()->addBreadcrumb('StaticMethod_1');
        PageBootstrap::start();
        $template->display(array());
        PageBootstrap::finish();
    }

    /**
     * @http_callable
     */
    public static function staticMethod_2($param1, $param2)
    {
        $template = Template::getTemplate(__DIR__ . '/tpl', 'static_method_2.html');

        PageBootstrap::getInstance()->addBreadcrumb('Main', '/', 'glyphicon-home', 'Go to main page');
        PageBootstrap::getInstance()->addBreadcrumb('TestApp', '/TestApp');
        PageBootstrap::getInstance()->addBreadcrumb('StaticMethod_2');
        PageBootstrap::start();
        $template->display(array('param1' => $param1, 'param2' => $param2));
        PageBootstrap::finish();

    }

    /**
     * @http_callable
     */
    public function display()
    {
        $template = Template::getTemplate(__DIR__ . '/tpl', 'display.html');

        $objectData = $this->getObjectArray();

        PageBootstrap::getInstance()->addBreadcrumb('Main', '/', 'glyphicon-home', 'Go to main page');
        PageBootstrap::getInstance()->addBreadcrumb('TestApp', '/TestApp');
        PageBootstrap::getInstance()->addBreadcrumb('Object ' . $objectData['id']);
        PageBootstrap::start();
        $template->display(array('object_data' => $objectData));
        PageBootstrap::finish();

    }

    /**
     * @http_callable
     */
    public function objectMethod_1()
    {
        $template = Template::getTemplate(__DIR__ . '/tpl', 'object_method_1.html');

        $objectData = $this->getObjectArray();

        PageBootstrap::getInstance()->addBreadcrumb('Main', '/', 'glyphicon-home', 'Go to main page');
        PageBootstrap::getInstance()->addBreadcrumb('TestApp', '/TestApp');
        PageBootstrap::getInstance()->addBreadcrumb('Object ' . $objectData['id'], '/TestApp/' . $objectData['id']);
        PageBootstrap::getInstance()->addBreadcrumb('ObjectMethod_1');
        PageBootstrap::start();
        $template->display(array('object_data' => $objectData));
        PageBootstrap::finish();

    }

    /**
     * @http_callable
     */
    public function objectMethod_2($param1, $param2)
    {
        $template = Template::getTemplate(__DIR__ . '/tpl', 'object_method_2.html');

        $objectData = $this->getObjectArray();

        PageBootstrap::getInstance()->addBreadcrumb('Main', '/', 'glyphicon-home', 'Go to main page');
        PageBootstrap::getInstance()->addBreadcrumb('TestApp', '/TestApp');
        PageBootstrap::getInstance()->addBreadcrumb('Object ' . $objectData['id'], '/TestApp/' . $objectData['id']);
        PageBootstrap::getInstance()->addBreadcrumb('ObjectMethod_2');
        PageBootstrap::start();
        $template->display(array('param1' => $param1,
                                 'param2' => $param2,
                                 'object_data' => $objectData));
        PageBootstrap::finish();

    }

    /**
     * Return
     *
     * @return mixed
     */
    private function getObjectArray()
    {
        foreach ($this as $property => $propertyValue) {
            $objectData[$property] = $propertyValue;
        }

        return $objectData;
    }
}