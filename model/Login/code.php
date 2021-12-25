<?php

if(!defined("MvcFramework")){ die("Access Denied!"); }

/** form_name: activation_code_form, form_action: index.php?controller=login&action=ActivateCode, form_type: ajax, form_validate, form_reset: hide, form_submit: LOGIN_PAGE_LBL_ACTIVATE */
class ActivationCode extends \Mvc\Model
{
    /** form_required, form_placeholder: REGISTER_PAGE_LBL_CODE, form_send_on_enter */
    public $Code;

    /** form_placeholder: LOGIN_PAGE_LBL_SECURITY_CODE, form_send_on_enter */
    public $SecurityCode;

    /** form_type: image, image_url: index.php?controller=captcha */
    public $Captcha;

    /** form_type: button, form_style:cursor=pointer;, form_click_event: reloadCaptcha() */
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