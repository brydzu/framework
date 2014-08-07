<?php

/**
 * Router with authorization
 *
 * @author arnold
 */
class Router extends Jasny\MVC\Router
{
    /**
     * Execute the action.
     * 
     * @return mixed  Whatever the controller returns
     */
    public function execute()
    {
        $route = $this->getRoute();
        if (App::config()->debug) App::logger()->debug("Route", compact('route'));
        
        if (!Auth::routeAllowed($route)) return $this->routeTo(403);
        return parent::execute();
    }
}
