<?php

namespace Core;
use Core\DB\DBPgConnect;

/**
 * Abstract class Application
 */
abstract class Application
{
	
	/**
	 * Objects cache (Key is ObjectID)
	 * @var array
	 */
	protected static $_cache;
	
	
	/**
	 * Return application class name
	 *
	 * @return string
	 */
	public static function getApplicationClassName()
	{
		return get_called_class();
	}
	
	/**
	 * Return path to application
	 *
	 * @return string
	 */
	public static function getApplicationURL()
	{
		return sprintf('/%s/', static::getApplicationClassName());
	}

	/**
	 * Return application table name
	 *
	 * @return string
	 */
	public static function getApplicationTableName()
	{
		return static::getApplicationTagValue('db_table');
	}
	
	/**
	 * Return key property name (column in DB)
	 *
	 * @return string
	 */
	public static function getApplicationDBPrimaryKey()
	{
		$ClassReflection = new \ReflectionClass(static::getApplicationClassName());
		foreach ($ClassReflection->getProperties() as $Property) {
			if (DocBlock::fromString($Property->getDocComment())->hasTag('db_primary_key')) {
				$pkColumnName = $Property->name;
				break;
			}
		}
		return $pkColumnName;
	}
	
	/**
	 * Return phpDocTag value
     *
	 * @param string $phpDocTag 
	 *
	 * @return string
	 */
	public static function getApplicationTagValue($phpDocTag)
	{
        if (static::isApplicationHasTag($phpDocTag)) {
            return static::getApplicationDocBlock()->getTagValue($phpDocTag);
        } else {
            return '';
        }
	}
	
	/**
	 * Determines whether there is a phpDocTag
	 *
	 * @param string $phpDocTag tag name
	 *
	 * @return boolean
	 */
	public static function isApplicationHasTag($phpDocTag)
	{
		return static::getApplicationDocBlock()->hasTag($phpDocTag);
	}

	/**
	 * Return application phpDoc 
	 *
	 * @return DocBlock
	 */
	public static function getApplicationDocBlock()
	{
		return DocBlock::fromClass(static::getApplicationClassName());
	}

	/**
	 * @http_callable
	 *
     * Build Object on his ID
     *
	 * @param integer $id ID
	 *
	 * @return object
	 */
	public static function byID($id)
	{
		if (isset(static::$_cache[$id])) {
			return static::$_cache[$id];
		} else {
            $sql = "SELECT *
                      FROM " . DBPgConnect::formatRegclassTablename(static::getApplicationTableName()) . "
                      WHERE " . static::getApplicationDBPrimaryKey() . " = " . DBPgConnect::formatValue($id);
			$db_row = DBPgConnect::getRow($sql);
			$Object = new static($db_row);
			static::$_cache[$id] = $Object;
			return $Object;
		}
	}

	/**
	 * @param array $db_row array(property => value)
	 */
    public function __construct($db_row)
    {
		foreach ($db_row as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * Return breadcrumb to object
	 *
	 * @return string
	 */
	protected function getBreadcrumbItemURL()
	{
		return static::getApplicationURL() . $this->id;
	}
}

