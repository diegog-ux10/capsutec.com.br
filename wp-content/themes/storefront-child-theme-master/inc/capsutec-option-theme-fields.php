<?php

/**
 * Option Theme Fields
 */

if ( function_exists( 'acf_add_options_page' ) ) 
{
    acf_add_options_page( array(
		'page_title' => 'Header',
		'menu_title' => 'Editar Header',
		'menu_slug'  => 'header',
		'capability' => 'manage_options',
		'post_id'    => 'header',
		'position'   => 3,
		'redirect'   => false
	) );
}