<?php

namespace Mvc;

if(!defined("MvcFramework")){ die("Access Denied!"); }

class Controller
{
	private $Name;
	private $FileName;
	private $FilePrefix;
	public $ActionName;
	public $IsIncludeAction;
	public $Identity;
	public $Entity;
	public $Language;

	public function __construct($Identity = null, $Entity = null, $Language = null, $Name = null, $FileName = null)
	{
		$this->Identity = $Identity;
		$this->Entity = $Entity;
		$this->Language = $Language;
		$this->Name = $Name;
		$this->FilePrefix = strtolower($FileName);
		$this->FileName = strtolower($FileName. '.php');
		$this->IsIncludeAction = false;
	}
		
	public function LoadIncludes($ActionName)
	{
		$this->ActionName = $ActionName;
		
		if($this->FilePrefix != null && !empty($this->FilePrefix) && $this->ActionName != null && !empty($this->ActionName) && file_exists(MVC_MODEL_PATH . DS . strtolower($this->FilePrefix) . DS . strtolower($this->ActionName) . '.php'))
		{
			require_once(MVC_MODEL_PATH . DS . strtolower($this->FilePrefix) . DS . strtolower($this->ActionName) . '.php');
		}
	}

	public function LoadModel($Model)
	{		
		if($this->FilePrefix != null && !empty($this->FilePrefix) && $Model!= null && !empty($Model) && file_exists(MVC_MODEL_PATH . DS . strtolower($this->FilePrefix) . DS . strtolower($Model) . '.php'))
		{
			require_once(MVC_MODEL_PATH . DS . strtolower($this->FilePrefix) . DS . strtolower($Model) . '.php');
		}
	}
	
	public function GetName()
	{
		return $this->Name;
	}
	
	public function GetFileName()
	{
		return $this->FileName;
	}
	
	public function GetFilePrefix()
	{
		return $this->FilePrefix;
	}
	
	public function GetActionName()
	{
		return $this->ActionName;
	}
	
	public function IncludeAction($ControllerName, $Action)
	{
		$Controller = $this->GetController($ControllerName);
		
		if($Controller != null)
		{
			$Controller->LoadIncludes($Action);
				
			if(method_exists($Controller, $Action))
			{
				$Controller->IsIncludeAction = true;
				$Controller->$Action();
				$Controller->IsIncludeAction = false;
			}
		}
	}
	
	public function RedirectAction($ControllerName, $Action)
	{
		header("Location: index.php?controller=" . $ControllerName . "&action=" . $Action);
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

	public function GetForm($Key, $ClassName, $Object = null, $ReturnArray = false, $Columns = null, $ParentForm = null, $ParentFormAjaxId = null, $Class = null, $OnCompleteCallBack = null, $options = null, $translations = null, $form_reset = null, $form_submit = null, $form_action = null, $form_method = null, $form_enctype = null, $form_type = null, $form_loading = null)
	{
		$result = null;

		if($this->Entity == null)
		{
			die("EntityFramework not found");
		}
		else
		{
			$Table = new \Entity\Model\Table($Key, $ClassName, $this->Entity, $this->Entity->LowerCase, $this->Entity->Pluralize, $this->Entity->Collation);
			$result = $Table->Form($Object, $ReturnArray, $Columns, $ParentForm, $ParentFormAjaxId, $Class, $OnCompleteCallBack, $options, $translations, $form_reset, $form_submit, $form_action, $form_method, $form_enctype, $form_type, $form_loading);
		}

		return $result;
	}

	public function GetPostedObject($Key, $ClassName)
	{
		$result = null;

		if($this->Entity == null)
		{
			die("EntityFramework not found");
		}
		else
		{
			$Table = new \Entity\Model\Table($Key, $ClassName, $this->Entity, $this->Entity->LowerCase, $this->Entity->Pluralize, $this->Entity->Collation);

			if($Table->HasPost())
			{
				$result = $Table->GetObjectByPost();
			}
		}

		return $result;
	}

	public function Json($Object)
	{
		ob_clean();
		echo json_encode($Object);
		exit;
	}

	public function HtmlElement($id, $css_attribute, $css_value)
	{
		echo "<script>".
		"if(typeof jQuery == 'undefined') { ".
			"alert('jQuery not found')".	
			"}".
			"else".
			"{".
			"jQuery('#". $id . "').css('" . $css_attribute . "', '" . $css_value ."');".
			"}".
		"</script>";
	}

	public function GenerateCaptchaCode()
	{
		$SecurityCode = \Identity\Core\Security::Salt(6);
		\Identity\Core\Session::Remove("security_code");
		\Identity\Core\Session::Add("security_code", $SecurityCode);

		return $SecurityCode;
	}
}

?>