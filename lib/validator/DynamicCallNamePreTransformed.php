<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 6:06 PM
 *
 * File: FunctionCall.php
 */

namespace chilimatic\lib\validator;

use chilimatic\lib\interfaces\IFlyWeightValidator;

/**
 * Class DynamicFunctionNamePreTransformed
 *
 * @package chilimatic\lib\parser
 */
class DynamicCallNamePreTransformed implements IFlyWeightValidator
{
    /**
     * the local Parser
     *
     * @var string
     */
    const PARSE_DELIMITER = '-';

    /**
     * list of invalid characters in a dynamic function call name
     *
     * @var string
     */
    private $invalidCharacters = '/[|,.:;?`!"§\'%&$\/()=*<>]/';

    /**
     * @var string
     */
    private $errorMsg;

    /**
     * @param mixed $value
     *
     * @return string
     */
    function __invoke($value)
    {
        return $this->validate($value);
    }

    /**
     * @param string $content
     *
     * @return string
     */
    public function validate($content)
    {
        if (!$content) {
            return false;
        }
        if (preg_match('/[A-Z]/', $content)) {
            return false;
        } elseif (mb_strpos($content, self::PARSE_DELIMITER) === 0) {
            $this->errorMsg = self::PARSE_DELIMITER . ' is not allowed to be at the beginning of the callname';

            return false;
        } elseif (mb_strpos($content, self::PARSE_DELIMITER) == (strlen($content) - 1)) {
            $this->errorMsg = self::PARSE_DELIMITER . ' is not allowed to be at the end of the callname';

            return false;
        } elseif (preg_match($this->invalidCharacters, $content)) {
            $this->errorMsg = $this->invalidCharacters . ' are not allowed to be in the callname';

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * @return string
     */
    public function getInvalidCharacters()
    {
        return $this->invalidCharacters;
    }
}