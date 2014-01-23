<?php

/**
 * Description of ErrorController
 *
 * @author arnold
 */
class ErrorController extends Controller
{
    /**
     * 404 not found
     * 
     * @param string $code     HTTP response code
     * @param string $message
     */
    public function notFoundAction($code=404, $message=null)
    {
        $this->view('404', compact('code', 'message'));
    }
    
    /**
     * 500 Internal Server Error
     * 
     * @param string $code     HTTP response code
     * @param string $message
     */
    public function errorAction($code=500, $message=null)
    {
        $this->view('500', compact('code', 'message'));
    }
}
