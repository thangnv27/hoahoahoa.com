<?php
include_once('wp_aff_includes1.php');
include_once('wp_aff_utility_functions.php');

function show_aff_platform_settings_page()
{
    if(isset($_GET['wpap_hide_sc_msg'])){//Turn off the super cache warning display
            $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
            $wp_aff_platform_config->setValue('wp_aff_do_not_show_sc_warning', '1');
            $wp_aff_platform_config->saveConfig();		
    }

    echo '<div class="wrap">';
    echo wp_aff_misc_admin_css();
    $action = isset($_GET['settings_action'])?$_GET['settings_action']:'';
    ?>
    <h2>WP Affiliate Platform Settings v <?php echo WP_AFFILIATE_PLATFORM_VERSION; ?></h2>
    <h2 class="nav-tab-wrapper">
    <a class="nav-tab <?php echo ($action=='') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=wp_aff_platform_settings">General Settings</a>
    <a class="nav-tab <?php echo ($action=='email') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=wp_aff_platform_settings&settings_action=email">Email Settings</a>
    <a class="nav-tab <?php echo ($action=='autoresponder') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=wp_aff_platform_settings&settings_action=autoresponder">Autoresponder Settings</a>
    <a class="nav-tab <?php echo ($action=='wp_user_settings') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=wp_aff_platform_settings&settings_action=wp_user_settings">WP User Settings</a>
    <a class="nav-tab <?php echo ($action=='advanced_settings') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=wp_aff_platform_settings&settings_action=advanced_settings">Advanced Settings</a>
    <a class="nav-tab <?php echo ($action=='integration_settings') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=wp_aff_platform_settings&settings_action=integration_settings">Integration Related</a>
    </h2>
    <?php
    echo '<div id="poststuff"><div id="post-body">'; 
    switch ($action)
    {
        case 'email':
            include_once('wp_aff_email_settings_menu.php');
            wp_aff_email_settings();
            break;
        case 'autoresponder':
            include_once('wp_aff_autoresponder_settings.php');
            wp_affiliate_auto_responder_settings();
            break;    
        case 'wp_user_settings':
            include_once('wp_aff_wp_user_settings_menu.php');
            wp_aff_wp_user_settings_menu_page();
            break;  
        case 'advanced_settings':
            include_once('wp_aff_advanced_settings_menu.php');
            wp_aff_advanced_settings_menu_page();
            break;
        case 'integration_settings':
            include_once('wp_aff_integration_settings_menu.php');
            wp_aff_integration_settings_menu_page();
            break;                    
        default:
            show_aff_platform_general_settings_page();
            break;
    }

    echo '</div></div>';
    echo '</div>';
}

function show_aff_platform_general_settings_page()
{
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

    if (isset($_POST['info_update']))
    {
        //Do some data validation
        $error_msg = "";
        $aff_url_validation_error_msg_ignore = "<p><i>If you know for sure that the URL is correct then ignore this message. You can copy and paste the URL in a browser's address bar to make sure the URL is correct.</i></p>";		
        if(!wp_aff_is_valid_url_if_not_empty($_POST["wp_aff_default_affiliate_landing_url"]))
        {
        $error_msg .= "<br /><strong>The URL specified in the \"Default Landing Page\" field does not seem to be a valid URL! Please check this value again:</strong>";
        $error_msg .= "<br />".$_POST["wp_aff_default_affiliate_landing_url"]."<br />";
        }		
        if(!wp_aff_is_valid_url_if_not_empty($_POST["wp_aff_login_url"]))
        {
        $error_msg .= "<br /><strong>The URL specified in the \"Affiliate Login URL\" field does not seem to be a valid URL! Please check this value again:</strong>";
        $error_msg .= "<br />".$_POST["wp_aff_login_url"]."<br />";
        }
        if(!wp_aff_is_valid_url_if_not_empty($_POST["wp_aff_terms_url"]))
        {
        $error_msg .= "<br /><strong>The URL specified in the \"Terms & Conditions URL\" field does not seem to be a valid URL! Please check this value again:</strong>";
        $error_msg .= "<br />".$_POST["wp_aff_terms_url"]."<br />";
        }

        if(!empty($error_msg)){
            echo '<div id="message" class="error"><p><strong>';
            echo $error_msg;
            echo $aff_url_validation_error_msg_ignore;
            echo '</strong></p></div>';		
        }
    }
        
    if (isset($_POST['wp_aff_reset_log_file']))
    {
        if(wp_aff_reset_log_files()){
            echo '<div id="message" class="updated fade"><p><strong>Debug log files have been reset!</strong></p></div>';
        }
        else{
            echo '<div id="message" class="updated fade"><p><strong>Debug log files could not be reset!</strong></p></div>';
        }
    }
    
    if (isset($_POST['info_update']))
    {   	    	    	
    	update_option('wp_aff_platform_version', WP_AFFILIATE_PLATFORM_VERSION);
    	
    	update_option('wp_aff_language', (string)$_POST["wp_aff_language"]);
	update_option('wp_aff_site_title', stripslashes((string)$_POST["wp_aff_site_title"]));
        update_option('wp_aff_cookie_life', (string)$_POST["wp_aff_cookie_life"]);
        update_option('wp_aff_currency_symbol', (string)$_POST["wp_aff_currency_symbol"]);
        update_option('wp_aff_currency', (string)$_POST["wp_aff_currency"]);
        update_option('wp_aff_contact_email', (string)$_POST["wp_aff_contact_email"]);
        update_option('wp_aff_default_affiliate_landing_url', trim($_POST["wp_aff_default_affiliate_landing_url"]));
        update_option('wp_aff_login_url', (string)$_POST["wp_aff_login_url"]);
        update_option('wp_aff_terms_url', (string)$_POST["wp_aff_terms_url"]);
        update_option('wp_aff_enable_clicks_custom_field', ($_POST['wp_aff_enable_clicks_custom_field']!='') ? 'checked="checked"':'' );        

        $wp_aff_platform_config->setValue('wp_aff_enable_manual_signup_approval',($_POST['wp_aff_enable_manual_signup_approval']=='1') ? '1':'');
        update_option('wp_aff_disable_visitor_signup', ($_POST['wp_aff_disable_visitor_signup']!='') ? 'checked="checked"':'' );
	$wp_aff_platform_config->setValue('wp_aff_make_paypal_email_required',($_POST['wp_aff_make_paypal_email_required']=='1') ? '1':'');        
        $wp_aff_platform_config->setValue('wp_aff_hide_tax_id_field',($_POST['wp_aff_hide_tax_id_field']=='1') ? '1':'');
        update_option('wp_aff_admin_notification', ($_POST['wp_aff_admin_notification']!='') ? 'checked="checked"':'' );
        $wp_aff_platform_config->setValue('wp_aff_enable_registration_bonus',($_POST['wp_aff_enable_registration_bonus']=='1') ? '1':'');
        $wp_aff_platform_config->setValue('wp_aff_registration_bonus_amt',trim($_POST['wp_aff_registration_bonus_amt']));

        update_option('wp_aff_show_buyer_details_to_affiliates', ($_POST['wp_aff_show_buyer_details_to_affiliates']!='') ? 'checked="checked"':'' );
        $wp_aff_platform_config->setValue('wp_aff_show_buyer_details_name_to_affiliates',($_POST['wp_aff_show_buyer_details_name_to_affiliates']=='1') ? '1':'');
        $wp_aff_platform_config->setValue('wp_aff_show_txn_id_to_affiliates',($_POST['wp_aff_show_txn_id_to_affiliates']=='1') ? '1':'');
        $wp_aff_platform_config->setValue('wp_aff_do_not_show_powered_by_section',($_POST['wp_aff_do_not_show_powered_by_section']=='1') ? '1':'');
        update_option('wp_aff_user_affilate_id', (string)$_POST["wp_aff_user_affilate_id"]);
        
        update_option('wp_aff_zipcode_url', (string)$_POST["wp_aff_zipcode_url"]);

        update_option('wp_aff_use_fixed_commission', ($_POST['wp_aff_use_fixed_commission']!='') ? 'checked="checked"':'' );
        $curr_symbol = get_option('wp_aff_currency_symbol');
        $commission_level = (string)$_POST["wp_aff_commission_level"];
		$commission_level = str_replace("%","",$commission_level);		
		$commission_level = str_replace($curr_symbol,"",$commission_level);				
		update_option('wp_aff_commission_level', $commission_level);
        update_option('wp_aff_commission_reversal', ($_POST['wp_aff_commission_reversal']!='') ? 'checked="checked"':'' );
        //update_option('wp_aff_fixed_comm_amt', (string)$_POST["wp_aff_fixed_comm_amt"]);

        update_option('wp_aff_use_2tier', ($_POST['wp_aff_use_2tier']!='') ? 'checked="checked"':'' );
        $commission_level = (string)$_POST["wp_aff_2nd_tier_commission_level"];
		$commission_level = str_replace("%","",$commission_level);
		$commission_level = str_replace($curr_symbol,"",$commission_level);		        
        update_option('wp_aff_2nd_tier_commission_level', $commission_level);
        //update_option('wp_aff_2nd_tier_fixed_comm_amt', (string)$_POST["wp_aff_2nd_tier_fixed_comm_amt"]);
        update_option('wp_aff_2nd_tier_duration', (string)$_POST["wp_aff_2nd_tier_duration"]);

        update_option('wp_aff_use_recaptcha', ($_POST['wp_aff_use_recaptcha']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_captcha_public_key', (string)$_POST["wp_aff_captcha_public_key"]);
        update_option('wp_aff_captcha_private_key', (string)$_POST["wp_aff_captcha_private_key"]);
        
        update_option('wp_aff_use_custom_color', ($_POST['wp_aff_use_custom_color']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_header_color', (string)$_POST["wp_aff_header_color"]);
        update_option('wp_aff_header_font_color', (string)$_POST["wp_aff_header_font_color"]);
        update_option('wp_aff_footer_color', (string)$_POST["wp_aff_footer_color"]);
        
        $tmpmsg1 = htmlentities(stripslashes($_POST['wp_aff_index_body']), ENT_COMPAT, "UTF-8");
        $wp_aff_platform_config->setValue('wp_aff_index_title',stripslashes((string)$_POST["wp_aff_index_title"]));
		$wp_aff_platform_config->setValue('wp_aff_index_body',$tmpmsg1);
		$tmpmsg2 = htmlentities(stripslashes($_POST['wp_aff_welcome_page_msg']), ENT_COMPAT, "UTF-8");
		$wp_aff_platform_config->setValue('wp_aff_welcome_page_msg',$tmpmsg2);
	
        
        update_option('wp_aff_enable_3rd_party', ($_POST['wp_aff_enable_3rd_party']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_sandbox_mode', ($_POST['wp_aff_sandbox_mode']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_pdt_identity_token', trim($_POST["wp_aff_pdt_identity_token"]));

        update_option('wp_aff_enable_debug', ($_POST['wp_aff_enable_debug']=='1') ? '1':'' );        
        
        $wp_aff_platform_config->saveConfig();
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Options Updated!';
        echo '</strong></p></div>';
    }
        
    $aff_language = get_option('wp_aff_language');
    if (empty($aff_language)) $aff_language = 'eng.php';
    
    $wp_aff_login_url = get_option('wp_aff_login_url');
    if (empty($wp_aff_login_url))
    {
        $wp_aff_login_url = WP_AFF_PLATFORM_URL.'/affiliates/login.php';
    }
           
    if (get_option('wp_aff_commission_reversal'))
        $wp_aff_commission_reversal = 'checked="checked"';
    else
        $wp_aff_commission_reversal = '';
        
    if (get_option('wp_aff_use_fixed_commission'))
        $wp_aff_use_fixed_commission = 'checked="checked"';
    else
        $wp_aff_use_fixed_commission = '';

    if (get_option('wp_aff_admin_notification'))
        $wp_aff_admin_notification = 'checked="checked"';
    else
        $wp_aff_admin_notification = '';
                
    if (get_option('wp_aff_show_buyer_details_to_affiliates'))
        $wp_aff_show_buyer_details_to_affiliates = 'checked="checked"';
    else
        $wp_aff_show_buyer_details_to_affiliates = '';
                
    if (get_option('wp_aff_use_2tier'))
        $wp_aff_use_2tier = 'checked="checked"';
    else
        $wp_aff_use_2tier = '';

    if (get_option('wp_aff_use_recaptcha'))
        $wp_aff_use_recaptcha = 'checked="checked"';
    else
        $wp_aff_use_recaptcha = '';
        
    if (get_option('wp_aff_use_custom_color'))
        $wp_aff_use_custom_color = 'checked="checked"';
    else
        $wp_aff_use_custom_color = ''; 

    $wp_aff_index_title = $wp_aff_platform_config->getValue('wp_aff_index_title');
    if(empty($wp_aff_index_title))
    {
        $wp_aff_index_title = "Welcome to The Affiliate Center";
    }    
    $wp_aff_index_body_tmp = $wp_aff_platform_config->getValue('wp_aff_index_body');//get_option('wp_aff_index_body');
    if(empty($wp_aff_index_body_tmp))
    {
        $wp_aff_index_body_tmp = wp_aff_default_index_body();
    }
    $wp_aff_index_body = html_entity_decode($wp_aff_index_body_tmp, ENT_COMPAT, "UTF-8");
    
    $wp_aff_welcome_page_msg = $wp_aff_platform_config->getValue('wp_aff_welcome_page_msg');
    $wp_aff_welcome_page_msg = html_entity_decode($wp_aff_welcome_page_msg, ENT_COMPAT, "UTF-8");

    if (get_option('wp_aff_enable_3rd_party'))
        $wp_aff_enable_3rd_party = 'checked="checked"';
    else
        $wp_aff_enable_3rd_party = ''; 
        
    if (get_option('wp_aff_sandbox_mode'))
        $wp_aff_sandbox_mode = 'checked="checked"';
    else
        $wp_aff_sandbox_mode = '';     
 
    ?>

    <p class="wp_affiliate_grey_box">
    For information and detailed documentation please visit the 
    <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate" target="_blank">WordPress Affiliate Platform Documentation Site</a>
    <br /><br />
    Like the plugin? Give us a <a href="http://www.tipsandtricks-hq.com/?p=1474#gfts_share" target="_blank">thumbs up here</a> by clicking on a share button.
    </p>

    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />

    <div class="postbox">
    <h3><label for="title">General Settings</label></h3>
    <div class="inside">

    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Affiliate Site Language:</strong>
    </td><td align="left">
	<select name="wp_aff_language">
	<option value="eng.php" <?php if($aff_language=="eng.php")echo 'selected="selected"';?>><?php echo "English" ?></option>
	<option value="ita.php" <?php if($aff_language=="ita.php")echo 'selected="selected"';?>><?php echo "Italian" ?></option>
	<option value="spa.php" <?php if($aff_language=="spa.php")echo 'selected="selected"';?>><?php echo "Spanish" ?></option>
	<option value="ger.php" <?php if($aff_language=="ger.php")echo 'selected="selected"';?>><?php echo "German" ?></option>
	<option value="nld.php" <?php if($aff_language=="nld.php")echo 'selected="selected"';?>><?php echo "Dutch" ?></option>
	<option value="fr.php" <?php if($aff_language=="fr.php")echo 'selected="selected"';?>><?php echo "French" ?></option>
	<option value="heb.php" <?php if($aff_language=="heb.php")echo 'selected="selected"';?>><?php echo "Hebrew" ?></option>
	<option value="ru.php" <?php if($aff_language=="ru.php")echo 'selected="selected"';?>><?php echo "Russian" ?></option>
        <option value="th.php" <?php if($aff_language=="th.php")echo 'selected="selected"';?>><?php echo "Thai" ?></option>
        <option value="bg.php" <?php if($aff_language=="bg.php")echo 'selected="selected"';?>><?php echo "Bulgarian" ?></option>
        <option value="da.php" <?php if($aff_language=="da.php")echo 'selected="selected"';?>><?php echo "Danish" ?></option>
        <option value="chi.php" <?php if($aff_language=="chi.php")echo 'selected="selected"';?>><?php echo "Chinese" ?></option>
        <option value="ro.php" <?php if($aff_language=="ro.php")echo 'selected="selected"';?>><?php echo "Romanian" ?></option>
        <option value="vn.php" <?php if($aff_language=="vn.php")echo 'selected="selected"';?>><?php echo "Vietnamese" ?></option>
	</select>
    </td></tr>
	
    <tr valign="top"><td width="25%" align="left">
    <strong>Affiliate Site Title:</strong>
    </td><td align="left">
    <input name="wp_aff_site_title" type="text" size="40" value="<?php echo get_option('wp_aff_site_title'); ?>"/>
    <br /><i>This will be shown in the header of the affiliate site</i><br />
    </td></tr>
   
    <tr valign="top"><td width="25%" align="left">
    <strong>Cookie Life (Days):</strong>
    </td><td align="left">
    <input name="wp_aff_cookie_life" type="text" size="5" value="<?php echo get_option('wp_aff_cookie_life'); ?>"/> Days
    <br /><i>This is the Cookie Life Time. A referrer will be awarded for a sale if the sale is made before the cookie life expires</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Currency Symbol:</strong>
    </td><td align="left">
    <input name="wp_aff_currency_symbol" type="text" size="2" value="<?php echo get_option('wp_aff_currency_symbol'); ?>"/>
    <br /><i>Example: $, &#163;, &#8364; etc. This symbol will be shown next to the payment amount.</i><br />
    </td></tr>  
        
    <tr valign="top"><td width="25%" align="left">
    <strong>Currency Code:</strong>
    </td><td align="left">
    <input name="wp_aff_currency" type="text" size="3" value="<?php echo get_option('wp_aff_currency'); ?>"/>
    <br /><i>eg. USD, AUD, GBP etc. The affiliates will earn commission in this currency</i><br />
    </td></tr>  


    <tr valign="top"><td width="25%" align="left">
    <strong>Contact Email Address:</strong>
    </td><td align="left">
    <input name="wp_aff_contact_email" type="text" size="50" value="<?php echo get_option('wp_aff_contact_email'); ?>"/>
    <br /><i>The affiliates will be able to contact the admin using this email address</i><br />
    </td></tr>  

    <tr valign="top"><td width="25%" align="left">
    <strong>Default Landing Page:</strong>
    </td><td align="left">
    <input name="wp_aff_default_affiliate_landing_url" type="text" size="100" value="<?php echo get_option('wp_aff_default_affiliate_landing_url'); ?>"/>
    <br /><i>This is the URL where your affiliates will send traffic to by default. You can configure additional text links and banner ads from the <a href="admin.php?page=edit_banners">Add/Edit Ads</a> menu.</i><br />
    </td></tr>  
    
    <tr valign="top"><td width="25%" align="left">
    <strong>Affiliate Login URL:</strong>
    </td><td align="left">
    <input name="wp_aff_login_url" type="text" size="100" value="<?php echo $wp_aff_login_url; ?>"/>
    <br /><i>The affiliates will be able to log in at this URL. You do not need to change it unless you have customized your affiliate area/portal using <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/setting-up-the-affiliate-viewarea-315" target="_blank">this instruction</a>.</i><br />
    </td></tr>  

    <tr valign="top"><td width="25%" align="left">
    <strong>Terms & Conditions URL:</strong>
    </td><td align="left">
    <input name="wp_aff_terms_url" type="text" size="100" value="<?php echo get_option('wp_aff_terms_url'); ?>"/>
    <br /><i>URL of the affiliate Terms and Conditions page. Leave empty if you do not have a Terms and Conditions page.</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Enable Custom Field Tracking:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_enable_clicks_custom_field" value="1" <?php echo get_option('wp_aff_enable_clicks_custom_field'); ?> />
    <br /><i>Enable this if you want your affiliates to be able to track a custom field for the clicks. <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=357" target="_blank">Read More Here</a></i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Show Buyer Details to Affiliates in the Affiliate Area:</strong>
    </td><td align="left">
    <input name="wp_aff_show_buyer_details_name_to_affiliates" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates')=='1'){echo 'checked="checked"';} ?> value="1"/> Show Buyer Name    
    <br /><input type="checkbox" name="wp_aff_show_buyer_details_to_affiliates" value="1" <?php echo $wp_aff_show_buyer_details_to_affiliates; ?> /> Show Buyer Email Address
    <br /><input type="checkbox" name="wp_aff_show_txn_id_to_affiliates" value="1" <?php if($wp_aff_platform_config->getValue('wp_aff_show_txn_id_to_affiliates')=='1'){echo 'checked="checked"';} ?> value="1"/> Show Transaction ID
    <br /><i>By default, the buyer details from a sale is only available to the site admin (this is to comply with the privacy policy of most websites). If you want the buyer details to be available to the affiliates then check this option (make sure you inform your buyers that their details will be revealed to 3rd party affiliates otherwise they can get very upset).</i><br />
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    <strong>Your Tips & Tricks HQ Affiliate ID:</strong>
    </td><td align="left">
    <input name="wp_aff_do_not_show_powered_by_section" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_do_not_show_powered_by_section')=='1'){echo 'checked="checked"';} ?> value="1"/>
    Turn off the affiliate ID display section (this will turn off the powered by section in the affiliate area)
    <br />
    <input name="wp_aff_user_affilate_id" type="text" size="15" value="<?php echo get_option('wp_aff_user_affilate_id'); ?>"/> (optional)
    <br /><i>If you have signed up for an affilate account on <a href="https://www.tipsandtricks-hq.com/affiliate_program" target="_blank">Tips and Tricks HQ</a> then you can specify your affiliate ID here to promote our product and get rewarded for it.</i><br />
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    <strong>Zip Postal Code URL:</strong>
    </td><td align="left">
    <input name="wp_aff_zipcode_url" type="text" value="<?php echo get_option('wp_aff_zipcode_url'); ?>" class="regular-text" />
	<br /><span class="description">Help users get exactly zip code where they live.</span>
    </td></tr>
    
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">Affiliate Signup/Registration Specific Settings</label></h3>
	<div class="inside">
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
    
    <tr valign="top"><td width="25%" align="left">
    <strong>Manually Approve Affiliate Registration:</strong>
    </td><td align="left">
    <input name="wp_aff_enable_manual_signup_approval" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_enable_manual_signup_approval')=='1'){echo 'checked="checked"';} ?> value="1"/>
    <br /><i>If you want to manually approve the registration of each affiliate signup then use this option. An affiliate's account will be set to "Pending" status after registration when this is enabled. You will need to manually set the affiliate's status to "Approved" from the manage affiliates menu.</i><br />
    </td></tr>
        
    <tr valign="top"><td width="25%" align="left">
    <strong>Do Not Allow Visitors to Signup:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_disable_visitor_signup" value="1" <?php echo get_option('wp_aff_disable_visitor_signup'); ?> />
    <br /><i>Check this box if you don't want to allow your visitors to be able to sign up as an affiliate. If you want to selectively create accounts for your affiliates from the admin dashboard then check this option.</i><br />
    </td></tr>

	<tr valign="top"><td width="25%" align="left">
	<strong>Make PayPal Email Address a Required Field:</strong>
	</td><td align="left">
	<input name="wp_aff_make_paypal_email_required" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_make_paypal_email_required')=='1'){echo 'checked="checked"';} ?> value="1"/><br />
	<i>If checked, the PayPal email address field will be a required field on the affiliate signup page (can be useful if you only want to pay affiliate commission via PayPal only).</i><br />
	</td></tr>

	<tr valign="top"><td width="25%" align="left">
	<strong>Hide the Tax ID / SSN Field:</strong>
	</td><td align="left">
	<input name="wp_aff_hide_tax_id_field" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_hide_tax_id_field')=='1'){echo 'checked="checked"';} ?> value="1"/><br />
	<i>If checked, the Tax ID/SSN field won't be displayed on the affiliate registration page.</i><br />
	</td></tr>
	
    <tr valign="top"><td width="25%" align="left">
    <strong>Send Signup Notification to Admin:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_admin_notification" value="1" <?php echo $wp_aff_admin_notification; ?> />
    <br /><i>Check this option if you want to get notified via email when a new affiliate signs up.</i><br />
    </td></tr>
    
	<tr valign="top"><td width="25%" align="left">
	<strong>Give Registration Bonus:</strong>
	</td><td align="left">
	Enable Bonus: <input name="wp_aff_enable_registration_bonus" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_enable_registration_bonus')=='1'){echo 'checked="checked"';} ?> value="1"/>
	<br />Bonus Amount: <input name="wp_aff_registration_bonus_amt" type="text" size="5" value="<?php echo $wp_aff_platform_config->getValue('wp_aff_registration_bonus_amt'); ?>"/> (Only enter the bonus amount number)
	<br /><i>Use this option to apply an one time bonus amount to the affiliate's account when the user registers.</i><br />
	</td></tr>
	    
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">Commission Settings</label></h3>
	<div class="inside">
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Use Fixed Commission Amount:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_use_fixed_commission" value="1" <?php echo $wp_aff_use_fixed_commission; ?> />
    <br /><i>Check this box if you want to use fixed commission amount ($) rather than a percentage (%) value. Leave it unchecked to use a percentage value for affiliate commission calculation.</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Commission Level:</strong>
    </td><td align="left">
    <input name="wp_aff_commission_level" type="text" size="4" value="<?php echo get_option('wp_aff_commission_level'); ?>"/>
    <br /><i>Only enter the number (do not use "%" or "$" sign). This is the default commission level for a newly joined affiliate. The commission level for individual affiliate can be changed by editing the affiliates details</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Use Automatic Commission Reversal:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_commission_reversal" value="1" <?php echo $wp_aff_commission_reversal; ?> />
    <br /><i>Check this box if you want to automatically reverse the commission for refunded products. Only works when used with <a href="http://www.tipsandtricks-hq.com/?p=1059" target="_blank">WP eStore</a>, WooCommerce or direct PayPal integration.</i><br />
    </td></tr>

    </table>
    </div></div>


	<div class="postbox">
	<h3><label for="title">2nd Tier Affiliate Settings (If you want to use a 2 tier affiliate model then use this section). <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=112" target="_blank"><strong>What is two-tier affiliate model?</strong></a></label></h3>
	<div class="inside">
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Use 2 Tier Affiliate Model:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_use_2tier" value="1" <?php echo $wp_aff_use_2tier; ?> />
    <br /><i>Check this box if you want to use a 2 tier affiliate model (two level of affiliates).</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>2nd Tier Commission Level:</strong>
    </td><td align="left">
    <input name="wp_aff_2nd_tier_commission_level" type="text" size="4" value="<?php echo get_option('wp_aff_2nd_tier_commission_level'); ?>"/>
    <br /><i>Only enter the number (do not use "%" or "$" sign). The commission that the parent affiliate should get (eg. 10%). If you are using a fixed commission structure then enter the fixed amount here.</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Duration:</strong>
    </td><td align="left">
    <input name="wp_aff_2nd_tier_duration" type="text" size="5" value="<?php echo get_option('wp_aff_2nd_tier_duration'); ?>"/> Day(s)
    <br /><i>Number of days the parent affiliate receives commission (eg. 365 days). Leave empty for lifetime.</i><br />
    </td></tr>
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">reCAPTCHA Settings (If you want to use <a href="http://www.google.com/recaptcha/" target="_blank">reCAPTCHA</a> then you need to get reCAPTCHA API keys from <a href="http://www.google.com/recaptcha/" target="_blank">here</a> and use in the settings below)</label></h3>
	<div class="inside">
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Use reCAPTCHA:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_use_recaptcha" value="1" <?php echo $wp_aff_use_recaptcha; ?> />
    <br /><i>Check this box if you want to use <a href="http://www.google.com/recaptcha/" target="_blank">reCAPTCHA</a> on the affiliate signs up form.</i><br />
    </td></tr>
    <tr valign="top"><td width="25%" align="left">
    <strong>Public Key:</strong>
    </td><td align="left">
    <input name="wp_aff_captcha_public_key" type="text" size="50" value="<?php echo get_option('wp_aff_captcha_public_key'); ?>"/>
    <br /><i>The public key for the reCAPTCHA API</i><br />
    </td></tr>  
    <tr valign="top"><td width="25%" align="left">
    <strong>Private Key:</strong>
    </td><td align="left">
    <input name="wp_aff_captcha_private_key" type="text" size="50" value="<?php echo get_option('wp_aff_captcha_private_key'); ?>"/>
    <br /><i>The private key for the reCAPTCHA API</i><br />
    </td></tr>
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">Affiliate Area/Center Related Options</label></h3>
	<div class="inside">
            
    <p>The info from this section is shown in the affiliate area of your site. Read <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/setting-up-the-affiliate-viewarea-315" target="_blank">this documentation</a> to learn about the affiliate area/portal.</p>
    
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Index Page Title:</strong>
    </td><td align="left">
    <input name="wp_aff_index_title" type="text" size="80" value="<?php echo $wp_aff_index_title; ?>"/>
    <br /><i>This title will appear on the index page of your affiliate center</i><br />
    </td></tr>  
    <tr valign="top"><td width="25%" align="left">
    <strong>Index Page Message:</strong>
    </td><td align="left">
    <p>The following will appear on the index page of your affiliate portal</p>
    <?php 
    $wp_aff_index_body_settings = array('textarea_name' => 'wp_aff_index_body');
    wp_editor($wp_aff_index_body, "wp_aff_index_body_editor_content", $wp_aff_index_body_settings);
    ?>
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    <strong>Welcome Page Message (optional):</strong>
    </td><td align="left">
    <p>If you want to add extra message/info for your affiliates then add it in the following field. This message will appear on the welcome page (the page affiliates see right after they log in)</p>
    <?php 
    $wp_aff_welcome_page_msg_settings = array('textarea_name' => 'wp_aff_welcome_page_msg');
    wp_editor($wp_aff_welcome_page_msg, "wp_aff_welcome_page_msg_editor_content", $wp_aff_welcome_page_msg_settings);
    ?>    
    </td></tr>
        
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">3rd Party Shopping Cart Integration (You do not need to use these settings when using with the <a href="http://www.tipsandtricks-hq.com/?p=1059" target="_blank">WP eStore</a> plugin)</label></h3>
	<div class="inside">
	
	<br />
	<strong><i>(Only use this section if you have been instructed to do so from one of the <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/" target="_blank">documentation pages</a>)</i></strong>
	<br /><br />
	
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">		
    <tr valign="top"><td width="25%" align="left">
    <strong>Enable 3rd Party Cart Integration:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_enable_3rd_party" value="1" <?php echo $wp_aff_enable_3rd_party; ?> />
    <br /><i>Check this box if you want to use this plugin with a 3rd Party Shopping Cart plugin.</i><br />
    </td></tr>
    <tr valign="top"><td width="25%" align="left"> 
    <strong>Sandbox Mode:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_sandbox_mode" value="1" <?php echo $wp_aff_sandbox_mode; ?> />
    <br /><i>Check this box if you want to test a transaction in Sandbox mode.</i><br />
    </td></tr>
    <tr valign="top"><td width="25%" align="left">
    <strong>PayPal PDT Identity Token:</strong>
    </td><td align="left">
    <input name="wp_aff_pdt_identity_token" type="text" size="100" value="<?php echo get_option('wp_aff_pdt_identity_token'); ?>"/>
    <br /><i>Specify your identity token in the text field above. If you need help finding your token then <a href="http://www.tipsandtricks-hq.com/forum/topic/how-do-i-setup-paypal-pdt-and-get-my-paypal-pdt-token-id" target="_blank">click here</a>.</i><br />
    </td></tr>          
    </table>
    </div></div>
		    
    <div class="postbox">
    <h3><label for="title">Testing and Debugging Settings</label></h3>
    <div class="inside">    	    
        <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">    
        <tr valign="top"><td width="25%" align="left">
            <strong>Enable Debug:</strong>
            </td><td align="left">
            <input name="wp_aff_enable_debug" type="checkbox"  <?php $wp_aff_enable_debug = get_option('wp_aff_enable_debug');echo ($wp_aff_enable_debug)?'checked="checked"':''?> value="1"/><br />
            <i>If checked, debug output will be written to log file. This can come in handy when troubleshooting.</i><br /><br />
                You can check the debug log file by clicking on the link below (The log files can be viewed using any text editor):
            <li style="margin-left:15px;"><a href="<?php echo WP_AFF_PLATFORM_URL."/wp_affiliate_debug.txt"; ?>" target="_blank">wp_affiliate_debug.txt file</a></li>		    	
            <li style="margin-left:15px;"><a href="<?php echo WP_AFF_PLATFORM_URL."/api/ipn_handle_debug.txt"; ?>" target="_blank">ipn_handle_debug.txt file</a> (for direct PayPal button integration)</li>
            <div class="submit">
            <input type="submit" name="wp_aff_reset_log_file" style="font-weight:bold; color:red" value="Reset Debug Log File" class="button" /> 
            <p class="description">The above debug log files will be "reset" and timestamped with a log file reset message.</p>
            </div>
        </td>
        </tr>            
        </table>
    </div></div>       
        
    <div class="submit">
        <input type="submit" class="button-primary" name="info_update" value="<?php _e('Update options'); ?> &raquo;" />
    </div>
    
    </form>

    <?php
}

function wp_aff_reset_log_files()
{
    $log_reset = true;
    $logfile_list = array (
        WP_AFF_PLATFORM_PATH."/wp_affiliate_debug.txt",
        WP_AFF_PLATFORM_PATH."/api/ipn_handle_debug.txt",
    );

    foreach($logfile_list as $logfile)
    {
        if(empty($logfile)){continue;}

        $text = '['.date('m/d/Y g:i A').'] - SUCCESS : Log file reset';
        $text .= "\n------------------------------------------------------------------\n\n";
        $fp = fopen($logfile, 'w');
        if($fp != FALSE) {         
            @fwrite($fp, $text);
            @fclose($fp);
        }
        else{
            $log_reset = false;	
        }
    }
    return $log_reset;
}