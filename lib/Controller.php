<?php

/**
 * Base class for controllers.
 */
abstract class Controller extends Jasny\MVC\Controller
{ 
    /**
     * Show a view.
     * 
     * @param string $name     Filename of Twig template
     * @param array  $context  Data
     */
    protected function view($name=null, $context=[])
    {
        View::getEnvironment(); // Init Twig view
        return parent::view($name, $context);
    }
}
