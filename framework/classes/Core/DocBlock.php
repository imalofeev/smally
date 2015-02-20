<?php
namespace Core;

/**
 * Class for work with DocBlock comments
 */
class DocBlock
{
    /**
     * String with docblock comment
     *
     * @var string
     */
    private $_string;

    /**
     * @var bool
     */
    private $_isStringParsed;

    /**
     * Array with PHPDocTags
     *
     * @var array
     */
    private $_tags;

    /**
     * Strings without tags
     *
     * @var array
     */
    private $_descriptionStrings;

    /**
     *
     * @param string $string string with PHPDocTags
     */
    public function __construct($string) {
        $this->_string = $string;
    }

    /**
     * Return DocBlock Object
     *
     * @param string $string PHPDoc-комментарий
     *
     * @return DocBlock
     */
    public static function fromString($string) {
        return new DocBlock($string);
    }

    /**
     * @param string $tagName tag name
     *
     * @return boolean
     */
    public function hasTag($tagName) {
        if($this->_isStringParsed){
            if(array_key_exists($tagName, $this->_tags)) {
                return true;
            } else {
                return false;
            }
        }else{
            if(strstr($this->_string, '@'.$tagName)) {
                $this->parseTags();
                return $this->hasTag($tagName);
            }else{
                return false;
            }
        }
    }

    /**
     * Return PHPDocTag value, if it is't found then return NULL. If many values then return array.
     *
     * @param string $tagName
     *
     * @return mixed
     */
    public function getTagValue($tagName) {
        if(!$this->_isStringParsed) {
            $this->parseTags();
        }

        return $this->_tags[$tagName];
    }

    /**
     * Parse text comment and select tags. Selected tags saved in $tags array
     *
     */
    private function parseTags() {
        $strings = explode("\n",$this->_string);
        
        foreach($strings as $string) {
            $string = trim($string);

            if(substr($string,0,3)=='* @'){
                list($tagName,$tag_value) = explode(' ', substr($string,3),2);
                if(isset($this->_tags[$tagName]) && is_array($this->_tags[$tagName])) {
                    $this->_tags[$tagName][] = $tag_value;
                } else if (isset($this->_tags[$tagName])) {
                    $this->_tags[$tagName] = array($this->_tags[$tagName], $tag_value);
                } else {
                    $this->_tags[$tagName] = $tag_value;
                }
            }elseif(substr($string,0,2) == '* '){
                $this->_descriptionStrings[] = substr($string,2);
            }

        }

        $this->_isStringParsed = true;
    }

    /**
     * Return comment without tags
     * @return string
     */
    public function getDescription() {
        if(!$this->_isStringParsed) {
            $this->parseTags();
        }

        return implode("\n", $this->_descriptionStrings);
    }

    /**
     * Return tags array
     *
     * @return array
     */
    public function getTags() {
        if(!$this->_isStringParsed) {
            $this->parseTags();
        }

        return $this->_tags;
    }

    /**
     * Return tags values
     *
     * @param string $tagName
     *
     * @return array
     */
    public function getTagValues($tagName) {
        if(!$this->_isStringParsed) {
            $this->parseTags();
        }

        if(is_array($this->_tags[$tagName])){
            return $this->_tags[$tagName];
        }else{
            return array($this->_tags[$tagName]);
        }
    }

    /**
     * Return DocBlock Object on DockBlock class comment
     *
     * @param string $className 
     *
     * @return DocBlock
     */
    public static function fromClass($className) {
        $ClassReflection = new \ReflectionClass($className);
        return new DocBlock($ClassReflection->getDocComment());
    }

    /**
     * Return DocBlock Object on DockBlock property comment
     *
     * @param string $className
     * @param string $propertyName
     * 
     * @return DocBlock
     */
    public static function fromClassProperty($className,$propertyName) {
        $PropertyReflection=new \ReflectionProperty($className,$propertyName);
        return new DocBlock($PropertyReflection->getDocComment());
    }

    /**
     * Return DocBlock Object on DockBlock method comment
     *
     * @param string $className
     * @param string $methodName 
     *
     * @return DocBlock
     */
    public static function fromClassMethod($className,$methodName) {
        $MethodReflection=new \ReflectionMethod($className,$methodName);
        return new DocBlock($MethodReflection->getDocComment());
    }

}


