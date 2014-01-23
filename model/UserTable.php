<?php

/**
 * Table gateway for `user` table
 */
class UserTable extends DB\UserTable
{
    /**
     * Generate a confirmation hash
     * 
     * @param int $id
     * @return string
     */
    public static function generateConfirmationHash($id)
    {
        return sprintf('%010s', substr(base_convert(md5($id . App::config()->secret->signup), 16, 36), -10) .
            base_convert($id, 10, 36));
    }
    
    /**
     * Fetch a Signup record
     * 
     * @parma int|string|array $id  Id, filter or confirmation hash
     * @return Signup
     */
    public function fetchForConfirmation($hash)
    {
        $id = base_convert(substr($hash, 10), 36, 10);
        if (self::generateConfirmationHash($id) != $hash) return null; // invalid hash
        
        return parent::fetch($id);
    }
    
    /**
     * Generate a hash to reset the password
     * 
     * @param string $id
     * @param string $password
     * @return string
     */
    public static function generatePasswordResetHash($id, $password) {
        return sprintf('%010s', substr(base_convert(md5($id . App::config()->secret->signup . $password), 16, 36), -10)
            . base_convert($id, 10, 36));
    }
    
    /**
     * Fetch a user for a password reset
     * 
     * @param string $hash
     * @return User
     */
    public function fetchForPasswordReset($hash)
    {
        $id = base_convert(substr($hash, 10), 36, 10);
        
        $user = $this->fetch($id);
        if (!$user || self::generatePasswordResetHash($id, $user->password) != $hash) return null; // invalid hash
        
        return $user;
    }
}
