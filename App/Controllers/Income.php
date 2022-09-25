<?php

namespace App\Controllers;

use \Core\View;
use App\Flash;
use App\Models\IncomeExpenseManager;
use \App\Date;

/**
 * Income controller
 * 
 * PHP version 7.3
 */
class Income extends Authenticated
{
    /**
     * Income index
     *
     * @return void
     */
    public function indexAction()
    {
		$arg['incomesCategories'] = IncomeExpenseManager::getIncomesCategories();
		$arg['date'] = Date::getCurrentDate();
        View::renderTemplate('Income/index.html', $arg);
    }
	
	/**
	 * Add a new income 
	 * 
	 * @return void 
	 */
	public function newAction($arg = [])
	{
		$allGood = IncomeExpenseManager::saveIncome();
		
		if ($allGood) {
			Flash::addMessage('Przychód dodano pomyślnie!');
			View::renderTemplate('MainMenu/index.html');
		} else {
		$arg['incomesCategories'] = IncomeExpenseManager::getIncomesCategories();
		$arg['amount'] = $_POST['amount'];
		$arg['date'] = $_POST['date'];
		$arg['comment'] = $_POST['comment'];
		
		View::renderTemplate('Income/index.html', $arg);
		}
	}
}