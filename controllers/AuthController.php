<?php

/**
 * Authentication contoller
 */
class AuthController extends Controller
{
    /**
     * Login
     */
    public function loginAction()
    {
        if (!Auth::login($_POST['email'], $_POST['password'])) 
            $this->setFlash('error', "Incorrect e-mail address or password");
        
        return $this->redirect($this->localReferer() ?: '/');
    }
    
    /**
     * Login with social API (Facebook, Twitter, etc)
     */
    public function loginWithAction($service)
    {
        if ($this->localReferer()) $_SESSION['login_with_referer'] = $this->localReferer();
        
        if (!Auth::loginWith($service))
            $this->setFlash('error', "Didn't log in with $service");

        $redirect = isset($_SESSION['login_with_referer']) ? $_SESSION['login_with_referer'] : '/';
        unset($_SESSION['login_with_referer']);

        return $this->redirect($redirect);
    }
    
    /**
     * Logout
     */
    public function logoutAction()
    {
        Auth::logout();
        return $this->redirect($this->localReferer() ?: '/');
    }

    /**
     * Sign up
     * @todo Use Form object
     */
    public function signupAction()
    {
        if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['password'])) {
            $error = "All fields are required";
        } elseif (strlen($_POST['password']) < 4) {
            $error = "Password should be at least 4 characters long";
        } elseif (($user = User::fetch(['email'=>$_POST['email']])) && $user->isUser()) {
            $error = "You already have an account. Did you forget your password?";
        }
        
        if (isset($error)){
            $this->setFlash('error', $error);
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        
        $values = $_POST;
        $values['password'] = Auth::password($values['password']);

        $user = new User();
        $user->setValues($values)->save();

        Email::load('signup')->render(['user'=>$user])->send($user->email, $user->getFullName());

        $this->setFlash('success', "We've send you an e-mail to complete the sign up");
        return $this->redirect($this->localReferer() ?: '/');
    }
    
    /**
     * Confirm signup, clicking on the link in the welcome email.
     */
    public function confirmAction($hash)
    {
        $user = User::fetchForConfirmation($hash) ?: new User();
        if (!$user) $this->notFound("Invalid confirmation hash");
        
        if ($user->status != 'new') {
            $this->setFlash('error', 'Your account has already been activated');
            return $this->redirect('/');
        }
        
        $user->setValues(['status'=>'active'])->save();
        Auth::setUser($user);
        
        return $this->redirect('/');
    }
    
    /**
     * Ask a user to log in
     */
    public function loginRequiredAction()
    {
        $this->redirect(($this->localReferer() ?: '/') . '#require-login');
    }
    
    /**
     * Send e-mail to user to restore password
     */    
    public function forgotPasswordAction() 
    {
        $user = User::fetch(['email'=>$_POST['email']]);
        if (!$user) $this->notFound("There is no user with this e-mail");
        
        Email::load('reset-password')->render(['user' => $user])->send($user->email, $user->getFullName());
        
        $this->setFlash('success', "An e-mail with link for reseting password is on it's way");
        $this->redirect($this->localReferer() ?: '/');
    }
    
    /**
     * Set new password
     *
     * @param string $hash
     */    
    public function resetPasswordAction($hash) 
    {
        $user = User::fetchForPasswordReset($hash);
        if (!$user) $this->notFound("This link is no longer valid");

        if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['password']) {
            $password = Auth::password($_POST['password']);
            $user->setValues(['password' => $password])->save();
            
            Auth::setUser($user);
            $this->setFlash('success', "Password has been reset successfully.");
            $this->redirect($user);
        }
        
        $this->view('reset-password', compact('user', 'hash'));
    }
    
    /**
     * Change password
     */    
    public function changePasswordAction() 
    {
        $user = Auth::user();
        if($user->password != Auth::password($_POST['old-password'], $user->password)) {
            $this->setFlash('error', 'Current password is incorrect');
            $this->redirect($this->localReferer() ?: '/');
        }

        $user->setValues(['password' => Auth::password($_POST['password'])])->save();
        $this->setFlash('success', 'Password was changed successfully');            
        $this->redirect($this->localReferer() ?: '/');
    }
}
