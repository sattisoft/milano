<?php


namespace sattisoft\milano;


class Milano
{

    /**
     * @var object|Milano
     */
    public static $App;

    /**
     * @var
     */
    public $_cache;

    /**
     * @var
     */
    public $_is_Dev;

    /**
     * @var
     */
    public $_pathAlias;

    /**
     * @var
     */
    public $_config;

    /**
     * @var array
     */
    public $_load = [];

    /**
     * Milano constructor.
     * @param $is_Dev
     * @param $paths
     */
    public function __construct($is_Dev, $paths)
    {
        $this->_is_Dev = $is_Dev;
        $this->_pathAlias = $paths;
    }

    /**
     * @param $path
     * @return string
     */
    public function getPathAlias($path){
        $pattern = substr($path,0,strpos($path,'/'));
            if (isset(Milano::$App->_config['pathAlias'][$pattern]))
                $path = str_replace('@app',Milano::$App->_config['pathAlias'][$pattern],$path);
            return $path;
    }
}