<?php

/**
 * Router with authorization
 *
 * @author arnold
 */
class Router extends Jasny\Router
{
    /**
     * Execute the action.
     * 
     * @return mixed  Whatever the controller returns
     */
    public function execute()
    {
        if (!Auth::routeAllowed($this->getRoute())) return $this->routeTo(403);
        return parent::execute();
    }
}
