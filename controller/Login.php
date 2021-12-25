<?php

if(!defined("MvcFramework")){ die("Access Denied!"); }

class LoginController extends \Mvc\Controller
{
    public function __construct()
    {
        $args = func_get_args();
        call_user_func_array(array($this, 'parent::__construct'), $args);
        
        if($this->Identity == null)
        {
            die("IdentityFramework not found");
        }
        else if($this->Entity == null)
        {
            die("EntityFramework not found");
        }
        else if(!$this->Identity->Config->UserCanLogin)
        {
            $this->RedirectAction("Home", "Index");
        }
        else if($this->Identity->CurrentUser != null)
        {
            $this->RedirectAction("Home", "Index");
        }
    }

	public function Index()
	{
        $this->LoadModel("user");        
        
        $ViewData = array();
        $ViewData["Form"] = $this->GetForm("user", "LoginUserModel", null, false, null, null, null, null, "reloadCaptcha", null, $this->Language->GetTexts());        
        return new \Mvc\View($this, null, $ViewData);
    }

    public function ResendActivationCode()
    {
        if(!$this->Identity->Config->UserRegistrationHasToActivate)
        {
            $this->RedirectAction("Home", "Index");
        }

        $this->LoadModel("email");        
        
        $ViewData = array();
        $ViewData["Form"] = $this->GetForm("email", "ResendActivationCodeEmail", null, false, null, null, null, null, "reloadCaptcha", null, $this->Language->GetTexts());        
        return new \Mvc\View($this, null, $ViewData);
    }

    public function PasswordForgot()
    {
        $this->LoadModel("email");        
        
        $ViewData = array();
        $ViewData["Form"] = $this->GetForm("email", "ResendPasswordForgotEmail", null, false, null, null, null, null,"reloadCaptcha", null, $this->Language->GetTexts());        
        return new \Mvc\View($this, null, $ViewData);
    }

    public function Activate()
    {
        if(!$this->Identity->Config->UserRegistrationHasToActivate)
        {
            $this->RedirectAction("Home", "Index");
        }

        $this->LoadModel("code");        
        
        $ViewData = array();
        $ViewData["Form"] = $this->GetForm("code", "ActivationCode", null, false, null, null, null, null, "reloadCaptcha", null, $this->Language->GetTexts());        
        return new \Mvc\View($this, null, $ViewData);
    }

    public function SignIn()
    {
        ob_clean();

        $this->LoadModel("user");  

        $user = $this->GetPostedObject("user", "LoginUserModel");
        $attempts = \Identity\Core\Session::Get("login_attempts");
        $security_code = \Identity\Core\Session::Get("security_code");

        if($attempts == null || empty($attempts))
        {
            $attempts = 0;
        }

        if($user != null && $attempts >= $this->Identity->Config->LoginMaxAttempts && $user->SecurityCode != $security_code)
        {
            $this->HtmlElement("user_securitycode", "display", "block");
            $this->HtmlElement("user_captcha", "display", "block");
            $this->HtmlElement("user_captcharefresher", "display", "block");

            $attempts++;
            \Identity\Core\Session::Add("login_attempts", $attempts);
            echo $this->Language->Text("LOGIN_PAGE_PLS_ENTER_VALID_SECURITY_CODE");
        }
        else if($user == null || $user->Username == null || $user->Password == null || empty($user->Username) || empty($user->Password))
        {
            $attempts++;
            \Identity\Core\Session::Add("login_attempts", $attempts);
            echo $this->Language->Text("LOGIN_PAGE_PLS_ENTER_DATA");
        }
        else 
        {
            $login_user = $this->Identity->LoginUser($user->Username, $user->Password, $user->RememberMe);
           
            if($login_user == null)
            {
                $attempts++;
                \Identity\Core\Session::Add("login_attempts", $attempts);
                echo $this->Language->Text("LOGIN_PAGE_LOGIN_INVALID");
            }
            else if($login_user instanceof \Exception)
            {
                $attempts++;
                \Identity\Core\Session::Add("login_attempts", $attempts);
                echo $login_user->getMessage();
            }
            else
            {
                echo "<script>window.location.reload();</script>";
            }
        }

        exit;
    }

    public function SignOut()
    {
        ob_clean();

        $this->Identity->Logout();
        $this->RedirectAction("Home", "Index");
    }

    public function SendActivationCode()
    {
        ob_clean();

        if(!$this->Identity->Config->UserRegistrationHasToActivate)
        {
            $this->RedirectAction("Home", "Index");
        }

        $this->LoadModel("email");  

        $email = $this->GetPostedObject("email", "ResendActivationCodeEmail");
        $security_code = \Identity\Core\Session::Get("security_code");

        if($email->Email == null || empty($email->Email))
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_NULL");
        }        
        else if($email->SecurityCode != $security_code)
        {
            echo $this->Language->Text("LOGIN_PAGE_PLS_ENTER_VALID_SECURITY_CODE");
        }
        else if($this->Entity->Table("users")->Where("email", "=", $email->Email)->Get() == null)
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_NOT_FOUND");
        }
        else if($this->Entity->Table("users")->Where("email", "=", $email->Email)->And()->Where("isactive", "=", false)->And()->Where("activationcode", "<>", "null")->Get() == null)
        {
            echo $this->Language->Text("IF_ERROR_USER_ALREADY_ACTIVATED");
        }
        else
        {
            $result = $this->Identity->ResendActivationCode($email->Email);

            if($result && !($result instanceof \Exception))
            {
                echo $this->Language->Text("REGISTER_PAGE_SENT_ACTIVATION_MAIL");
            }
            else
            {
                echo $this->Language->Text("IF_ERROR_MAIL_COULD_NOT_BE_SENT");
            }
        }
    }

    public function ActivateCode()
    {
        ob_clean();

        if(!$this->Identity->Config->UserRegistrationHasToActivate)
        {
            $this->RedirectAction("Home", "Index");
        }

        $this->LoadModel("code");

        $code = $this->GetPostedObject("code", "ActivationCode");
        $security_code = \Identity\Core\Session::Get("security_code");

        if($code->Code == null || empty($code->Code))
        {
            echo $this->Language->Text("IF_ERROR_USER_ACTIVATION_CODE_NULL");
        }
        else if($code->SecurityCode != $security_code)
        {
            echo $this->Language->Text("LOGIN_PAGE_PLS_ENTER_VALID_SECURITY_CODE");
        }
        else if($this->Entity->Table("users")->Where("activationcode", "=", $code->Code)->Get() == null)
        {
            echo $this->Language->Text("IF_ERROR_USER_ACTIVATION_CODE_NOT_FOUND");
        }
        else
        {
            $result = $this->Identity->ActivateCode($code->Code);

            if($result && !($result instanceof \Exception))
            {
                echo $this->Language->Text("IF_ERROR_USER_SUCCESSFULLY_ACTIVATED");
            }
            else
            {
                echo $this->Language->Text("IF_ERROR_USER_COULD_NOT_BE_ACTIVATED");
            }
        }
    }

    public function SendPasswordForgot()
    {
        ob_clean();
        
        $this->LoadModel("email");  

        $email = $this->GetPostedObject("email", "ResendPasswordForgotEmail");
        $security_code = \Identity\Core\Session::Get("security_code");

        if($email->Email == null || empty($email->Email))
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_NULL");
        }        
        else if($email->SecurityCode != $security_code)
        {
            echo $this->Language->Text("LOGIN_PAGE_PLS_ENTER_VALID_SECURITY_CODE");
        }
        else if($this->Entity->Table("users")->Where("email", "=", $email->Email)->Get() == null)
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_NOT_FOUND");
        }
        else
        {
            $result = $this->Identity->PasswordForgot($email->Email);

            if($result && !($result instanceof \Exception))
            {
                echo $this->Language->Text("LOGIN_PAGE_SENT_TEMP_PASSWORD");
            }
            else
            {
                echo $this->Language->Text("IF_ERROR_MAIL_COULD_NOT_BE_SENT");
            }
        }
    }
}

?>