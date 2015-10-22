<?php
/**
 *
 * @author j
 * Date: 10/9/15
 * Time: 4:49 PM
 *
 * File: GetForWeb.php
 */

namespace chilimatic\lib\log\client;

use chilimatic\lib\template\IAnnotationTemplate;
use chilimatic\lib\traits\general\MethodAnnotationTemplateHelper;

class PrintOutWebTemplate extends AbstractClient implements IAnnotationTemplate
{
    use MethodAnnotationTemplateHelper;

    /**
     * @view Phtml
     * @viewTemplate app/main/view/Ooops.phtml
     */
    public function send()
    {
        $this->getView(__METHOD__);
    }

}