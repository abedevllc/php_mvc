<?php

if(!defined("MvcFramework")){ die("Access Denied!"); }

/** form_name: register_form, form_action: index.php?controller=Register&action=NewUser, form_type: ajax, form_validate, form_reset: REGISTER_PAGE_LBL_RESET, form_submit: REGISTER_PAGE_LBL_REGISTER */
class RegisterUserModel extends \Mvc\Model
{
    /** form_required, form_placeholder: REGISTER_PAGE_LBL_FIRSTNAME, form_send_on_enter */
    public $Firstname;

    /** form_required, form_placeholder: REGISTER_PAGE_LBL_LASTNAME, form_send_on_enter */
    public $Lastname;

    /** form_required, form_placeholder: LOGIN_PAGE_LBL_USERNAME, form_send_on_enter */
    public $Username;

    /** form_required, form_placeholder: REGISTER_PAGE_LBL_EMAIL, form_send_on_enter */
    public $Email;

    /** form_type:password, form_required, form_placeholder: LOGIN_PAGE_LBL_PASSWORD, form_send_on_enter */
    public $Password;

     /** form_type:password, form_required, form_placeholder: REGISTER_PAGE_LBL_PASSWORD_CONFIRM, form_send_on_enter */
     public $PasswordConfirm;

     /** form_placeholder: LOGIN_PAGE_LBL_SECURITY_CODE, form_send_on_enter */
    public $SecurityCode;

    /** form_type: image, image_url: index.php?controller=captcha */
    public $Captcha;

    /** form_type: button, form_style: cursor=pointer;, form_click_event: reloadCaptcha() */
    public $CaptchaRefresher = "LOGIN_PAGE_LBL_CAPTCHA_REFRESH";

     /** form_type: script */
     public $script = "function reloadCaptcha() { ".
        
        "if(typeof jQuery == 'undefined') { ".
			"alert('jQuery not found')".	
			"}".
			"else".
			"{".
                "var src = jQuery('#ef_captcha').attr('src');".
                "jQuery('#ef_captcha').attr('src', src);".				    
			"}".        
        "}";

}

?>