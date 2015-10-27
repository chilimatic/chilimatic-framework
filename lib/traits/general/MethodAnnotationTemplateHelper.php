<?php
namespace chilimatic\lib\traits\general;
use chilimatic\lib\config\Config;
use chilimatic\lib\parser\annotation\ViewParser;

/**
 *
 * @author j
 * Date: 10/12/15
 * Time: 5:23 PM
 *
 * File: MethodAnnotationTemplateHelper.php
 */

trait MethodAnnotationTemplateHelper
{

    /**
     * @param string $callerName
     *
     * @return array|string
     */
    public function getView($callerName)
    {
        $method = str_replace(__CLASS__ . '::', '', $callerName);
        if (!method_exists(__CLASS__, $method)) {
            return '';
        }

        $parser = new ViewParser();
        $reflection = new \ReflectionClass($this);
        $method = $reflection->getMethod($callerName);

        // checks the doc header of the class
        $tokens = $parser->parse($method->getDocComment());


        $class = new $tokens[0];
        $class->setTemplateFile(Config::get('document_root') . '/' . $tokens[1]);

        return $class;
    }
}