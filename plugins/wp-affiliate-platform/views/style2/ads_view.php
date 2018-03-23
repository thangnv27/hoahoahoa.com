<?php

function wp_aff_ads_view() {
    $output = "";
    $output .= wp_aff_view_2_get_navbar();
    $output .= '<div id="wp_aff_inside">';
    $output .= wp_aff_show_banners();
    $output .= '</div>';
    $output .= wp_aff_view_2_get_footer();
    return $output;
}

function wp_aff_show_banners() {
    ?>
    <script type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL . '/views/js/jquery.lightbox-0.5.min.js'; ?>"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(function() {
                $('[id=wp_aff_inside]').find('a[rel*=lightbox]').lightBox({
                    imageLoading: '<?php echo WP_AFF_PLATFORM_URL . '/affiliates/images/lightbox-ico-loading.gif'; ?>',
                    imageBtnClose: '<?php echo WP_AFF_PLATFORM_URL . '/affiliates/images/lightbox-btn-close.gif'; ?>',
                    imageBtnPrev: '<?php echo WP_AFF_PLATFORM_URL . '/affiliates/images/lightbox-btn-prev.gif'; ?>',
                    imageBtnNext: '<?php echo WP_AFF_PLATFORM_URL . '/affiliates/images/lightbox-btn-next.gif'; ?>',
                    imageBlank: '<?php echo WP_AFF_PLATFORM_URL . '/affiliates/images/lightbox-blank.gif'; ?>'
                });
            });
        });
    </script>
    <?php
    global $wpdb;
    $aff_banners_table = WP_AFF_BANNERS_TBL_NAME;
    $resultset = $wpdb->get_results("select * from $aff_banners_table ORDER BY name asc", OBJECT);

    $output = "";

    $output .= "<h3 class='wp_aff_title'>" . AFF_B_BANNER_PAGE_TITLE . "</h3>";
    $output .= "<p>" . AFF_B_BANNERS_PAGE_MESSAGE . "</p>";
    $output .= '<div class="alert alert-info"><strong>' . AFF_B_BANNERS . '</strong></div>';

    if ($resultset) {
        $output .= '
		<table class="table" width="100%" id="gallery">
		<thead><tr>
		<th scope="col" class="tableheader">' . AFF_B_BANNER_NAME . '</th>
		<th scope="col" class="tableheader">' . AFF_B_BANNER_LINK . '</th>
		<th scope="col" class="tableheader">' . AFF_B_CODE . '</th>
		</tr></thead>
		<tbody>';

        foreach ($resultset as $resultset) {
            if ($resultset->creative_type == "0") {
                $separator = '?';
                $url = $resultset->ref_url;
                if (strpos($url, '?') !== false) {
                    $separator = '&';
                }
                $rel_tag = "";
                if (WP_AFFILIATE_ENABLE_NOFOLLOW_IN_AFFILIATE_ADS === '1') {
                    $rel_tag = 'rel="nofollow"';
                }
                if (empty($resultset->image)) {// Text Link
                    $aff_url = $resultset->ref_url . $separator . "ap_id=" . $_SESSION['user_id'];
                    $code = "<a href=\"$aff_url\" target=\"blank\" $rel_tag>$resultset->link_text</a>";
                    $banner = "<a href=\"$aff_url\" target=\"blank\">$resultset->link_text</a>";
                } else {//Banner image
                    $aff_url = $resultset->ref_url . $separator . "ap_id=" . $_SESSION['user_id'];
                    $code = "<a href=\"$aff_url\" target=\"_blank\" $rel_tag><img src=\"$resultset->image\" alt=\"$resultset->link_text\" border=\"0\" /></a>";
                    $banner = "<div id=\"lightbox\"><a rel=\"lightbox\" href=\"$resultset->image\" ><img src=\"$resultset->image\" alt=\"$resultset->link_text\" border=\"0\" /></a></div>";
                }
                $output .= '<tr>';
                $output .= '<td class="col1"><strong>' . $resultset->name . '</strong></td>';
                $output .= '<td class="col2">' . $banner . '</td>';
                $output .= '<td class="col3"><textarea rows="5">';
                $output .= $code;
                $output .= "</textarea></td>";
                $output .= '</tr>';
            }
        }
        $output .= '</tbody>
		</table>';
    } else {
        $output .= "<div class='alert alert-warning'>" . AFF_B_NO_BANNER . "</div>";
    }
    return $output;
}
