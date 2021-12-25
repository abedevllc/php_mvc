<?php

if(!defined("MvcFramework")){ die("Access Denied!"); }

class ProfileController extends \Mvc\Controller
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
    }

	public function Index()
	{
        $this->LoadModel("profile");        
        $ProfileModel = new \ProfileModel();
        $ProfileModel->ConvertFromIdentityUser($this->Identity->CurrentUser);
        $ViewData = array();
        $ViewData["Form"] = $this->GetForm("profile", "ProfileModel", $ProfileModel, false, null, null, null, null, null, null, $this->Language->GetTexts());        
        return new \Mvc\View($this, null, $ViewData);
    }
    
    public function Update()
    {
        ob_clean();

        $this->LoadModel("profile");
        $profile = $this->GetPostedObject("profile", "ProfileModel");
        
        if($profile->Firstname == null || empty($profile->Firstname))
        {
            echo $this->Language->Text("IF_ERROR_USER_FIRSTNAME_NULL");
        }
        else if($profile->Lastname == null || empty($profile->Lastname))
        {
            echo $this->Language->Text("IF_ERROR_USER_LASTNAME_NULL");
        }
        else if($profile->Email == null || empty($profile->Email))
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_NULL");
        }
        else if(!filter_var($profile->Email, FILTER_VALIDATE_EMAIL))
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_INVALID");
        }
        else if($this->Identity->CurrentUser->Email != $profile->Email && $this->Entity->Table("users")->Where("email", "=", $profile->Email)->And()->Where("id", "<>", $this->Identity->CurrentUser->Id)->Get() != null)
        {
            echo $this->Language->Text("IF_ERROR_USER_EMAIL_EXISTS");
        }
        else if($profile->Password != null && strlen($profile->Password) < $this->Identity->Config->PasswordMinimumLength)
        {
            echo $this->Language->Text("IF_ERROR_USER_PASSWORD_LENGTH");
        }
        else if($profile->Password != null && $profile->Password != $profile->PasswordConfirm)
        {
            echo $this->Language->Text("IF_ERROR_USER_PASSWORD_NOT_CONFIRMED");
        }
        else
        {
            if($this->Identity->CurrentUser->Firstname != $profile->Firstname)
            {
                $this->Identity->CurrentUser->Firstname = $profile->Firstname;
            }

            if($this->Identity->CurrentUser->Lastname != $profile->Lastname)
            {
                $this->Identity->CurrentUser->Lastname = $profile->Lastname;
            }

            if($this->Identity->CurrentUser->Email != $profile->Email)
            {
                $this->Identity->CurrentUser->Email = $profile->Email;
            }
           
            if($profile->Picture != null)
            {
                $this->Identity->CurrentUser->Picture = $profile->Picture;
            }

            if($this->Identity->UpdateUser($this->Identity->CurrentUser))
            {
                echo $this->Language->Text("IF_USER_PROFILE_SUCCESSFULY_UPDATED");
            }
            else
            {
                
            }
        }

        exit;
    }
}

?>