<?php
/**
 *
 * @author j
 * Date: 4/22/15
 * Time: 9:46 PM
 *
 * File: ToFile.php
 */

namespace chilimatic\lib\log\client;

class ToFile extends AbstractClient
{
    /**
     * @var string
     */
    private $targetFile;


    /**
     * @path
     */
    public function send()
    {
        $msgString = '';
        foreach ($this->logMessages as $message) {
            $msgString .= $message;
        }

        if (!$this->targetFile) {
            error_log($msgString);
        } else {
            file_put_contents($this->targetFile, $msgString, FILE_APPEND);
        }
    }

}