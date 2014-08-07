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
        if (!Auth::login($_POST['email'], $_POST['password'])) {
            $this->flash->set('error', "Incorrect e-mail address or password");
        }
        
        return $this->back();
    }
    
    /**
     * Login with social API (Facebook, Twitter, etc)
     */
    public function loginWithAction($service)
    {
        if ($this->request->getLocalReferer()) {
            $_SESSION['login_with_referer'] = $this->request->getLocalReferer();
        }
        
        if (!Auth::loginWith($service)) {
            $this->flash->set('error', "Didn't log in with $service");
        }
        
        $redirect = isset($_SESSION['login_with_referer']) ? $_SESSION['login_with_referer'] : '/';
        unset($_SESSION['login_with_referer']);

        $this->redirect($redirect);
    }
    
    /**
     * Logout
     */
    public function logoutAction()
    {
        Auth::logout();
        $this->back();
    }

    /**
     * Sign up
     */
    public function signupAction()
    {
        if (User::fetch(['email'=>$_POST['email']])) {
            $this->flash->set('error', "You already have an account. Did you forget your password?");
            $this->back();
        }
        
        $user = new User();
        $values = ['password' => Auth::password($_POST['password'])] + $_POST;
        $user->setValues($values)->save();

        Email::load('signup')->render(['user'=>$user])->send($user->email, $user->getFullName());

        $this->flash->set('success', "We've send you an e-mail to complete the sign up");
        $this->back();
    }
    
    /**
     * Confirm signup, clicking on the link in the welcome email.
     */
    public function confirmAction($hash)
    {
        $user = User::fetchForConfirmation($hash) ?: new User();
        if (!$user) $this->notFound("Invalid confirmation hash");
        
        if ($user->status != 'new') {
            $this->flash->set('error', 'Your account has already been activated');
            return $this->redirect('/');
        }
        
        $user->activate();
        Auth::setUser($user);
        
        return $this->redirect('/');
    }
    
    /**
     * Ask a user to log in
     */
    public function loginRequiredAction()
    {
        $this->view('auth/login');
    }
    
    /**
     * Send e-mail to user to restore password
     */    
    public function forgotPasswordAction() 
    {
        $user = User::fetch(['email'=>$_POST['email']]);
        if (!$user) $this->notFound("There is no user with this e-mail");
        
        Email::load('reset-password')->render(['user' => $user])->send($user->email, $user->getFullName());
        
        $this->flash->set('success', "An e-mail with link for reseting password is on it's way");
        $this->back();
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
            $this->flash->set('success', "Password has been reset successfully.");
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
            $this->flash->set('error', 'Current password is incorrect');
            $this->redirect($this->localReferer() ?: '/');
        }

        $user->setValues(['password' => Auth::password($_POST['password'])])->save();
        $this->flash->set('success', 'Password was changed successfully');            
        $this->back();
    }
}
