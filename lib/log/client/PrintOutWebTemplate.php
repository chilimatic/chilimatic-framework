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
     * @view chilimatic\lib\view\PHtml
     * @viewTemplate app/module/main/view/Ooops.phtml
     */
    public function send()
    {
        $view = $this->getView(__FUNCTION__);
        $view->logMessages = $this->logMessages;
        echo $view->render();
    }

}