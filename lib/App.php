<?php

use Jasny\Config;
use Jasny\MVC\Request;

/**
 * The class that bind everything together into an application.
 * Don't be afraid to customize this class to satisfy your needs.
 */
class App
{
    /** @var Config */
    protected static $config;
    
    /** @var Router */
    protected static $router;
    
    /** @var Logger */
    protected static $logger;
    
    
    /**
     * Get application name
     * 
     * @return string
     */
    public function name()
    {
        if (isset(self::config()->app->name)) return self::config()->app->name;
        return isset($_SERVER['HTTP_HOST']) ? self::domain(false) : null;
    }
    
    /**
     * Get application version
     * 
     * @return string
     */
    public function version()
    {
        return isset(self::config()->app->version) ? self::config()->app->version : null;
    }
    
    
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
            self::$config = new Config();
            $files = ['settings.yml', 'settings.' . self::env() . '.yml', 'setting.local.yml'];

            foreach ($files as $file) {
                $path = "config/$file";
                if (file_exists($path)) self::$config->load($path);
            }
        }
        
        return self::$config;
    }
    
    
    /**
     * Set application locale.
     * @link http://php.net/setlocale
     * 
     * @param string $locale  Defaults to 'locale' setting from config.
     */
    public static function setLocale($locale=null)
    {
        if (!isset($locale)) {
            if (!isset(self::config()->locale)) return;
            $locale = self::config()->locale;
        }
        
        $localeCharset = setlocale(LC_ALL, "$locale.UTF-8", $locale, "$locale.ISO-8859-1");
        Locale::setDefault($localeCharset);
        
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
            self::$router = new Router(new Config('config/routes.yml'));
            self::$router->setBase(static::getBasePath());
        }
        
        return self::$router;
    }
    
    
    /**
     * Get the logger
     * 
     * @return Logger
     */
    public static function logger()
    {
        if (!isset(self::$logger)) {
            self::$logger = Logger\Factory::createLogger();
        }
        
        return self::$logger;
    }

    /**
     * Enable error handling
     */
    public static function handleErrors()
    {
        if (Request::getOutputFormat() != 'html') ini_set('html_erors', false);
        
        if (isset(self::config()->log)) {
            $log = self::config()->log;
            
            if (!is_bool($log)) Monolog\ErrorHandler::register(App::logger());
            ini_set('display_errors', $log === false);
            ini_set('log_errors', $log === true);
        }
        
        App::router()->handleErrors();
    }
    
    
    /**
     * Get current domain
     * 
     * @param sting $subdomain  Alternative subdomain / module
     * @return string
     */
    public static function domain($subdomain=null)
    {
        $domain = $_SERVER['HTTP_HOST'];
        
        if (isset($subdomain)) {
            $regex = preg_quote(defined('MODULE') ? MODULE : 'www', '/');
            if (isset($_SERVER['DOCUMENT_ROOT'])) $regex .= '|' . preg_quote(basename($_SERVER['DOCUMENT_ROOT']), '/');
            
            $domain = ($subdomain ? $subdomain . '.' : '') . preg_replace('/^' . $regex . '\./', '', $domain);
        }
            
        return $domain;
    }

    /**
     * Get full url
     * 
     * @param sting  Alternative path
     * @return string
     */
    public static function url($path=null)
    {
        $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $domain = $_SERVER['HTTP_HOST'];

        $curpath = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
        if (!isset($path)) {
            $path = $curpath;
        } elseif ($path[0] !== '/') {
            $path = dirname($curpath) . '/' . $path;
        } else {
            $path = static::getBasePath() . $path;
        }
        
        return $protocol . $domain . $path;
    }
    
    /**
     * Get the base URL path
     * 
     * @return string 
     */
    protected static function getBasePath()
    {
        if (!isset($_SERVER['DOCUMENT_ROOT'])) return;
        
        $docroot = $_SERVER['DOCUMENT_ROOT'];
        
        return strpos(getcwd(), $docroot) === 0 && strlen(getcwd()) !== strlen($docroot) ?
            '/' . trim(substr(getcwd(), strlen($docroot)), '/') :
            null;
    }
}
