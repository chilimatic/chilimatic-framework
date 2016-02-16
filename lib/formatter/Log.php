<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 06.02.15
 * Time: 00:11
 */

namespace chilimatic\lib\formatter;

class Log
{
    /**
     * @var array
     */
    private $map = [
        'date'      => '%d',
        'prefix'    => '%p',
        'message'   => '%m',
        'data'      => '%D'
    ];

    /**
     * @var string
     */
    public $formatString;

    /**
     * @param string $formatString
     */
    public function __construct($formatString = '%d %p %m %D')
    {
        $this->formatString = $formatString;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    public function format(array $input)
    {
        $output = $this->formatString;

        foreach ($input as $key => $value)
        {
            if (!isset($this->map[$key])) {
                continue;
            }

            if (is_array($value)) {
                $value = implode('\n\t', $value);
            }
            $output = str_replace($this->map[$key], $value, $output);
        }

        return $output;
    }

}