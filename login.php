<?php
/*
+ ----------------------------------------------------------------------------+
|     e107 website system
|
|     Copyright (C) 2008-2009 e107 Inc 
|     http://e107.org
|
|
|     Released under the terms and conditions of the
|     GNU General Public License (http://gnu.org).
|
|     $Source: /cvs_backup/e107_0.8/login.php,v $
|     $Revision$
|     $Date$
|     $Author$
+----------------------------------------------------------------------------+
*/

require_once("class2.php");
include_lan(e_LANGUAGEDIR.e_LANGUAGE.'/lan_'.e_PAGE);

if (USER || e_LOGIN != e_SELF) // Disable page if user logged in, or some custom e_LOGIN value is used.
{
	header('location:'.e_BASE.'index.php');      
	exit();
}

$HEADER = '';
$FOOTER='';				// Avoids strange displays when debug enabled! (But doesn't completely maintain XHTML formatting)
require_once(HEADERF);
$use_imagecode = ($pref['logcode'] && extension_loaded("gd"));
if ($use_imagecode) 
{
	require_once(e_HANDLER."secure_img_handler.php");
	$sec_img = new secure_image;
}

if (!USER) 
{
	require_once(e_HANDLER."form_handler.php");
	$rs = new form;
	$text = "";
    $allowEmailLogin = varset($pref['allowEmailLogin'],0);
    $ulabel = array(LAN_LOGIN_1,LAN_LOGIN_28,LAN_LOGIN_29);

	$LOGIN_USERNAME_LABEL = $ulabel[$allowEmailLogin];
	$LOGIN_TABLE_LOGINMESSAGE = LOGINMESSAGE;
	$LOGIN_TABLE_USERNAME = "<input class='tbox' type='text' name='username' id='username' size='40' maxlength='100' />";
	$LOGIN_TABLE_PASSWORD = "<input class='tbox' type='password' name='userpass' id='userpass' size='40' maxlength='100' />";
	if (!USER && e107::getSession()->is('challenge') && varset($pref['password_CHAP'],0)) 
	{
	  $LOGIN_TABLE_PASSWORD .= "<input type='hidden' name='hashchallenge' id='hashchallenge' value='".e107::getSession()->get('challenge')."' />\n\n";
	}
	if ($use_imagecode)
	{
		$LOGIN_TABLE_SECIMG_LAN = LAN_LOGIN_13;
		$LOGIN_TABLE_SECIMG_HIDDEN = "<input type='hidden' name='rand_num' value='".$sec_img->random_number."' />";
		$LOGIN_TABLE_SECIMG_SECIMG = $sec_img->r_image();
		$LOGIN_TABLE_SECIMG_TEXTBOC = "<input class='tbox' type='text' name='code_verify' size='15' maxlength='20' />";
	}
	$LOGIN_TABLE_AUTOLOGIN = "<input type='checkbox' name='autologin' value='1' />";
	$LOGIN_TABLE_AUTOLOGIN_LAN = LAN_LOGIN_8;
	$LOGIN_TABLE_SUBMIT = "<input class='button' type='submit' name='userlogin' value=\"".LAN_LOGIN_9."\" />";

	if (!isset($LOGIN_TABLE) || !$LOGIN_TABLE)
	{
		if (file_exists(THEME.'login_template.php'))
		{
			require_once(THEME.'login_template.php');
		}
		else
		{
			require_once(e_BASE.$THEMES_DIRECTORY."templates/login_template.php");
		}
	}
	$text = preg_replace("/\{(.*?)\}/e", 'varset($\1,"\1")', $LOGIN_TABLE);
	echo preg_replace("/\{(.*?)\}/e", 'varset($\1,"\1")', $LOGIN_TABLE_HEADER);

	$login_message = LAN_LOGIN_3." | ".SITENAME;
	$ns->tablerender($login_message, $text, 'login_page');

	$LOGIN_TABLE_FOOTER_USERREG = '&nbsp;';		// In case no registration system enabled
	if ($pref['user_reg'])
	{
		$LOGIN_TABLE_FOOTER_USERREG = "<a href='".e_SIGNUP."'>".LAN_LOGIN_11."</a>";
	}
	echo preg_replace("/\{([^ ]*?)\}/e", 'varset($\1,"\1")', $LOGIN_TABLE_FOOTER);
}

echo "</body></html>";

$sql->db_Close();

?>