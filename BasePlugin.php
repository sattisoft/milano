<?php


namespace sattisoft\milano;


abstract Class BasePlugin
{
    abstract public function checkConfig();

    abstract public function initPlugin();

    /**
     * @return string
     */
    protected function Pluginname(){

        return 'Plugin';

    }

    /**
     * @param $functionName
     * @param string $event = "beforeRun"
     */
    protected function registerFunction($functionName, $event = "beforeRun"){

        if (!isset(Milano::$App->_load[$event]))
            Milano::$App->_load[$event] = [];

        $functionName = ((isset(Milano::$App->_config[$this->Pluginname()]['namespace'])) ? Milano::$App->_config[$this->Pluginname()]['namespace'] : 'milano\plugins') . '\\' . '::' . $functionName . '();';
        array_push(Milano::$App->_load[$event], $functionName);

    }
}