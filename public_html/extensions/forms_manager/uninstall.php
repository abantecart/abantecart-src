<?php
/*------------------------------------------------------------------------------
  $Id$

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2011, 2012 AlgoZone, Inc

------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

//delete menu item
$menu = new AMenu ( "admin" );
$menu->deleteMenuItem ("forms_manager");