<?php

if(!defined("MvcFramework")){ die("Access Denied!"); }

/** form_name: login_form, form_action: index.php?controller=login&action=SignIn, form_type: ajax, form_validate, form_reset: hide, form_submit: LOGIN_PAGE_LBL_LOGIN */
class LoginUserModel extends \Mvc\Model
{
    /** form_required, form_placeholder: LOGIN_PAGE_LBL_USERNAME, form_send_on_enter */
    public $Username;

    /** form_required, form_placeholder: LOGIN_PAGE_LBL_PASSWORD, form_type: password, form_send_on_enter */
    public $Password;

    /** form_placeholder: LOGIN_PAGE_LBL_SECURITY_CODE, form_style: display=none, form_send_on_enter */
    public $SecurityCode;

    /** form_type: image, image_url: index.php?controller=captcha, form_style: display=none; */
    public $Captcha;

    /** form_type: button, form_style: display=none;cursor=pointer, form_click_event: reloadCaptcha() */
    public $CaptchaRefresher = "LOGIN_PAGE_LBL_CAPTCHA_REFRESH";

    /** type: bit, form_type: checkbox, form_label: LOGIN_PAGE_LBL_REMEMBER_ME, form_label_position: after */
    public $RememberMe;

    /** form_type: script */
    public $script = "function reloadCaptcha() { ".
        
        "if(typeof jQuery == 'undefined') { ".
			"alert('jQuery not found')".	
			"}".
			"else".
			"{".
                "var src = jQuery('#user_captcha').attr('src');".
                "jQuery('#user_captcha').attr('src', src);".				    
			"}".        
        "}";
}

?>