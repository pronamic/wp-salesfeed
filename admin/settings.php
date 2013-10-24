<div class="wrap">
	<?php screen_icon(); ?>

	<h2><?php echo get_admin_page_title(); ?></h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'salesfeed' ); ?>

		<?php do_settings_sections( 'salesfeed' ); ?>

		<?php submit_button(); ?>
	</form>
</div>