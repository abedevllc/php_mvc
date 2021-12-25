<?php

namespace Mvc;

if(!defined("MvcFramework")){ die("Access Denied!"); }

if(!defined("DS")){ define("DS", DIRECTORY_SEPARATOR); }

class Mvc
{
	private $DefaultControllerName;
	private $DefaultActionName;
	private $Identity;
	private $Entity;
	private $Language;

	public function __construct($DefaultControllerName, $DefaultActionName, $Identity = null, $Entity = null, $Language = null)
	{
		$this->DefaultControllerName = $DefaultControllerName;
		$this->DefaultActionName = $DefaultActionName;
		$this->Identity = $Identity;
		$this->Entity = $Entity;
		$this->Language = $Language;

		$this->Init();
	}
	
	private function Init()
	{
		$this->InitPathes();
		$this->InitIncludes();
		
		$CurrentControllerName = $this->GetCurrentController();
		$CurrentActionName = $this->GetCurrentAction();
		$this->Redirect($CurrentControllerName, $CurrentActionName);
	}
	
	private function InitPathes()
	{
		define("MVC_ROOT_PATH", dirname(__FILE__));
		define("MVC_CORE_PATH", MVC_ROOT_PATH . DS . 'core');
		define("MVC_CORE_BASE_PATH", MVC_CORE_PATH . DS . 'base');
		define("MVC_CONTROLLER_PATH", MVC_ROOT_PATH . DS . 'controller');
		define("MVC_VIEW_PATH", MVC_ROOT_PATH . DS . 'view');
		define("MVC_MODEL_PATH", MVC_ROOT_PATH . DS . 'model');
	}
	
	public function Redirect($ControllerName = null, $Action = null)
	{
		$Controller = null;
		
		if($ControllerName == null || empty($ControllerName))
		{
			$ControllerName = $this->DefaultControllerName;
		}
		
		if($ControllerName != null && !empty($ControllerName))
		{
			$Controller = $this->GetController($ControllerName);
		}
		
		if($Controller != null)
		{			
			if($Action == null || empty($Action))
			{
				$Action = $this->DefaultActionName;
			}
	
			if($Action != null && !empty($Action))
			{
				$Controller->LoadIncludes($Action);
				
				if(method_exists($Controller, $Action))
				{
					$Controller->$Action();
				}
				else
				{
					die("No action found!");
				}
			}
		}
		else
		{
			die("No controller found!");
		}
	}
	
	private function GetController($ControllerName)
	{
		$ControllerName = str_replace("controller", "", strtolower($ControllerName));
		
		if(file_exists(MVC_CONTROLLER_PATH . DS . $ControllerName . '.php'))
		{
			require_once(MVC_CONTROLLER_PATH . DS . $ControllerName . '.php');
			$controllerClass = ucfirst($ControllerName). 'Controller';
			return new $controllerClass($this->Identity, $this->Entity, $this->Language, ucfirst($ControllerName). 'Controller', ucfirst($ControllerName));
		}
		else
		{
			return null;
		}
	}
	
	private function GetCurrentController()
	{
		if(isset($_GET['controller']) && $_GET['controller'] != null && !empty($_GET['controller']))
		{
			return $_GET['controller'];
		}
		else
		{
			return null;
		}
	}
	
	private function GetCurrentAction()
	{
		if(isset($_GET['action']) && $_GET['action'] != null && !empty($_GET['action']))
		{
			return $_GET['action'];
		}
		else
		{
			return null;
		}
	}
	
	private function InitIncludes()
	{
		require_once(MVC_CORE_BASE_PATH . DS . 'controller.php');
		require_once(MVC_CORE_BASE_PATH . DS . 'model.php');
		require_once(MVC_CORE_BASE_PATH . DS . 'view.php');		
	}
}

?>