<?php
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="no-comments"><?php _e('This post is password protected. Enter the password to view comments.', 'Fortuna'); ?></p>
	<?php
		return;
	}
?>
	
<?php if ( have_comments() ) : ?>

	<div class="comment_list" id="comments">
		<h4><?php comments_number(__('No Comments', 'Fortuna'), __('One Comment', 'Fortuna'), __('% Comments', 'Fortuna'));?></h4>

		<!-- Comment list -->
		<ol>
			<?php wp_list_comments('type=comment&callback=boc_comment'); ?>
		</ol>
		<!-- Comment list::END -->
		
		<div class="section">
		    <div style="float: left;"><?php previous_comments_link(); ?></div>
		    <div style="float: right;"><?php next_comments_link(); ?></div>
		</div>
	</div>

<?php else : // this is displayed if there are no comments so far ?>

	<?php if ( comments_open() ) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="no-comments"><?php _e('Comments are closed.', 'Fortuna'); ?></p>

	<?php endif; ?>

<?php endif; ?>

<?php if ( comments_open() ) : ?>

				
	<?php

$args = array(
  'id_form'           => 'commentform',
  'id_submit'         => 'submit',
  'title_reply'       => '<span>'.__('Leave A Comment', 'Fortuna').'</span>',
  'label_submit'      => __('Comment', 'Fortuna'),

  'comment_field' =>  '<div id="comment-textarea">
					<p>		
						<label for="comment">'.__('Comment', 'Fortuna').'<span class="required">*</span></label>
						<textarea id="comment" rows="8" class="aqua_input" name="comment"></textarea>
					</p>
				</div>',
	
  'must_log_in' => '<p>You must be <a href="'.esc_url(wp_login_url( get_permalink() )).'">logged in</a> to post a comment.</p>',

  'logged_in_as' => '<p>'.__('Logged in as', 'Fortuna').' <a href="'.esc_url(get_option('siteurl')).'/wp-admin/profile.php">'.$user_identity.'</a>. <a href="'.wp_logout_url(get_permalink()).'" title="Log out of this account">'.__('Log out &raquo;', 'Fortuna').'</a></p>',

  'comment_notes_before' => '',  
  'comment_notes_after' => '',

  'fields' => apply_filters( 'comment_form_default_fields', array(

    'author' =>
      '<p>
			<label for="author">'.__('Name', 'Fortuna').'<span class="required">*</span></label>
			<input id="author" class="aqua_input" name="author" type="text" value=""/>
		</p>',

    'email' =>
      '<p>	
			<label for="email">'.__('Email', 'Fortuna').'<span class="required">*</span></label> 
			<input id="email" class="aqua_input" name="email" type="email" value=""/>
		</p>',

    'url' =>
      '<p>		
			<label for="url">'.__('Website', 'Fortuna').'</label>
			<input id="url" class="aqua_input" name="url" type="text" value="" size="30"/>
		</p>'
    )
  ),
);	
		?>		
				

				
		<!-- Comment Section -->	

		<?php comment_form($args); ?>
					
		<!-- Comment Section::END -->


<?php endif; ?>