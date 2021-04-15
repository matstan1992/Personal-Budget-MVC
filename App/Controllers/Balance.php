<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Balances;

/**
 * Balance controller
 * 
 * PHP version 7.3
 */
class Balance extends Authenticated
{
	/**
     * Balance index
     *
     * @return void
     */
    public function indexAction()
    {
		$arg = Balances::getCurrentMonth();
        View::renderTemplate('Balance/index.html', $arg);
    }
}