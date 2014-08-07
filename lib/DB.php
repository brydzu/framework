<?php

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
            $settings = isset(App::config()->db) ? App::config()->db : null;
            
            $db = new DB($settings);
            $db->set_charset('utf8_general');
            
            if (!empty(App::config()->db->debug)) $this->setLogger(App::logger());
        }
        
        return parent::conn($name);
    }
    
    /**
     * Enable model generator (with caching)
     * 
     * The model generator automatically generates Record and Table classes for tables. These classes are generated when
     *  used in the code using an autoloader. They use the DB namespace (if needed) and are stored in directory
     *  'cache/modal'. They are automatically replaced if the table definition is modified. If the 'cache' setting is 
     *  set to false (on production env) you need to manually delete the cached files. 
     */
    public static function enableModelGenerator()
    {
        self::conn(); // Connect to DB, setting the default connection
        
        if (App::config()->cache) set_include_path(get_include_path() . PATH_SEPARATOR . 'cache/model');
        Jasny\DB\ModelGenerator::enable('cache/model');
    }
}
