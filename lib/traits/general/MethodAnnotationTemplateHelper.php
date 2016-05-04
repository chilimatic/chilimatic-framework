<?php
namespace chilimatic\lib\traits\general;

use chilimatic\lib\Parser\Annotation\AnnotationViewParser;
use chilimatic\lib\view\AbstractView;

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
     * @return AbstractView
     */
    public function getViewFromAnnotation($callerName, $relativePath)
    {
        $method = str_replace(__CLASS__ . '::', '', $callerName);
        if (!method_exists(__CLASS__, $method)) {
            return '';
        }

        $parser = new AnnotationViewParser();
        $reflection = new \ReflectionClass($this);
        $method = $reflection->getMethod($callerName);

        // checks the doc header of the class
        $tokens = $parser->parse($method->getDocComment());

        /**
         * @var \chilimatic\lib\view\AbstractView $class
         */
        $class = new $tokens[0];

        if ($relativePath && $tokens[1]) {
            $class->setTemplateFile($relativePath . '/' . $tokens[1]);
        }

        return $class;
    }
}