<?php


namespace sattisoft\milano;


class Bootstrap
{
    /**
     * @return $this
     */
    public function load(){

        if (Milano::$App->_is_Dev === true || !is_file(Milano::$App->_pathAlias['cache'] . '/milano/load-cache.php')) {
            Milano::$App->_load['beforeRun'] = [];
            if (Milano::$App->_config['plugins'] !== NULL){
                foreach (Milano::$App->_config['plugins'] as $plugin => $data) {
                    $pluginClass = ((isset($data['namespace'])) ? $data['namespace'] : 'milano\plugins') . '\\' . $plugin;
                    if (method_exists($pluginClass, 'init')) {
                        (new $pluginClass)->init();
                    }
                }
            }
            $_load = '<?php return (object) [';
            foreach (Milano::$App->_load as $event => $load){
                $_load .= '\'' . $event . '\'=> function(){';
                    foreach ($load as $function){
                        $_load .= $function;
                    }
                $_load .= '},';
            }
            file_put_contents(Milano::$App->_pathAlias['cache'] . '/milano/load-cache.php', $_load . '];');
        }
        if (Milano::$App->_is_Dev === true || !is_file(Milano::$App->_pathAlias['cache'] . '/milano/require-cache.php')){
            $requireText = '<?php ';
            if (Milano::$App->_config['params']['require'] !== NULL){
                foreach (Milano::$App->_config['params']['require'] as $require){
                    $requireText .= $this->getRequireFileContent($require);
                }
            }
            file_put_contents(Milano::$App->_pathAlias['cache'] . '/milano/require-cache.php', (isset($require)) ? $requireText . ';': '');
        }

        require_once Milano::$App->_pathAlias['cache'] . '/milano/require-cache.php';
        Milano::$App->_load = require_once Milano::$App->_pathAlias['cache'] . '/milano/load-cache.php';
        return $this;
    }

    /**
     *
     */
     public function run(){
        Milano::$App->_load->beforeRun;


    }

    /**
     * @param $content
     * @return string
     */
    private function getRequireFileContent($content)
    {
        if (substr($content, 0, 1) === '@') {
            $content = Milano::$App->replaceDirName($content);
        }

        $require = '';

        if (is_file($content)){
            $require .= 'require_once' . '\'' . $content . '\';';
        }elseif(is_dir($content)){
            foreach ($content as $item){
                $item = $content . '/' . $item;
                $require .= $this->getRequireFileContent($item);
            }
        }

        return $require;
    }
}