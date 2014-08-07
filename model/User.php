<?php

/**
 * Record for `user` table
 */
class User extends DB\User implements Jasny\Auth\User
{
    use AttachImages;
    
    /**
     * Get username
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Get hased password
     * 
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get authentication level
     */
    public function getAuthLevel()
    {
        return $this->auth_level;
    }

    /**
     * Get the full name of the person.
     * 
     * @return string
     */
    public function getFullName()
    {
        return join(' ', [$this->first_name, $this->last_name]);
    }
    
    /**
     * Cast record to string
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getFullname();
    }
    
    
    /**
     * Activate the user
     */
    public function activate()
    {
        
    }
    
    /**
     * Event triggered on login
     * 
     * @return boolean
     */
    public function onLogin()
    {
        if (!$this->status === 'active') return;
        
        $this->_getDBTable()->save(['last_login' => new DateTime()]);
        return true;
    }

    /**
     * Add a social network id to this user
     * 
     * @param Social\Connection $conn
     * @param object            $me     User profile
     * @return Person $this
     */
    public function addSocialNetwork(Social\Connection $conn, $me=null)
    {
        if (!isset($me)) $me = $conn->me();
        $this->{$conn::serviceProvider . '_id'} = $me->id;

        // We save this to cope with Twitter's strict rate limits
        if ($conn instanceof Social\Twitter\Connection) {
            $this->twitter_access_token = $conn->getAccessToken();
            $this->twitter_access_secret = $conn->getAccessSecret();
        }
        
        // Add (missing) profile information
        if ($me instanceof \Social\Person) {
            if (!isset($this->first_name)) $this->first_name = $me->getFirstName();
            if (!isset($this->last_name)) $this->last_name = $me->getLastName();
            if (!isset($this->gender)) $this->gender = $me->getGender();
        } else {
            list($this->first_name, $this->last_name) = explode(' ', $me->getName(), 2) + [null, null];
        }
        
        if (!isset($this->email)) $this->email = $me->getEmail();
        
        if ($me->getPicture('800x800')) $this->addImages([$me->getPicture('800x800')], true);
        
        return $this;
    }

    
    /**
     * Get confirmation hash
     * 
     * @return string
     */
    public function getConfirmationHash()
    {
        return Auth::generateConfirmationHash($this);
    }
    
    /**
     * Generate hash for password reset
     * 
     * @return string
     */
    public function getPasswordResetHash() 
    {
        return Auth::generatePasswordResetHash($this);
    }
}
