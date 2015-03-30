<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 28.11.14
 * Time: 17:49
 */
namespace chilimatic\lib\view;

/**
 * Class PHtml
 * @package chilimatic\lib\view
 */
class PHtml extends \chilimatic\lib\view\Generic
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
        if (!file_exists((string) $this->getTemplateFile())) {
            $this->setTemplateFile($this->getConfigVariable('templatePath') . self::FILE_EXTENSION);
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
        $this->setTemplateFile($templateFile);

        $this->initRender();

        if (!$this->getTemplateFile()){
            throw new \LogicException('no template given');
        }

        try {
            ob_start();
            include $this->templateFile;
            $this->content = ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw new \ErrorException($e->getMessage(), $e->getCode());
        }

        return $this->content;
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->templateFile;
    }

    /**
     * @param string $templateFile
     *
     * @return $this
     */
    public function setTemplateFile($templateFile)
    {
        if (!$templateFile) return $this;
        $this->templateFile = (string) $templateFile;
        return $this;
    }
}