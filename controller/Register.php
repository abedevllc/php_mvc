<?php

if(!defined("MvcFramework")){ die("Access Denied!"); }

class RegisterController extends \Mvc\Controller
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
        else if(!$this->Identity->Config->UserCanRegister)
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
        $ViewData["Form"] = $this->GetForm("user", "RegisterUserModel", null, false, null, null, "reloadCaptcha", null, $this->Language->GetTexts());        
        return new \Mvc\View($this, null, $ViewData);
    }

    public function NewUser()
    {
        ob_clean();

        $this->LoadModel("user");  

        $user = $this->GetPostedObject("user", "RegisterUserModel");        
        $security_code = \Identity\Core\Session::Get("security_code");
        
        if($user->Firstname == null || empty($user->Firstname))
        {
            echo $this->Language->Text("IF_ERROR_USER_FIRSTNAME_NULL");
        }
        else if($user->Lastname == null || empty($user->Lastname))
        {
            echo $this->Language->Text("IF_ERROR_USER_LASTNAME_NULL");
        }
        else if($user->Username == null || empty($user->Username))
        {
            echo $this->Language->Text("IF_ERROR_USER_USERNAME_NULL");
        }
        else if(strlen($user->Username) < $this->Identity->Config->UsernameMinimumLength)
        {
            echo $this->Language->Text("IF_ERROR_USER_USERNAME_LENGTH");
        }
        else if($user->Email == null || empty($user->Email))
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_NULL");
        }
        else if(!filter_var($user->Email, FILTER_VALIDATE_EMAIL))
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_INVALID");
        }
        else if($user->Password == null || empty($user->Password))
        {
            echo $this->Language->Text("IF_ERROR_USER_PASSWORD_NULL");
        }
        else if(strlen($user->Password) < $this->Identity->Config->PasswordMinimumLength)
        {
            echo $this->Language->Text("IF_ERROR_USER_PASSWORD_LENGTH");
        }
        else if($user->Password != $user->PasswordConfirm)
        {
            echo $this->Language->Text("IF_ERROR_USER_PASSWORD_NOT_CONFIRMED");
        }
        else if($user->SecurityCode != $security_code)
        {
            echo $this->Language->Text("LOGIN_PAGE_PLS_ENTER_VALID_SECURITY_CODE");
        }
        else if($this->Entity->Table("users")->Where("username", "=", $user->Username)->Get() != null)
        {
            echo $this->Language->Text("IF_ERROR_USER_USERNAME_EXISTS");
        }
        else if($this->Entity->Table("users")->Where("email", "=", $user->Email)->Get() != null)
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_EXISTS");
        }
        else 
        {
            $new_user = new \Identity\Model\User();
            $new_user->Username = $user->Username;
            $new_user->Password = $user->Password;							
            $new_user->LoginTypeId = 1;
            $new_user->IsActive = true;
            $new_user->Email = $user->Email;
            $new_user->Firstname = $user->Firstname;
            $new_user->Lastname = $user->Lastname;
            $new_user->CreatedDate = date("Y-m-d H:i:s");
                            
            $new_user->Id = $this->Identity->AddUser($new_user);

            if($new_user->Id > 0)
            {
                $Role = new \Identity\Model\Role();
                $Role->Id = 2;

                $this->Identity->AddUserRole($new_user, $Role);

                if($this->Identity->Config->UserRegistrationHasToActivate)
                {
                    echo $this->Language->Text("REGISTER_PAGE_SENT_ACTIVATION_MAIL");
                }
                else
                {
                    echo $this->Language->Text("REGISTER_PAGE_SUCCESSFULLY_REGISTERED");
                }
            }
        }

        exit;
    }
}

?>