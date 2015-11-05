<?php
namespace chilimatic\lib\tool;

use \chilimatic\lib\exception\FileException;
use \chilimatic\lib\config\Config;

/**
 * Class Pdf
 *
 * @package chilimatic\lib\tool
 */
class Pdf
{


    /**
     * Concats multiple pdfs together using the shell program
     * pdftk
     *
     * @param $path_array array
     * @param $target_file_name
     * @param bool $multiple
     *
     * @throws \chilimatic\lib\exception\FileException|\Exception
     * @return bool
     */
    public static function concat_pdf($path_array, $target_file_name, $multiple = false)
    {

        $cmd       = 'pdftk ';
        $cmd_files = '';
        $unique    = array();
        try {
            if (empty($path_array)) {
                throw new FileException((string)"\$path_array was empty", (int)Config::get('file_error'), (int)Config::get('error_lvl_low'), (string)__FILE__, (string)__LINE__);
            }

            foreach ($path_array as $pdf_file) {
                if (!file_exists($pdf_file) || in_array($pdf_file, $unique)) continue;
                if ($multiple === false) {
                    $unique[] = $pdf_file;
                }

                $cmd_files .= (string)" " . escapeshellarg(addslashes(stripslashes($pdf_file))) . " ";
            }

            unset($pdf_file);

            if (empty($cmd_files)) {
                throw new FileException((string)"No valid File within the whole array", (int)Config::get('file_error'), (int)Config::get('error_lvl_low'), (string)__FILE__, (string)__LINE__);
            }

            $cmd .= (string)$cmd_files;

            // so that the output wont be lost
            if (empty($target_file_name)) {
                $target_file_name = Config::get('storage_root') . "/" . Config::get('pdf_default_path') . "/concat_" . Tool::random_string(10) . ".pdf";
            }

            system($cmd . " cat output $target_file_name", $output);

            if (strpos(strtolower($output), 'err') !== false) {
                throw new FileException((string)"Error generating the pdf file $cmd\n$output", (int)Config::get('file_error'), (int)Config::get('error_lvl_low'), (string)__FILE__, (string)__LINE__);
            }
            unset($cmd, $output);
        } catch (FileException $e) {
            throw $e;
        }

        return true;
    }
}