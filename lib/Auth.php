<?php

/**
 * Authenticate and authorize
 */
class Auth
{
    /**
     * Authorization levels
     * @var array
     */
    protected static $levels = [
        1 => 'user',
        100 => 'admin'
    ];

    /**
     * Current user
     * @var User
     */
    protected static $user;
    
    
    /**
     * Get all auth levels
     *  
     * @return array
     */
    public static function getLevels()
    {
        return static::$levels;
    }
    
    /**
     * Get auth level
     * 
     * @param string $type
     * @return int
     */
    public static function getLevel($type)
    {
        $level = array_search($type, static::$levels);
        if ($level === false) throw new Exception("Authorization level '$type' isn't defined.");
        
        return $level;
    }
    
    
    /**
     * Generate a password
     * 
     * @param string $password
     * @param string $salt      Use specific salt to verify existing password
     */
    public static function password($password, $salt=null)
    {
        return isset($salt) ? crypt($password, $salt) : password_hash($password, PASSWORD_BCRYPT);
    }
    
    /**
     * Login with username and password
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public static function login($username, $password)
    {
        $user = User::fetch(['email'=>$username]);
        if (!isset($user) || $user->password !== self::password($password, $user->password)) return false;
        
        return self::setUser($user);
    }
    
    
    /**
     * Get a social API connection
     * 
     * @param string $service
     * @return \Social\Connection
     */
    protected static function getSocialConnection($service)
    {
        $cfg = App::config()->social;
        
        switch ($service) {
            case 'linkedin': return new Social\LinkedIn\Connection($cfg->linkedin->client_id, $cfg->linkedin->client_secret, $_SESSION);
            case 'google':   return new Social\Google\Connection($cfg->google->api_key, $cfg->google->client_id, $cfg->google->client_secret, $_SESSION);
            case 'facebook': return new Social\Facebook\Connection($cfg->facebook->client_id, $cfg->facebook->client_secret, $_SESSION);
            case 'twitter':  return new Social\Twitter\Connection($cfg->twitter->consumer_key, $cfg->twitter->consumer_secret, $_SESSION);
        }
        
        throw new Exception("Unknown service '$service'");
    }


    /**
     * Login using a social network
     * 
     * @param Social\Connection|string $conn  A Social connection supporting Auth
     * @return boolean
     */
    public static function loginWith($conn)
    {
        if (is_string($conn)) $conn = self::getSocialConnection($conn);
        
        try {
            $service = $conn::serviceProvider;
            $conn->auth(@App::config()->social->$service->scope);
        } catch (Social\AuthException $e) {
            return false;
        }

        // User is already registered and social network is already known
        $user = User::fetch([$conn::serviceProvider . '_id'=>$conn->me()->id]);

        // User is already registered by using a new social network
        if (!isset($user) && $conn->me()->getEmail())
            $user = User::fetch(['email'=>$conn->me()->getEmail()]);

        // This is a new user
        if (!isset($user)) {
            $user = new User();
            $user->status = 'active';
        }
        
        // Add info from the social network and save
        $user->addSocialNetwork($conn)->save();
        return self::setUser($user);
    }

    /**
     * Logout
     */
    public static function logout()
    {
       self::$user = null;
       unset($_SESSION['auth_user_id']);
    }
    
    
    /**
     * Set the current user
     * 
     * @param User $user
     * @return boolean
     */
    public static function setUser(User $user)
    {
        if ($user->status !== 'active') return false;
        
        self::$user = $user;
        $_SESSION['auth_user_id'] = $user->getId();

        $user->last_login = new DateTime();
        $user->save();

        return true;
    }
    
    /**
     * Get current authenticated user
     * 
     * @return User
     */
    public static function user()
    {
        if (!isset(self::$user) && isset($_SESSION['auth_user_id'])) {
            self::$user = User::fetch($_SESSION['auth_user_id']);
        }
        
        return self::$user;
    }
    
    
    /**
     * Check if user is allowed to perform action.
     * 
     * @param object $route
     * @return boolean
     */
    public static function routeAllowed($route)
    {
        return empty($route->auth) || self::user()->auth_level >= self::getLevel($route->auth);
    }
}
