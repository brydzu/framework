<?php

/**
 * View using Twig
 */
class View extends Jasny\MVC\View\Twig
{
    /**
     * Init Twig environment
     * 
     * @param string $path   Path to the templates 
     * @param string $cache  The cache directory or false if cache is disabled.
     * @return \Twig_Environment
     */
    public static function init($path=null, $cache=null)
    {
        static::$map['twig'] = __CLASS__;
        
        if (!isset($path)) $path = 'views';
        if (!isset($cache)) $cache = !empty(App::config()->cache) ? 'cache/view' : false;
                
        $twig = parent::init($path, $cache);
        
        $twig->addGlobal('app', new App());
        $twig->addGlobal('auth', new Auth());
        
        return $twig;
    }
}
