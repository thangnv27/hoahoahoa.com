<?php

function wpap_show_date_form_fields_new()
{    
    isset($_POST['start_date'])? $start_date = $_POST['start_date'] : $start_date = '';
    isset($_POST['end_date'])? $end_date = $_POST['end_date'] : $end_date = '';
    ?>
    <br />
    <strong>Select a date range (yyyy-mm-dd) and hit the Display Data button to view history</strong>
    		
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />
           
    <br /><br />
    <strong>Start Date:  </strong>
    <input type="text" class="wpap_date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" size="12">

    <strong>End Date: </strong>
    <input type="text" class="wpap_date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" size="12">
    <br />	
    	
    <div class="submit">
        <input type="submit" name="info_update" class="button" value="Display Data &raquo;" />
    </div>
    
    </form>
    <?php
}

function wpap_show_date_form_with_affiliate_id_field_new()
{
    ?>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <br />
    <strong>1. Enter the Affiliate ID</strong>
    <input name="wp_aff_referrer" type="text" size="30" value="<?php echo $_POST['wp_aff_referrer']; ?>" />
    
    <br /><br />
    <strong>2. Select a date range (yyyy-mm-dd) and hit the Display Data button</strong>
    		    
    <input type="hidden" name="info_update" id="info_update" value="true" />
           
    <br /><br />
    <strong>Start Date:  </strong>
    <input type="text" class="wpap_date" id="start_date" name="start_date" size="12">
	
    <strong>End Date: </strong>
    <input type="text" class="wpap_date" id="end_date" name="end_date" size="12">
    <br />	
    	
    <div class="submit">
        <input type="submit" name="info_update" class="button" value="Display Data &raquo;" />
    </div>
    
    </form>
    <?php	
}
