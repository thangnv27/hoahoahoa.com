<?php

function wp_aff_integration_settings_menu_page()
{
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    if (isset($_POST['update_integration_settings']))
    {
        update_option('wp_aff_enable_remote_post', ($_POST['wp_aff_enable_remote_post']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_remote_click_post_url', trim($_POST["wp_aff_remote_click_post_url"])); 
        update_option('wp_aff_comm_post_url', trim($_POST["wp_aff_comm_post_url"]));
        $wp_aff_platform_config->setValue('wp_aff_lead_capture_post_url', trim($_POST["wp_aff_lead_capture_post_url"]));
        update_option('wp_aff_secret_word_for_post', trim($_POST["wp_aff_secret_word_for_post"]));
        
        $wp_aff_platform_config->setValue('wp_aff_enable_wpcf7_lead_capture', ($_POST['wp_aff_enable_wpcf7_lead_capture']=='1') ? '1':'' ); 
        $wp_aff_platform_config->setValue('wp_aff_wp_cf7_form_exclusion_list', trim($_POST["wp_aff_wp_cf7_form_exclusion_list"]));
        
        $wp_aff_platform_config->setValue('wp_aff_enable_gf_paypal', ($_POST['wp_aff_enable_gf_paypal']=='1') ? '1':'' );
        
        $wp_aff_platform_config->setValue('wp_aff_enable_gumroad', ($_POST['wp_aff_enable_gumroad']=='1') ? '1':'' );    	

        $wp_aff_platform_config->saveConfig();
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Options Updated!';
        echo '</strong></p></div>';
    }
    
    //Default values
    if (get_option('wp_aff_enable_remote_post'))
        $wp_aff_enable_remote_post = 'checked="checked"';
    else
        $wp_aff_enable_remote_post = '';
    
    $wp_aff_remote_click_post_url = get_option('wp_aff_remote_click_post_url');
    if(empty($wp_aff_remote_click_post_url))
    {
        $wp_aff_remote_click_post_url = WP_AFF_PLATFORM_URL.'/api/remote-click-track.php';
    }
    $wp_aff_comm_post_url = get_option('wp_aff_comm_post_url');
    if(empty($wp_aff_comm_post_url))
    {
        $wp_aff_comm_post_url = WP_AFF_PLATFORM_URL.'/api/post.php';
    }
    $wp_aff_lead_capture_post_url = $wp_aff_platform_config->getValue('wp_aff_lead_capture_post_url');
    if(empty($wp_aff_lead_capture_post_url)){
    	$wp_aff_lead_capture_post_url = WP_AFF_PLATFORM_URL.'/api/remote-lead-capture.php';
    }
    $wp_aff_secret_word_for_post = get_option('wp_aff_secret_word_for_post');
    if(empty($wp_aff_secret_word_for_post))
    {
        $wp_aff_secret_word_for_post = uniqid();
    }    
    
    
    ?>

    <p class="wp_affiliate_grey_box">
    These are optional settings that can be useful for some 3rd party integration setup.
    </p>

    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">


    <a name="remote-post"></a>
    <div class="postbox">
    <h3><label for="title">Additional Integration Options (Remote POST)</label></h3>
    <div class="inside">

    <br />
    <strong><i>(Only use this section if you have been instructed to do so from one of the <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/" target="_blank">documentation pages</a>)</i></strong>
    <br /><br />

    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">    
    <tr valign="top"><td width="25%" align="left">
    <strong>Enable Remote POST:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_enable_remote_post" value="1" <?php echo $wp_aff_enable_remote_post; ?> />
    <br /><i>Check this box if you want to be able to award commission by sending a HTTP POST request to a URL or remotely track clicks.</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>POST URL for Remote Click Tracking:</strong>
    </td><td align="left">
    <input name="wp_aff_remote_click_post_url" type="text" size="100" value="<?php echo $wp_aff_remote_click_post_url; ?>"/>
    <br /><i>This is the URL where you will need to POST your request to track clicks remotely.</i><br />
    </td></tr>  

    <tr valign="top"><td width="25%" align="left">
    <strong>POST URL for Sale/Commission Awarding:</strong>
    </td><td align="left">
    <input name="wp_aff_comm_post_url" type="text" size="100" value="<?php echo $wp_aff_comm_post_url; ?>"/>
    <br /><i>This is the URL where you will need to POST your request to award commission/sale tracking.</i><br />
    </td></tr>     

    <tr valign="top"><td width="25%" align="left">
    <strong>POST URL for Lead Capture:</strong>
    </td><td align="left">
    <input name="wp_aff_lead_capture_post_url" type="text" size="100" value="<?php echo $wp_aff_lead_capture_post_url; ?>"/>
    <br /><i>This is the URL where you will need to POST your request to capture a lead from your script.</i><br />
    </td></tr> 

    <tr valign="top"><td width="25%" align="left">
    <strong>Secret Word:</strong>
    </td><td align="left">
    <input name="wp_aff_secret_word_for_post" type="text" size="30" value="<?php echo $wp_aff_secret_word_for_post; ?>"/>
    <br /><i>This secret word will be used to verify any request sent to the POST URL. You can change this code to something random.</i><br />
    </td></tr>  
    </table>
    </div></div>

    <a name="lead-capture"></a>
    <div class="postbox">
    <h3><label for="title">Lead Capture Related Settings</label></h3>
    <div class="inside">    	    
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">  

    <tr valign="top"><td width="25%" align="left">
    <strong>Enable Contact Form 7 Lead Capture:</strong>
    </td><td align="left">
    <input name="wp_aff_enable_wpcf7_lead_capture" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_enable_wpcf7_lead_capture')=='1'){echo 'checked="checked"';} ?> value="1"/><br />		    
    <i>Check this option if you want to capture leads with the Contact Form 7 plugin. <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=215" target="_blank">Read More Here</a></i>
    <br />
    </td>
    </tr> 

    <tr valign="top"><td width="25%" align="left">
    <strong>Lead Capture Form Exclusion List (optional)</strong>
    </td><td align="left">
    <input name="wp_aff_wp_cf7_form_exclusion_list" type="text" size="100" value="<?php echo $wp_aff_platform_config->getValue('wp_aff_wp_cf7_form_exclusion_list'); ?>"/>
    <br /><i>If you have multiple contact forms and you want to exclude a contact form (example, your general contact form) from the lead capture pool then specify the ID of that form (example, 2500) in the above field. You can add multiple form IDs separated by comma.</i><br />
    </td></tr>   

    </table>
    </div></div>  
        
    <a name="gravity-forms"></a>    
    <div class="postbox">
    <h3><label for="title">Gravity Forms Integration Settings</label></h3>
    <div class="inside">    	    
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">  

    <tr valign="top"><td width="25%" align="left">
    <strong>Enable Gravity Forms PayPal Tracking:</strong>
    </td><td align="left">
    <input name="wp_aff_enable_gf_paypal" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_enable_gf_paypal')=='1'){echo 'checked="checked"';} ?> value="1"/><br />		    
    <i>Check this option if you want to track and award commission for customers who make a purchase via the Gravity Forms PayPal addon on your site. <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=586" target="_blank">Read More Here</a></i>
    <br />
    </td>
    </tr> 

    <tr valign="top"><td width="25%" align="left">
    <strong>Gravity Forms Lead Capture:</strong>
    </td><td align="left">
    <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=385" target="_blank">How to capture lead with Gravity Forms</a>
    </td>
    </tr> 

    </table>
    </div></div> 
        
    <a name="gumroad"></a>
    <div class="postbox">
    <h3><label for="title">Gumroad Integration</label></h3>
    <div class="inside">

    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    Enable Gumroad Integration
    </td><td align="left">
    <input name="wp_aff_enable_gumroad" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_enable_gumroad')=='1'){echo 'checked="checked"';} ?> value="1"/>    
    <p class="description">Enable this if you want to integrate the plugin with Gumroad.</p>
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    Gumroad Ping URL
    </td><td align="left">
    <?php 
    echo WP_AFF_PLATFORM_SITE_HOME_URL.'/?ap_aff_gumroad_ping=1';
    ?>
    <p class="description">This is the Gumraod ping URL for your site.</p>
    </td></tr>
    
    </table>
    </div></div>

    <div class="submit">
    <input type="submit" class="button-primary" name="update_integration_settings" value="<?php _e('Update options'); ?> &raquo;" />
    </div>

    </form>    
    <?php	
    
}