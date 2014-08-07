<?php

/**
 * Authenticate and authorize
 */
class Auth extends Jasny\Auth
{
    use Auth\Social;
    
    /**
     * Fetch a user by ID
     * 
     * @param int $id
     * @return User
     */
    public static function fetchUserById($id)
    {
        return User::fetch($id);
    }
    
    /**
     * Fetch a user by username
     * 
     * @param string $username
     * @return User
     */
    public static function fetchUserByUsername($username)
    {
        return User::fetch(['email'=>$username]);
    }
    

    /**
     * Check if user is allowed to perform action.
     * 
     * @param object $route
     * @return boolean
     */
    public static function routeAllowed($route)
    {
        return empty($route->auth) || (self::user()->auth_level >= self::getLevel($route->auth));
    }
}
