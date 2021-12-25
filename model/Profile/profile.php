<?php

if(!defined("MvcFramework")){ die("Access Denied!"); }

/** form_name: profile_model, form_action: index.php?controller=profile&action=update, form_type: ajax, form_validate, form_reset: REGISTER_PAGE_LBL_RESET, form_submit: PROFILE_PAGE_LBL_SAVE */
class ProfileModel extends \Mvc\Model
{
    public function ConvertFromIdentityUser($IdentityUser)
    {
        if($IdentityUser != null)
        {
            $this->Firstname = $IdentityUser->Firstname;
            $this->Lastname = $IdentityUser->Lastname;
            $this->Email = $IdentityUser->Email;
            
            if($IdentityUser->Profile == null)
            {
                $IdentityUser->Include("Profile", "Profiles");
            }
            
            if($IdentityUser->Profile != null)
            {
                $this->Title = $IdentityUser->Profile->Title;
                $this->BirthDay = $IdentityUser->Profile->BirthDay;
                $this->Web = $IdentityUser->Profile->Web;
            }
        }
    }

     /** form_placeholder: PROFILE_PAGE_LBL_TITLE, form_send_on_enter */
     public $Title;

     /** form_required, form_placeholder: REGISTER_PAGE_LBL_FIRSTNAME, form_send_on_enter */
     public $Firstname;

     /** form_required, form_placeholder: REGISTER_PAGE_LBL_LASTNAME, form_send_on_enter */
     public $Lastname;

     /** form_type: text, form_placeholder: PROFILE_PAGE_LBL_BIRTHDAY, form_send_on_enter */
    public $BirthDay;
    
    /** form_type: text, form_placeholder: PROFILE_PAGE_LBL_WEB, form_send_on_enter */
	public $Web;

      /** form_required, form_placeholder: REGISTER_PAGE_LBL_EMAIL, form_send_on_enter */
    public $Email;

    /** form_type:password, form_required, form_placeholder: LOGIN_PAGE_LBL_PASSWORD, form_send_on_enter */
    public $Password;

     /** form_type:password, form_required, form_placeholder: REGISTER_PAGE_LBL_PASSWORD_CONFIRM, form_send_on_enter */
     public $PasswordConfirm;

     /** type:longblob, form_type: file,  form_label: PROFILE_PAGE_LBL_PICTURE, form_label_position: after */
     public $Picture;

    
}



?>