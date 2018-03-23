<?php

function wp_aff_details_view() {
    $output = "";
    $output .= wp_aff_view_2_get_navbar();
    $output .= '<div id="wp_aff_inside">';
    $output .= wp_aff_show_edit_details();
    $output .= '</div>';
    $output .= wp_aff_view_2_get_footer();
    return $output;
}

function wp_aff_show_edit_details() {
    include_once(WP_AFF_PLATFORM_PATH . 'views/countries.php');
    $output = '<div class="wpap-vertical-buffer-10"></div>';

    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $errorMsg = '';

    if (isset($_POST['commited']) && $_POST['commited'] == 'yes') {
        if (!isset($_SESSION['user_id'])) {//Check if user is logged in
            die("User is not logged in as an affiliate. Profile update request denied.");
        }
        //Field validation
        if ($_POST['clientemail'] == '') {
            $errorMsg .= AFF_REQUIRED . ": " . AFF_EMAIL . '<br>';
        }
        if ($wp_aff_platform_config->getValue('wp_aff_make_paypal_email_required') == '1') {
            if ($_POST['clientpaypalemail'] == '') {
                $errorMsg .= AFF_REQUIRED . ": " . AFF_PAYPAL_EMAIL . '<br>';
            }
        }

        if ($errorMsg == '') {
            if (!empty($_POST['password'])) {
                $password = $_POST['password'];
                include_once(ABSPATH . WPINC . '/class-phpass.php');
                $wp_hasher = new PasswordHash(8, TRUE);
                $password = $wp_hasher->HashPassword($password);
            } else {
                $password = $_POST['encrypted-pass'];
            }
            $payableto = ""; //$_POST['clientpayableto']    	
            $updatedb = "UPDATE $affiliates_table_name SET pass = '" . $password . "', company = '" . $_POST['clientcompany'] . "', payableto = '" . $payableto . "', title = '" . $_POST['clienttitle'] . "', firstname = '" . $_POST['clientfirstname'] . "', lastname = '" . $_POST['clientlastname'] . "', email = '" . $_POST['clientemail'] . "', street = '" . $_POST['clientstreet'] . "', town = '" . $_POST['clienttown'] . "', state = '" . $_POST['clientstate'] . "', country = '" . $_POST['clientcountry'] . "', postcode = '" . $_POST['clientpostcode'] . "', website = '" . $_POST['webpage'] . "', phone = '" . $_POST['clientphone'] . "', fax = '" . $_POST['clientfax'] . "', paypalemail = '" . $_POST['clientpaypalemail'] . "', tax_id = '" . $_POST['tax_id'] . "', account_details = '" . $_POST['account_details'] . "' WHERE refid = '" . $_SESSION['user_id'] . "'";
            $results = $wpdb->query($updatedb);

            do_action('wp_aff_profile_update', $_SESSION['user_id'], $_POST);

            $output .= "<div class='alert alert-success'>" . AFF_D_CHANGED . "</div>";
        }
    }

    $editingaff = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '" . $_SESSION['user_id'] . "'", OBJECT);

    if ($errorMsg != '')
        $output .= "<div class='alert alert-danger'>$errorMsg</div>";

    if ($editingaff) {
        ob_start();
        ?>	

        <form action="" method="post" ENCTYPE=multipart/form-data>
            <table class="table">
                <tr>
                    <td><?php echo AFF_AFFILIATE_ID; ?>:</td>
                    <td><?php echo $_SESSION['user_id']; ?></td>
                </tr>

                <tr>
                    <td><?php echo AFF_PASSWORD; ?>:</td>
                    <td>
                        <input class="user-edit" type=password name=password value="">
                        <input name="encrypted-pass" type="hidden" value="<?php echo $editingaff->pass; ?>" size="20" />
                        <div class="wpap_help_text"><?php echo AFF_LEAVE_EMPTY_TO_KEEP_PASSWORD; ?></div>
                    </td>                
                </tr>

                <tr>
                    <td><?php echo AFF_COMPANY; ?>: </td>
                    <td><input class="user-edit" type=text name=clientcompany value="<?php echo $editingaff->company; ?>"></td>
                </tr>

                <tr>
                    <td><?php echo AFF_TITLE; ?>: </td>
                    <td>
                        <select class="user-select" name=clienttitle>
                            <option value=Mr <?php if ($editingaff->title == "Mr") echo 'selected="selected"'; ?>><?php echo AFF_MR; ?></option>
                            <option value=Mrs <?php if ($editingaff->title == "Mrs") echo 'selected="selected"'; ?>><?php echo AFF_MRS; ?></option>
                            <option value=Miss <?php if ($editingaff->title == "Miss") echo 'selected="selected"'; ?>><?php echo AFF_MISS; ?></option>
                            <option value=Ms <?php if ($editingaff->title == "Ms") echo 'selected="selected"'; ?>><?php echo AFF_MS; ?></option>
                            <option value=Dr <?php if ($editingaff->title == "Dr") echo 'selected="selected"'; ?>><?php echo AFF_DR; ?></option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_FIRST_NAME; ?>: </td>
                    <td>                
                        <input class="user-edit" type=text name=clientfirstname value="<?php echo $editingaff->firstname; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_LAST_NAME; ?>: </td>
                    <td>                 
                        <input class="user-edit" type=text name=clientlastname value="<?php echo $editingaff->lastname; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_EMAIL; ?>: </td>
                    <td>                  
                        <input class="user-edit" type=text name=clientemail value="<?php echo $editingaff->email; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_ADDRESS; ?>: </td>
                    <td>                
                        <input class="user-edit" type=text name=clientstreet value="<?php echo $editingaff->street; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_TOWN; ?>: </td>
                    <td>                
                        <input class="user-edit" type=text name=clienttown value="<?php echo $editingaff->town; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_STATE; ?>: </td>
                    <td>                
                        <input class="user-edit" type=text name=clientstate value="<?php echo $editingaff->state; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_COUNTRY; ?>: </td>
                    <td>                 
                        <select class="user-select" name=clientcountry class=dropdown>
                            <?php
                            foreach ($GLOBALS['countries'] as $key => $country)
                                print '<option value="' . $key . '" ' . ($editingaff->country == $key ? 'selected' : '') . '>' . $country . '</option>' . "\n";
                            ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_ZIP; ?>: </td>
                    <td>                  
                        <input class="user-edit" type=text name=clientpostcode value="<?php echo $editingaff->postcode; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_WEBSITE; ?>: </td>
                    <td>                  
                        <input class="user-edit" type=text name=webpage value="<?php echo $editingaff->website; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_PHONE; ?>: </td>
                    <td>                  
                        <input class="user-edit" type=text name=clientphone value="<?php echo $editingaff->phone; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_FAX; ?>: </td>
                    <td>                  
                        <input class="user-edit" type=text name=clientfax value="<?php echo $editingaff->fax; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_PAYPAL_EMAIL; ?>: </td>
                    <td>                  
                        <input class="user-edit" type=text name=clientpaypalemail value="<?php echo $editingaff->paypalemail; ?>">
                    </td>
                </tr>

                <tr>
                    <td><?php echo AFF_BANK_ACCOUNT_DETAILS; ?>: </td>
                    <td>                  
                        <textarea class="user-edit" name="account_details" cols="23" rows="2"><?php echo $editingaff->account_details; ?></textarea>	           
                    </td>
                </tr>

                <?php
                if($wp_aff_platform_config->getValue('wp_aff_hide_tax_id_field')!='1')
                {
                ?>                
                <tr>
                    <td><?php echo AFF_TAX_ID; ?>: </td>
                    <td>                  
                        <input class="user-edit" type=text name=tax_id value="<?php echo $editingaff->tax_id; ?>">
                    </td>
                </tr>
                <?php } ?>
                
                <tr>
                    <td colspan="2">
                        <input type=hidden name=commited value=yes>
                        <div class="row text-center">
                            <button name="Submit" class="btn btn-default btn-lg wpap-buffer-5" type="submit" value="">
                                <span class="glyphicon glyphicon-hand-right"></span> <?php echo AFF_UPDATE_BUTTON_TEXT; ?>
                            </button>
                        </div>
                    </td>
                </tr>                

            </table>
        </form>

        <?php
        $output .= ob_get_contents();
        ob_end_clean();
    }
    return $output;
}
