<?php

/**
 * Webservice API server
 */
class API
{
    /**
     * Check if this is a JSONP request
     * 
     * @return boolean
     */
    public static function isJSONP()
    {
        return isset($_GET['callback']);
    }
    
    /**
     * Output data as JSON
     * 
     * @param mixed $data
     */
    public static function output($data)
    {
        if (static::isJSONP()) {
            header("Content-Type: application/javascript");
            echo $_GET['callback'] . '(' . json_encode($data) . ')';
        } else {
            header("Content-Type: application/json");
            echo json_encode($data);
        }
    }
    
    /**
     * Exit on user error.
     * 
     * @param int    $status
     * @param string $error
     * @param in
     */
    public static function error($status, $error)
    {
        if (!static::isJSONP()) {
            switch ($status) {
                case 400: header("HTTP/1.1 400 Bad Request"); break;
                case 402: header("HTTP/1.1 402 Payment Required"); break;
                case 404: header("HTTP/1.1 404 Not Found"); break;
                case 409: header("HTTP/1.1 409 Conflict"); break;
                case 500: header("HTTP/1.1 500 Internal server error"); break;
                case 503: header("HTTP/1.1 503 Service unavailable"); break;
            }
        }
        
        header("Content-Type: text/plain");
        echo $error;
    }
    
    
    /**
     * Enable the error handler
     */
    public static function handleErrors()
    {
        set_error_handler([get_called_class(), '_errorHandler']);
        set_exception_handler([get_called_class(), '_exceptionHandler']);
    }
    
    /**
     * Error handler
     * @ignore
     * 
     * @param int $errno
     * @return false
     */
    public static function _errorHandler($errno)
    {
        if (!(error_reporting() & $errno)) return;
        
        static::error(500, 'unexpected_error', "Oops, something went wrong on our side. Please try again.");
        return false;
    }
    
    /**
     * Exception handler
     * @ignore
     * 
     * @param Exception $e
     */
    public static function _exceptionHandler($e)
    {
        trigger_error("Uncaught exception '" . get_class($e) . "' with message '" . $e->getMessage() . "' on in " . $e->getFile() . ":" . $e->getLine(), E_USER_ERROR);
    }
}
