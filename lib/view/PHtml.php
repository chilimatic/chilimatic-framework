<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 28.11.14
 * Time: 17:49
 */
namespace chilimatic\lib\view;
use chilimatic\lib\exception\ViewException;

/**
 * Class PHtml
 *
 * @package chilimatic\lib\view
 */
class PHtml extends AbstractView
{
    /**
     * default rendering file extension
     */
    const FILE_EXTENSION = '.phtml';

    /**
     * @var string
     */
    protected $templateFile = '';

    /**
     * rendered final Content
     *
     * @var string
     */
    protected $content = '';


    /**
     * @return mixed|void
     */
    public function initRender()
    {
        if (!file_exists((string)$this->getTemplateFile())) {
            if ($this->getConfigVariable('templatePath')) {
                $this->setTemplateFile($this->getConfigVariable('templatePath') . self::FILE_EXTENSION);
            } else {
                throw new ViewException('No Tpl found:' . $this->getTemplateFile());
            }
        }
    }

    /**
     * @param string $templateFile
     *
     * @throws \LogicException
     * @throws \ErrorException
     * @return string
     */
    public function render($templateFile = '')
    {
        if ($templateFile) {
            $this->setTemplateFile($templateFile);
        }

        $this->initRender();

        if (!$this->getTemplateFile()) {
            throw new \LogicException('no template given');
        }


        try {
            ob_start();
            include $this->getTemplateFile();
            $this->content = ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw new \ErrorException($e->getMessage(), $e->getCode());
        }

        return $this->content;
    }


}