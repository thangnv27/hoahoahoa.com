<?php

function wp_aff_link_generation_view() {
    $output .= wp_aff_view_get_navbar();
    $output .= '<div id="wp_aff_inside">';
    $output .= wp_aff_show_link_generation_tool();
    $output .= '</div>';
    $output .= wp_aff_view_get_footer();
    return $output;
}

function wp_aff_show_link_generation_tool() {
    $output .= '<div id="subnav">';
    $output .= '<li><a href="' . wp_aff_view_get_url_with_separator("ads") . '">' . AFF_NAV_BANNERS . '</a></li>';
    $output .= '<li><a href="' . wp_aff_view_get_url_with_separator("creatives") . '">' . AFF_NAV_CREATIVES . '</a></li>';
    $output .= '<li><a href="' . wp_aff_view_get_url_with_separator("link_generation") . '">' . AFF_NAV_LINK_GENERATION . '</a></li>';
    $output .= '<div style="clear:both;"></div>';
    $output .= '</div><br />';

    $output .= "<h3 class='wp_aff_title'>" . AFF_B_LINK_GENERATION_PAGE_TITLE . "</h3>";
    $output .= "<p style='text-align:left;'>" . AFF_B_LINK_GENERATION_PAGE_MESSAGE . "</p>";

    $default_url = get_option('wp_aff_default_affiliate_landing_url');
    if (empty($default_url)) {
        $default_url = home_url();
    }
    if(isset($_REQUEST['wp_aff_link_generation_url'])){
        $default_url = strip_tags($_REQUEST['wp_aff_link_generation_url']);
    }

    $output .= '<form id="wp_aff_link_generation_form" action="" method="post">';
    $output .= '<div class="wp_aff_link_gen_page_url_label">'.AFF_B_LINK_GENERATION_PAGE_URL.'</div>';
    $output .= '<div class="wp_aff_link_generation_input"><input type="text" name="wp_aff_link_generation_url" value="'.$default_url.'" size="60" /></div>';
    
    if(isset($_REQUEST['wp_aff_generate_referral_link'])){
        $aff_id = $_SESSION['user_id'];
        $referral_url = add_query_arg( 'ap_id', $aff_id, $default_url );
        $output .= '<br />';
        $output .= '<div class="wp_aff_referral_url_label">'.AFF_B_LINK_GENERATION_HELP_TEXT.'</div>';
        $output .= '<div class="wp_aff_referral_url_input"><input type="text" name="wp_aff_referral_url_input" value="'.$referral_url.'" size="60" /></div>';
        $output .= '<br />';
    }
    
    $output .= '<div class="wp_aff_link_generation_submit"><input type="submit" name="wp_aff_generate_referral_link" value="'.AFF_B_LINK_GENERATION_BUTTON_TEXT.'" /></div>';
    $output .= '</form>';

    return $output;
}
