<?php

class DefaultController extends Controller
{
    /**
     * Home page
     */
    public function indexAction()
    {
        $this->view('index');
    }
    
    /**
     * Show static page
     * 
     * @param string $page
     */
    public function pageAction($page)
    {
        $this->view("pages/" . basename($page));
    }
}
