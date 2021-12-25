<?php

if(!defined("MvcFramework")){ die("Access Denied!"); }

class AdminController extends \Mvc\Controller
{
    public function __construct()
    {
        $args = func_get_args();
        call_user_func_array(array($this, 'parent::__construct'), $args);

        if($this->Identity == null)
        {
            die("IdentityFramework not found");
        }
        else if($this->Identity->CurrentUser == null)
        {
            $this->RedirectAction("Login", "Index");
        }
        else if(!$this->Identity->CurrentUser->IsInRole("administrator"))
        {
            $this->RedirectAction("User", "Index");
        }
    }

	public function Index()
	{
        return new \Mvc\View($this);
    }
    
    
}

?>