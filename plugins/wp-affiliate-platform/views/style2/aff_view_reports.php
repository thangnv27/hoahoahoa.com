<?php
$calendar_icon_url = WP_AFF_PLATFORM_URL . '/affiliates/images/calendar.gif';
?>

<script type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL . '/affiliates/lib/date/date.js'; ?>"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL . '/affiliates/lib/date/jquery.bgiframe.min.js'; ?>"></script><![endif]-->
<script type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL . '/affiliates/lib/date/jquery.datePicker-v2.js'; ?>"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo WP_AFF_PLATFORM_URL . '/affiliates/lib/date/datePicker.css'; ?>">
<script type="text/javascript">
    /* <![CDATA[ */
    jQuery(document).ready(function($) {
        $(function() {
            $('.date-pick').datePicker({startDate: "2008-01-01"});
            $('.dp-choose-date').html('<span class="wpap-date-calendar-icon"><img src="<?php echo $calendar_icon_url; ?>" alt="<?php echo AFF_CHOOSE_DATE; ?>" /></span>');//Replace the default "choose date" text with icon                    
        });
    });
    /*]]>*/
</script>

<div class="panel panel-default">
    <div class="panel-heading"><?php echo AFF_SELECT_DATE_RANGE; ?></div>

    <div class="panel-body">	

        <form id="dateform" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
            <input type="hidden" name="info_update" id="info_update" value="true" />

            <div class="row wpap-buffer-10">
                <div id="startdate" class="input-group wpap-col-centered">
                    <input type="text" id="start_date" name="start_date" class="date-pick" placeholder="<?php echo AFF_START_DATE_TEXT; ?>">
                </div>
            </div>

            <div class="row wpap-buffer-10">
                <div id="enddate" class="input-group wpap-col-centered">
                    <input type="text" id="end_date" name="end_date" class="date-pick" placeholder="<?php echo AFF_END_DATE_TEXT; ?>">
                </div>
            </div>

            <div class="row wpap-buffer-10 text-center">
                <button name="info_update" class="btn btn-default btn-lg wpap-buffer-5" type="submit" value="">
                    <?php echo AFF_DISPLAY_DATA_BUTTON_TEXT; ?>
                </button>
            </div>

        </form>

    </div>
</div>
