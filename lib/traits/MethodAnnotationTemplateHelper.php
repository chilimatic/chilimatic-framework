<?php
namespace chilimatic\lib\traits\general;
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
        if (!method_exists(__CLASS__, $callerName)) {
            return '';
        }

        $parser = new ViewParser();
        $reflection = new \ReflectionClass($this);
        $method = $reflection->getMethod($callerName);

        // checks the doc header of the class
        $tokens = $parser->parse($method->getDocComment());




        return $tokens;
    }
}