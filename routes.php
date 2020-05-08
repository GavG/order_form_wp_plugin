<?php

use Order_Form\Core\Route_Type as Route_Type;

$router
	->register_route_of_type( ROUTE_TYPE::ADMIN )
	->with_controller( 'Admin_Settings@register_hook_callbacks' )
	->with_model( 'Admin_Settings' )
	->with_view( 'Admin_Settings' );
