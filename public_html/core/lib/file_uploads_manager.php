<?php
	/*------------------------------------------------------------------------------
   $Id$

   AbanteCart, Ideal OpenSource Ecommerce Solution
   http://www.AbanteCart.com

   Copyright © 2011-2015 Belavier Commerce LLC

   This source file is subject to Open Software License (OSL 3.0)
   License details is bundled with this package in the file LICENSE.txt.
   It is also available at this URL:
   <http://www.opensource.org/licenses/OSL-3.0>

  UPGRADE NOTE:
	Do not edit or add to this file if you wish to upgrade AbanteCart to newer
	versions in the future. If you wish to customize AbanteCart for your
	needs please refer to http://www.AbanteCart.com for more information.
 ------------------------------------------------------------------------------*/
	if (! defined ( 'DIR_CORE' )) {
		header ( 'Location: static_pages/' );
	}

	/**
	 * Class to handle access to global attributes
	 *
	 */

class AFile_Uploads_Manager extends AFile {

	public function __construct() {
		parent::__construct();
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class AFile_Uploads_Manager');
		}
	}
}