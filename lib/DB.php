<?php

use Monolog\Handler\FirePHPHandler, Monolog\Handler\ChromePHPHandler;

/**
 * Database class
 */
class DB extends Jasny\DB\MySQL\Connection
{
    /**
     * Get the default or named DB connection.
     * 
     * @param string $name
     * @return Connection
     */
    public static function conn($name = 'default')
    {
        if ($name == 'default' && !isset(self::$connections['default'])) {
            $db = new DB(@App::config()->db);
            $db->set_charset('utf8_general');
            
            if (!empty(App::config()->db->debug)) $db->enableDebugging();
        }
        
        return parent::conn($name);
    }
    
    /**
     * Send queries to Firefox (firebug) and Chrome
     */
    public function enableDebugging()
    {
        $this->setLogger(new Monolog\Logger('DB:', [new FirePHPHandler(), new ChromePHPHandler()]));
    }

    /**
     * Enable model generator (with caching)
     * 
     * The model generator automatically generates Record and Table classes for tables. These classes are generated when
     *  used in the code using an autoloader. They use the DB namespace (if needed) and are stored in directory
     *  'cache/modal'. They are automatically replaced if the table definition is modified on the development env. On
     *  production you need to manually delete the cached files. 
     */
    public static function enableModelGenerator()
    {
        self::conn(); // Connect to DB, setting the default connection
        
        if (App::env() == 'prod') set_include_path(get_include_path() . PATH_SEPARATOR . BASE_PATH . '/cache/model');
        Jasny\DB\ModelGenerator::enable(BASE_PATH . '/cache/model');
    }
}
