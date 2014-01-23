<?php

use Jasny\Config;
use Monolog\Handler\FirePHPHandler, Monolog\Handler\ChromePHPHandler;

/**
 * Application
 */
class App
{
    /** @var Config */
    protected static $config;
    
    /** @var Router */
    protected static $router;
    
    /** @var Monolog\Logger */
    protected static $logger;
    
    
    /**
     * Get application environment
     * 
     * @param string $check  Only return if env matches
     * @return string|false
     */
    public static function env($check=null)
    {
        $env = getenv('APPLICATION_ENV') ?: 'prod';
        return !isset($check) || preg_replace('/\..*/', '', $env) ? $env : false;
    }
    
    /**
     * Get application settings
     * 
     * @return Config
     */
    public static function config()
    {
        if (!isset(self::$config)) {
            $path = BASE_PATH . '/config';
            
            self::$config = new Config("$path/settings.yml");
            if (file_exists("$path/settings." . self::env() . '.yml'))
                self::$config->load("$path/settings." . self::env() . '.yml');

            if (self::$config->locale) self::setLocale(self::$config->locale);
        }
        
        return self::$config;
    }
    
    /**
     * Set application locale
     * 
     * @return Config $this
     */
    public static function setLocale($locale)
    {
        $locale_charset = setlocale(LC_ALL, $locale, "$locale.UTF-8", "$locale.ISO-8859-1");
        Locale::setDefault($locale_charset);
        
        putenv("LANG=$locale");
        define('LANG', substr($locale, 0, 2));
    }

    
    /**
     * Get the application router
     * 
     * @return Router
     */
    public static function router()
    {
        if (!isset(self::$router)) {
            self::$router = new Router(new Config(BASE_PATH . '/config/routes.yml'));
            if (defined('BASE_DIR')) self::$router->setBase(BASE_DIR);
        }
        
        return self::$router;
    }
    
    
    /**
     * Send a message to the browsers console.
     * Works with FireFox (using FirePHP) and Chrome (using Chrome Console)
     * 
     * @param string|mixed $message
     */
    public static function debug($message)
    {
        if (!self::config()->debug) return;
        
        if (!isset(self::$logger))
            self::$logger = new Monolog\Logger('', [new FirePHPHandler(), new ChromePHPHandler()]);
        
        if (!is_scalar($message)) $message = json_encode($message, JSON_PRETTY_PRINT);
        self::$logger->debug($message);
    }
    
    /**
     * Get full URL
     * 
     * @param sting $subdomain
     * @return string
     */
    public static function url($subdomain)
    {
        return (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $subdomain . '.' . DOMAIN;
    }

    
    /**
     * Get value of tracking cookie.
     * Set the tracking cookie if is doesn't exist.
     * 
     * @return string
     */
    public static function tracking()
    {
        if (!isset($_COOKIE['cltr'])) {
            $_COOKIE['cltr'] = uniqid();
            
            // Don't set cookie if user has "Do Not Track" enabled.
            if (!isset($_SERVER['DNT']) || $_SERVER['DNT'] == 0 || App::env('dev'))
                setcookie('cltr', $_COOKIE['cltr'], strtotime('now + 1 year'), '/', DOMAIN);
        }
        
        return $_COOKIE['cltr'];
    }
}
