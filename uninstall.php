<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

// Delete options
delete_option( 'salesfeed_account_id' );
