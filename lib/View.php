<?php

/**
 * View using Twig
 */
class View extends Jasny\View_Twig
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
        
        if (!isset($path)) $path = BASE_PATH . '/views';
        if (!isset($cache)) $cache = App::env() == 'prod' ? BASE_PATH . '/cache/view' : false;
                
        $twig = parent::init($path, $cache);
        $twig->addGlobal('auth', new Auth());
        
        return $twig;
    }
}
