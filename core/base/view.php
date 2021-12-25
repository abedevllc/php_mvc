<?php

namespace Mvc;

if(!defined("MvcFramework")){ die("Access Denied!"); }

class View
{
	private $ActionName;
	private $ViewName;
	private $ViewFileName;
	private $Model;
	private $ViewData;
	private $Controller; 
	private $Content;
	
	public function __construct($Controller, $Model = null, $ViewData = null, $NoDirectCall = false)
	{
		$this->Controller = $Controller;
		$this->ViewName = $Controller->GetFilePrefix();
		$this->ActionName = $Controller->GetActionName();
		$this->Model = $Model;
		$this->ViewData = $ViewData;
		
		if($NoDirectCall)
		{
			if($Controller->IsIncludeAction)
			{
				$this-> Display();
			}
			else
			{
				die("Access denied!");
			}
		}
		else
		{
			$this-> Display();	
		}
	}
	
	private function Display()
	{
		$this->ViewFileName = MVC_VIEW_PATH . DS . strtolower($this->ViewName) . DS . $this->ActionName . '.php';

		if($this->ViewName != null && $this->ActionName != null && !empty($this->ViewName) && !empty($this->ActionName) && file_exists($this->ViewFileName))
		{
			$View = $this;
			$Model = $this->Model;	
			$ViewData = $this->ViewData;	
			ob_start();	
			require_once($this->ViewFileName);			
			$this->Content = ob_get_clean();
			echo $this->Content;
		}
		else
		{
			die("View not found at: " . $this->ViewFileName);
		}
	}
	
	public function Render($ViewFileName, $Model = null, $ViewData = null)
	{
		$ViewFileName = str_replace('.php', '', $ViewFileName);
		
		if($ViewFileName != null && !empty($ViewFileName) && file_exists(MVC_VIEW_PATH . DS . strtolower($ViewFileName) . '.php'))
		{
			$View = $this;
			ob_start();
			require_once(MVC_VIEW_PATH . DS . strtolower($ViewFileName) . '.php');					
			$this->Content = ob_get_clean();	
			echo $this->Content;
		}
	}
	
	public function RenderIn($ViewFileName, $Position, $Model = null, $ViewData = null)
	{
		$ViewFileName = str_replace('.php', '', $ViewFileName);
		
		if($ViewFileName != null && !empty($ViewFileName) && file_exists(MVC_VIEW_PATH . DS . strtolower($ViewFileName) . '.php'))
		{	
			$View = $this;
			$ViewContent = ob_get_contents();
			ob_end_clean();
			ob_start();
			${$Position} = $ViewContent;	
			require_once(MVC_VIEW_PATH . DS . strtolower($ViewFileName) . '.php');	
			$ParentViewContent = ob_get_clean();	
			echo $ParentViewContent;
			exit;	
		}
	}
	
	public function IncludeAction($ControllerName, $Action)
	{
		$this->Controller->IncludeAction($ControllerName, $Action);
	}
	
	public function IncludeModel($ModelName)
	{
		$ModelName = str_replace('.php', '', $ModelName);
		
		if($ModelName != null && !empty($ModelName) && file_exists(MVC_MODEL_PATH . DS . strtolower($ModelName) . '.php'))
		{
			require_once(MVC_MODEL_PATH . DS . strtolower($ModelName) . '.php');
		}
	}
	
	public function IncludeModels($Models)
	{
		if($Models != null && count($Models) > 0)
		{
			foreach($Models as $Model)
			{
				$this->IncludeModel($Model);
			}
		}
	}
	
	public function RedirectAction($ControllerName, $Action)
	{
		$this->Controller->RedirectAction($ControllerName, $Action);
	}
}

?>