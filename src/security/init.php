<?php

// detect not yet installed WordPress
if(defined('WP_INSTALLING') && WP_INSTALLING){
	return;
}

// detect direct access
if (!defined('ABSPATH')) {
	exit('no thanks');
}
