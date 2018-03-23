<?php

function wp_aff_link_generation_view() {
    $output .= wp_aff_view_2_get_navbar();
    $output .= '<div id="wp_aff_inside">';
    $output .= wp_aff_show_link_generation_tool();
    $output .= '</div>';
    $output .= wp_aff_view_2_get_footer();
    return $output;
}

function wp_aff_show_link_generation_tool() {
    $output = '';

    $output .= '<h3 class="wp_aff_title">' . AFF_B_LINK_GENERATION_PAGE_TITLE . '</h3>';
    $output .= '<div class="alert alert-info">' . AFF_B_LINK_GENERATION_PAGE_MESSAGE . '</div>';

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
        $output .= '<div class="row wpap-vertical-buffer-10"></div>';
        $output .= '<div class="wp_aff_referral_url_label alert alert-warning">'.AFF_B_LINK_GENERATION_HELP_TEXT.'</div>';
        $output .= '<div class="wp_aff_referral_url_input"><input type="text" name="wp_aff_referral_url_input" value="'.$referral_url.'" size="60" /></div>';        
    }
    
    $output .= '<div class="row wpap-vertical-buffer-10"></div>';
    $output .= '<div class="wp_aff_link_generation_submit">';
    $output .= '<button type="submit" name="wp_aff_generate_referral_link" class="btn btn-default btn-lg">'.AFF_B_LINK_GENERATION_BUTTON_TEXT.'</button>';
    $output .= '</div>';
    
    $output .= '</form>';

    return $output;
}
