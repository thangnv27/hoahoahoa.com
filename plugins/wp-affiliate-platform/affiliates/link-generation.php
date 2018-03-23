<?php

include_once ('misc_func.php');
if (!isset($_SESSION)) {
    @session_start();
}

if (!aff_check_security()) {
    aff_redirect('index.php');
    exit;
}

include "header.php";

echo '<div id="subnav"><li><a href="ads.php">' . AFF_NAV_BANNERS . '</a></li></div>';
echo '<div id="subnav"><li><a href="creatives.php">' . AFF_NAV_CREATIVES . '</a></li></div>';
echo '<div id="subnav"><li><a href="link-generation.php">' . AFF_NAV_LINK_GENERATION . '</a></li></div>';
echo '<div style="clear:both;"></div><br />';

echo "<h3 class='title'>" . AFF_B_LINK_GENERATION_PAGE_TITLE . "</h3>";
echo "<p style='text-align:center;'>" . AFF_B_LINK_GENERATION_PAGE_MESSAGE . "</p>";

$default_url = get_option('wp_aff_default_affiliate_landing_url');
if (empty($default_url)) {
    $default_url = home_url();
}
if (isset($_REQUEST['wp_aff_link_generation_url'])) {
    $default_url = strip_tags($_REQUEST['wp_aff_link_generation_url']);
}

$output .= '<form id="wp_aff_link_generation_form" action="" method="post">';
$output .= '<div class="wp_aff_link_gen_page_url_label">'.AFF_B_LINK_GENERATION_PAGE_URL.'</div>';
$output .= '<div class="wp_aff_link_generation_input"><input type="text" name="wp_aff_link_generation_url" value="' . $default_url . '" size="60" /></div>';

if (isset($_REQUEST['wp_aff_generate_referral_link'])) {
    $aff_id = $_SESSION['user_id'];
    $referral_url = add_query_arg('ap_id', $aff_id, $default_url);
    $output .= '<br />';
    $output .= '<div class="wp_aff_referral_url_label">' . AFF_B_LINK_GENERATION_HELP_TEXT . '</div>';
    $output .= '<div class="wp_aff_referral_url_input"><input type="text" name="wp_aff_referral_url_input" value="' . $referral_url . '" size="60" /></div>';
    $output .= '<br />';
}

$output .= '<div class="wp_aff_link_generation_submit"><input type="submit" class="button" name="wp_aff_generate_referral_link" value="' . AFF_B_LINK_GENERATION_BUTTON_TEXT . '" /></div>';
$output .= '</form>';

echo $output;

include "footer.php";