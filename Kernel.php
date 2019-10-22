<?php


namespace sattisoft\milano;


class Kernel
{
    /**
     * @param array $paths
     * @param null $is_DEV = NULL
     * @param null $config = NULL
     * @param bool $runSelf = true
     */
    public function init($paths = [], $is_DEV = NULL, $config = NULL, $runSelf = true)
    {
        if ($is_DEV === NULL){
            if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' && $_SERVER['SERVER_ADDR'] === '127.0.0.1'){
                $is_DEV = true;
            }else{
                $is_DEV = false;
            }
        };

        if ($paths === []){

            $cachefile = $paths['milano'] . '/cache/paths-cache.php';

            if (is_file($paths['milano'] . '/cache/paths-cache.php') && $is_DEV === FALSE){
                $paths = require_once $cachefile;
            }else{
                if (!isset($paths['app']))
                    $paths['app'] = dirname($paths['milano']);

                if (!isset($paths['config']))
                    $paths['config'] = $paths['app'] . '/config';

                if (!isset($paths['cache']))
                    $paths['cache'] = $paths['app'] . '/cache';

                if (!isset($paths['vendor']))
                    $paths['vendor'] = $paths['app'] . '/vendor';

                if (!isset($paths['routes']))
                    $paths['routes'] = $paths['app'] . '/routes';

                if (!isset($paths['mainConfig']))
                    $paths['mainConfig'] = '';

                if (!isset($paths['params']))
                    $paths['params'] = '';

                if (!isset($paths['appConfig']))
                    $paths['appConfig'] = '';

                if (!isset($paths['cookieConfig']))
                    $paths['cookieConfig'] = '';

                if (!isset($paths['requireConfig']))
                    $paths['requireConfig'] = '';

                if (!isset($paths['sessionConfig']))
                    $paths['sessionConfig'] = '';

                if (!isset($paths['pathAliasConfig']))
                    $paths['pathAliasConfig'] = '';

                if (!isset($paths['namespacesConfig']))
                    $paths['namespacesConfig'] = '';

                if (!isset($paths['dbConfig']))
                    $paths['dbConfig'] = '';

                if (!isset($paths['defaultConfig']))
                    $paths['defaultConfig'] = '';

                function content ($paths){
                    $string = '<?php return [ ';
                    foreach ($paths as $key => $path){
                        $string .= sprintf('\'%s\' => \'%s\',',$key, $path);
                    }
                    return $string . '];';
                };
                file_put_contents($cachefile,content($paths));
            }
        }

        require_once $paths['milano'] . '/Milano.php';

        require_once $paths['milano'] . '/Bootstrap.php';

        require_once $paths['milano'] . '/BasePlugin.php';

        require_once $paths['milano'] . '/routingFunctions.php';

        if (is_file($paths['vendor'] . '/autoload.php'))
            require_once $paths['vendor'] . '/autoload.php';

        Milano::$App = new Milano($is_DEV, $paths);

        if ($is_DEV === true || !file_exists($paths['cache'] . '/milano/config-cache.php'))
            $this->createConfigFile($config);

        Milano::$App->config = require_once $paths['cache'] . '/milano/config-cache.php';


        if ($runSelf === true)
            (new Bootstrap())->load()->run();
    }

    /**
     * @param $config
     */
    private function createConfigFile($config){

        /**
         * gets the first schema of the config
         */
        if ($config === NULL){
            if (($configfile = (is_file(Milano::$App->_pathAlias['config']) && substr(Milano::$App->_pathAlias['config'], -4) === 'json') ? Milano::$App->_pathAlias['config']  : ((is_file(Milano::$App->_pathAlias['config'] . '.json')) ? Milano::$App->_pathAlias['config'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/config.json')) ? Milano::$App->_pathAlias['config'] . '/config.json' : false))) !== FALSE) {
                $config = json_decode(implode('', file($configfile)), true);
            }elseif (($configfile = (is_file(Milano::$App->_pathAlias['config']) && substr(Milano::$App->_pathAlias['config'], -3) === 'php') ? Milano::$App->_pathAlias['config']  : ((is_file(Milano::$App->_pathAlias['config'] . '.php')) ? Milano::$App->_pathAlias['config'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/config.php')) ? Milano::$App->_pathAlias['config'] . '/config.php' : false))) !== FALSE) {
                $config = require_once $configfile;
            }elseif (($configfile = (is_file(Milano::$App->_pathAlias['mainConfig']) && substr(Milano::$App->_pathAlias['mainConfig'], -4) === 'json') ? Milano::$App->_pathAlias['mainConfig']  : ((is_file(Milano::$App->_pathAlias['mainConfig'] . '.json')) ? Milano::$App->_pathAlias['mainConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/main.json')) ? Milano::$App->_pathAlias['config'] . '/main.json' : false))) !== FALSE) {
                $config = json_decode(implode('', file($configfile)), true);
            }elseif (($configfile = ((is_file(Milano::$App->_pathAlias['mainConfig']) && substr(Milano::$App->_pathAlias['mainConfig'], -4) === 'php')) ? Milano::$App->_pathAlias['mainConfig']  : ((is_file(Milano::$App->_pathAlias['mainConfig'] . '.php')) ? Milano::$App->_pathAlias['mainConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/main.php')) ? Milano::$App->_pathAlias['config'] . '/main.php' : false))) !== FALSE){
                $config = require_once $configfile;
            }else{
                $config = [];
            }
        }

        if (!isset($config['params'])){
            if ($configfile = (is_file(Milano::$App->_pathAlias['params']) && substr(Milano::$App->_pathAlias['params'], -4) === 'json') ? Milano::$App->_pathAlias['params']  : ((is_file(Milano::$App->_pathAlias['params'] . '.json')) ? Milano::$App->_pathAlias['params'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/params.json')) ? Milano::$App->_pathAlias['config'] . '/params.json' : false)) !== FALSE) {
                $config['params'] = json_decode(implode('', file($configfile)), true);
            }elseif ($configfile = (is_file(Milano::$App->_pathAlias['params']) && substr(Milano::$App->_pathAlias['params'], -4) === 'php') ? Milano::$App->_pathAlias['params']  : ((is_file(Milano::$App->_pathAlias['params'] . '.php')) ? Milano::$App->_pathAlias['params'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/params.php')) ? Milano::$App->_pathAlias['config'] . '/params.php' : false)) !== FALSE) {
                $config['params'] = require_once $configfile;
            }else{
                $config['params'] = [];
                }
            }

            if (!isset($config['params']['app'])){
                if ($configfile = (is_file(Milano::$App->_pathAlias['appConfig']) && substr(Milano::$App->_pathAlias['appConfig'], -4) === 'json') ? Milano::$App->_pathAlias['appConfig']  : ((is_file(Milano::$App->_pathAlias['appConfig'] . '.json')) ? Milano::$App->_pathAlias['appConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/app.json')) ? Milano::$App->_pathAlias['config'] . '/app.json' : false)) !== FALSE) {
                    $config['params']['app'] = json_decode(implode('', file($configfile)), true);
                }elseif ($configfile = (is_file(Milano::$App->_pathAlias['appConfig']) && substr(Milano::$App->_pathAlias['appConfig'], -4) === 'php') ? Milano::$App->_pathAlias['appConfig']  : ((is_file(Milano::$App->_pathAlias['appConfig'] . '.php')) ? Milano::$App->_pathAlias['appConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/app.php')) ? Milano::$App->_pathAlias['config'] . '/app.php' : false)) !== FALSE) {
                    $config['params']['app'] = require_once $configfile;
                }else{
                    $config['params']['app'] = [];
                }
            }

            if (!isset($config['params']['cookies'])){
                if ($configfile = (is_file(Milano::$App->_pathAlias['cookieConfig']) && substr(Milano::$App->_pathAlias['cookieConfig'], -4) === 'json') ? Milano::$App->_pathAlias['cookieConfig']  : ((is_file(Milano::$App->_pathAlias['cookieConfig'] . '.json')) ? Milano::$App->_pathAlias['cookieConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/cookies.json')) ? Milano::$App->_pathAlias['config'] . '/cookies.json' : false)) !== FALSE) {
                    $config['params']['cookies'] = json_decode(implode('', file($configfile)), true);
                }elseif ($configfile = (is_file(Milano::$App->_pathAlias['cookieConfig']) && substr(Milano::$App->_pathAlias['cookieConfig'], -4) === 'php') ? Milano::$App->_pathAlias['cookieConfig']  : ((is_file(Milano::$App->_pathAlias['cookieConfig'] . '.php')) ? Milano::$App->_pathAlias['cookieConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/cookies.php')) ? Milano::$App->_pathAlias['config'] . '/cookies.php' : false)) !== FALSE) {
                    $config['params']['cookies'] = require_once $configfile;
                }else{
                    $config['params']['cookies'] = [];
                }
            }

        if (!isset($config['params']['require'])){
            if ($configfile = (is_file(Milano::$App->_pathAlias['requireConfig']) && substr(Milano::$App->_pathAlias['requireConfig'], -4) === 'json') ? Milano::$App->_pathAlias['requireConfig']  : ((is_file(Milano::$App->_pathAlias['requireConfig'] . '.json')) ? Milano::$App->_pathAlias['requireConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/require.json')) ? Milano::$App->_pathAlias['config'] . '/require.json' : false)) !== FALSE) {
                $config['params']['require'] = json_decode(implode('', file($configfile)), true);
            }elseif ($configfile = (is_file(Milano::$App->_pathAlias['requireConfig']) && substr(Milano::$App->_pathAlias['requireConfig'], -4) === 'php') ? Milano::$App->_pathAlias['requireConfig']  : ((is_file(Milano::$App->_pathAlias['requireConfig'] . '.php')) ? Milano::$App->_pathAlias['requireConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/require.php')) ? Milano::$App->_pathAlias['config'] . '/require.php' : false)) !== FALSE) {
                $config['params']['require'] = require_once $configfile;
            }else{
                $config['params']['require'] = [];
            }
        }

            if (!isset($config['params']['session'])){
                if ($configfile = (is_file(Milano::$App->_pathAlias['sessionConfig']) && substr(Milano::$App->_pathAlias['sessionConfig'], -4) === 'json') ? Milano::$App->_pathAlias['sessionConfig']  : ((is_file(Milano::$App->_pathAlias['sessionConfig'] . '.json')) ? Milano::$App->_pathAlias['sessionConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/session.json')) ? Milano::$App->_pathAlias['config'] . '/session.json' : false)) !== FALSE) {
                    $config['params']['session'] = json_decode(implode('', file($configfile)), true);
                }elseif ($configfile = (is_file(Milano::$App->_pathAlias['sessionConfig']) && substr(Milano::$App->_pathAlias['sessionConfig'], -4) === 'php') ? Milano::$App->_pathAlias['sessionConfig']  : ((is_file(Milano::$App->_pathAlias['sessionConfig'] . '.php')) ? Milano::$App->_pathAlias['sessionConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/session.php')) ? Milano::$App->_pathAlias['config'] . '/session.php' : false)) !== FALSE) {
                    $config['params']['session'] = require_once $configfile;
                }else{
                    $config['params']['session'] = [];
                }
            }

            if (!isset($config['params']['pathAlias'])){
                if ($configfile = (is_file(Milano::$App->_pathAlias['pathAliasConfig']) && substr(Milano::$App->_pathAlias['pathAliasConfig'], -4) === 'json') ? Milano::$App->_pathAlias['pathAliasConfig']  : ((is_file(Milano::$App->_pathAlias['pathAliasConfig'] . '.json')) ? Milano::$App->_pathAlias['pathAliasConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/pathAlias.json')) ? Milano::$App->_pathAlias['config'] . '/pathAlias.json' : false)) !== FALSE) {
                    $config['params']['pathAlias'] = json_decode(implode('', file($configfile)), true);
                }elseif ($configfile = (is_file(Milano::$App->_pathAlias['pathAliasConfig']) && substr(Milano::$App->_pathAlias['pathAliasConfig'], -4) === 'php') ? Milano::$App->_pathAlias['pathAliasConfig']  : ((is_file(Milano::$App->_pathAlias['pathAliasConfig'] . '.php')) ? Milano::$App->_pathAlias['pathAliasConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/pathAlias.php')) ? Milano::$App->_pathAlias['config'] . '/pathAlias.php' : false)) !== FALSE) {
                    $config['params']['pathAlias'] = require_once $configfile;
                }else{
                    $config['params']['pathAlias'] = [];
                }
            }

            if (!isset($config['params']['namespaces'])){
                if ($configfile = (is_file(Milano::$App->_pathAlias['namespacesConfig']) && substr(Milano::$App->_pathAlias['namespacesConfig'], -4) === 'json') ? Milano::$App->_pathAlias['namespacesConfig']  : ((is_file(Milano::$App->_pathAlias['namespacesConfig'] . '.json')) ? Milano::$App->_pathAlias['namespacesConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/namespaces.json')) ? Milano::$App->_pathAlias['config'] . '/namespaces.json' : false)) !== FALSE) {
                    $config['params']['namespaces'] = json_decode(implode('', file($configfile)), true);
                }elseif ($configfile = (is_file(Milano::$App->_pathAlias['namespacesConfig']) && substr(Milano::$App->_pathAlias['namespacesConfig'], -4) === 'php') ? Milano::$App->_pathAlias['namespacesConfig']  : ((is_file(Milano::$App->_pathAlias['namespacesConfig'] . '.php')) ? Milano::$App->_pathAlias['namespacesConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/namespaces.php')) ? Milano::$App->_pathAlias['config'] . '/namespaces.php' : false)) !== FALSE) {
                    $config['params']['namespaces'] = require_once $configfile;
                }else{
                    $config['params']['namespaces'] = [];
                }
            }

        if (!isset($config['db'])){
            if ($configfile = (is_file(Milano::$App->_pathAlias['dbConfig']) && substr(Milano::$App->_pathAlias['dbConfig'], -4) === 'json') ? Milano::$App->_pathAlias['dbConfig']  : ((is_file(Milano::$App->_pathAlias['dbConfig'] . '.json')) ? Milano::$App->_pathAlias['dbConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/db.json')) ? Milano::$App->_pathAlias['config'] . '/db.json' : false)) !== FALSE) {
                $config['db'] = json_decode(implode('', file($configfile)), true);
            }elseif ($configfile = (is_file(Milano::$App->_pathAlias['dbConfig']) && substr(Milano::$App->_pathAlias['dbConfig'], -4) === 'php') ? Milano::$App->_pathAlias['dbConfig']  : ((is_file(Milano::$App->_pathAlias['dbConfig'] . '.php')) ? Milano::$App->_pathAlias['dbConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/db.php')) ? Milano::$App->_pathAlias['config'] . '/db.php' : false)) !== FALSE) {
                $config['db'] = require_once $configfile;
            }else{
                $config['db'] = [];
            }
        }

        if (!isset($config['plugins'])){
            if ($configfile = (is_file(Milano::$App->_pathAlias['pluginsConfig']) && substr(Milano::$App->_pathAlias['pluginsConfig'], -4) === 'json') ? Milano::$App->_pathAlias['pluginsConfig']  : ((is_file(Milano::$App->_pathAlias['pluginsConfig'] . '.json')) ? Milano::$App->_pathAlias['pluginsConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/plugins.json')) ? Milano::$App->_pathAlias['config'] . '/plugins.json' : false)) !== FALSE) {
                $config['plugins'] = json_decode(implode('', file($configfile)), true);
            }elseif ($configfile = (is_file(Milano::$App->_pathAlias['pluginsConfig']) && substr(Milano::$App->_pathAlias['pluginsConfig'], -4) === 'php') ? Milano::$App->_pathAlias['pluginsConfig']  : ((is_file(Milano::$App->_pathAlias['pluginsConfig'] . '.php')) ? Milano::$App->_pathAlias['pluginsConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/plugins.php')) ? Milano::$App->_pathAlias['config'] . '/plugins.php' : false)) !== FALSE) {
                $config['plugins'] = require_once $configfile;
            }else{
                $config['plugins'] = [];
            }
        }

        if (!isset($config['default'])){
            if ($configfile = (is_file(Milano::$App->_pathAlias['defaultConfig']) && substr(Milano::$App->_pathAlias['pluginsConfig'], -4) === 'json') ? Milano::$App->_pathAlias['defaultConfig']  : ((is_file(Milano::$App->_pathAlias['defaultConfig'] . '.json')) ? Milano::$App->_pathAlias['defaultConfig'] . '.json' : ((is_file(Milano::$App->_pathAlias['config'] . '/default.json')) ? Milano::$App->_pathAlias['config'] . '/default.json' : false)) !== FALSE) {
                $config['default'] = json_decode(implode('', file($configfile)), true);
            }elseif ($configfile = (is_file(Milano::$App->_pathAlias['defaultConfig']) && substr(Milano::$App->_pathAlias['pluginsConfig'], -4) === 'php') ? Milano::$App->_pathAlias['defaultConfig']  : ((is_file(Milano::$App->_pathAlias['defaultConfig'] . '.php')) ? Milano::$App->_pathAlias['defaultConfig'] . '.php' : ((is_file(Milano::$App->_pathAlias['config'] . '/default.php')) ? Milano::$App->_pathAlias['config'] . '/default.php' : false)) !== FALSE) {
                $config['default'] = require_once $configfile;
            }else{
                $config['default'] = [];
            }
        }




        //TODO all the default values for the config file

        if (!isset($config['params']['app']['name']))
            $config['params']['app']['name'] = 'milano';

        if (!isset($config['params']['app']['title']))
            $config['params']['app']['title'] = $config['params']['app']['name'];




        foreach ($config['plugins'] as $plugin => $data){
            $config[$plugin] = (isset($config[$plugin])) ? $config[$plugin] : (isset($data['config'])) ? $data['config'] : [];
            $pluginClass = ((isset($data['namespace'])) ? $data['namespace'] : 'milano\plugins') . '\\' . $plugin;
            if (method_exists($pluginClass, 'checkConfig'))
                $config[$plugin] = (new $pluginClass)->checkConfig($config[$plugin]);
        }

        unset($pluginClass, $data, $plugin);

           $fuldir = '';
           foreach (explode('/',Milano::$App->_pathAlias['cache'] . '/milano') as $dir){
               $fuldir .= $dir . '/';
               if (!is_dir($fuldir))
                   mkdir($fuldir);
           }

        $file = fopen(Milano::$App->_pathAlias['cache'] . '/milano/config-cache.php', 'w');

        fwrite($file, '<?php return [');

        fwrite($file, $this->getConfigFileContent($config));

        fwrite($file, '];');

        fclose($file);
    }

    /**
     * @param $config
     * @return string
     */
    public function getConfigFileContent($config){
        $string = '';
        foreach ($config as $key => $item){
            $string .= '\'' . $key . '\' => ';
            if (is_array($item)){
                $string .= '[' .  $this->getConfigFileContent($item) . '],';
            }else{
                $string .= '\'' . $item . '\',';
            }
        }
        return $string;
    }
}