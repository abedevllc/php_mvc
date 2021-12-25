<?php

if(!defined("MvcFramework")){ die("Access Denied!"); }

class HomeController extends \Mvc\Controller
{
	public function Index()
	{
		return new \Mvc\View($this);
	}
}

?>