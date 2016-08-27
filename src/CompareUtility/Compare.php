<?php
namespace CompareUtility;

class Compare
{
    private static $rootPath   = [];
    private static $scanList   = [];
    private static $ignoreList = [];
    private static $ignoreEveryList = [];

    public static function dump($var, $exit = false, $label = false, $echo = true)
    {

        // Store dump in variable
        ob_start();
        //var_dump( $var );
        print_r($var);
        $output = ob_get_clean();
        $label  = $label ? $label . ' ' : '';

        // Location and line-number
        $line      = '';
        $separator = "<p style='color:blue; margin:0; padding: 0;'>" . str_repeat("-", 100) . "</p>" . PHP_EOL;
        $caller    = debug_backtrace();
        if (count($caller) > 0) {
            $tmp_r = $caller[0];
            $line .= "<p style='color:blue; margin:0; padding: 0;'>Location:</p> => <span style='color:red'>" . $tmp_r['file'] . '</span>';
            $line .= " (" . $tmp_r['line'] . ')';
        }

        // Add formatting
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 6px; margin: 6px 0; text-align: left;">'
            . $label
            . $line
            . PHP_EOL
            . $separator
            . $output
            . '</pre>';

        // Output
        if ($echo == true) {
            echo $output;

            if ($exit) {
                die();
            }
        } else {
            return $output;
        }
    }

    public static function setRootPath($rootPath)
    {
        self::$rootPath = $rootPath;
    }

    public static function getRootPath()
    {
        return self::$rootPath;
    }

    public static function setScanList(array $scanList)
    {
        self::$scanList = $scanList;
    }

    public static function getScanList()
    {
        return self::$scanList;
    }

    public function setIgnoreList(array $ignoreList)
    {
        self::$ignoreList = $ignoreList;
    }

    public function getIgnoreList()
    {
        return self::$ignoreList;
    }

    public function setIgnoreEveryList(array $ignoreEveryList)
    {
        self::$ignoreEveryList = $ignoreEveryList;
    }

    public function getIgnoreEveryList()
    {
        return self::$ignoreEveryList;
    }

    public static function scan(){

        $files  = array('files'=>array(), 'dirs'=>array());
        $directories  = array();

        foreach (self::getScanList() as $row) {
            $last_letter   = $row[strlen($row) - 1];
            $directories[] = ($last_letter == '\\' || $last_letter == '/') ? $row : $row . '/';
        }

        while (sizeof($directories)) {
            $dir  = array_pop($directories);
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {

                    if ($file == '.' || $file == '..') {
                        continue;
                    }

                    // Set ignores from everywhere
                    if (in_array($file, self::getIgnoreEveryList())) {
                        continue;
                    }

                    // Set ignore for dirs and files
                    if (in_array($dir . $file, self::getIgnoreList())) {
                        continue;
                    }

                    $file  = $dir.$file;
                    if (is_dir($file)) {
                        $directory_path = $file.DIRECTORY_SEPARATOR;
                        array_push($directories, $directory_path);
                        // $files['dirs'][]  = $directory_path;
                        $files['dirs'][]  = str_replace(self::getRootPath(), '', $directory_path);
                    } elseif (is_file($file)) {
                        // $files['files'][]  = $file;
                        $files['files'][]  = str_replace(self::getRootPath(), '', $file);
                    }
                }
                closedir($handle);
            }
        }

        return $files;
    }
}
