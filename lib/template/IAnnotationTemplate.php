<?php
namespace chilimatic\lib\template;

/**
 *
 * @author j
 * Date: 10/11/15
 * Time: 4:17 PM
 *
 * File: IAnnotationTemplate.php
 */

interface IAnnotationTemplate
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function getView($name);

}