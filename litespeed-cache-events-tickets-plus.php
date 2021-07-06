<?php
/*
	Plugin Name: LiteSpeed Cache Events Tickets Plus
	Description: Allow event page caching by turning Event Tickets into ESI blocks
	Version: 1.0
	Author: Abundant Designs LLC
	Author URI: https://www.abundantdesigns.com
	License: GPLv2 or later
	Text Domain: litespeed-cache-events-tickets-plus
 */
 
add_action( 'litespeed_tpl_normal', function() {

	if ( apply_filters( 'litespeed_esi_status', false ) && method_exists( "Tribe__Tickets_Plus__Commerce__WooCommerce__Main", "get_instance" ) ) {

		$instance = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
		$ticket_form_hook = $instance->get_ticket_form_hook();
		
		if ( ! empty( $ticket_form_hook ) ) {
			// Remove ticket form added from Tribe__Tickets__Tickets::hook() (/event-tickets/src/Tribe/tickets.php)
			remove_action( $ticket_form_hook, [ $instance, 'maybe_add_front_end_tickets_form' ], 5 );
			remove_filter( $ticket_form_hook, [ $instance, 'show_tickets_unavailable_message' ], 6 );

			// Replace with ESI blocks
			add_action( $ticket_form_hook, function( $content ) {
				echo apply_filters( 'litespeed_esi_url', 'ls_esi_tickets_maybe_add_front_end_tickets_form', 'Event Tickets Plus Frontend Tickets Form', [ 'content' => $content ] );
			}, 5 );
			add_filter( $ticket_form_hook, function() {
				echo apply_filters( 'litespeed_esi_url', 'ls_esi_tickets_show_tickets_unavailable_message', 'Event Tickets Plus Unavailable Message' );
			}, 6 );
		}
	}

} );

add_action( 'litespeed_esi_load-ls_esi_tickets_maybe_add_front_end_tickets_form', function( $params ) {
	do_action( 'litespeed_control_set_ttl', 300 );

	$instance = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
	return $instance->maybe_add_front_end_tickets_form( $params['content'] );
} );

add_action( 'litespeed_esi_load-ls_esi_tickets_show_tickets_unavailable_message', function() {
	do_action( 'litespeed_control_set_ttl', 300 );

	$instance = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
	echo $instance->show_tickets_unavailable_message();
} );
