<?php

namespace App\Controllers;

use \Core\View;
use App\Flash;
use App\Models\IncomeExpenseManager;
use \App\Date;

/**
 * Expense controller
 * 
 * PHP version 7.3
 */
class Expense extends Authenticated
{
    /**
     * Expense index
     *
     * @return void
     */
    public function indexAction()
    {
		$arg['expensesCategories'] = IncomeExpenseManager::getExpensesCategories();
		$arg['paymentMethods'] = IncomeExpenseManager::getPaymentMethods();
		$arg['date'] = Date::getCurrentDate();
        View::renderTemplate('Expense/index.html', $arg);
    }
	
	/**
	 * Add a new expense 
	 * 
	 * @return void 
	 */
	public function newAction($arg = [])
	{
		$allGood = IncomeExpenseManager::saveExpense();
		
		if ($allGood) {
			Flash::addMessage('Wydatek dodano pomyślnie!');
			$this->redirect('/expense/index');
		} else {
		$arg['expensesCategories'] = IncomeExpenseManager::getExpensesCategories();
		$arg['paymentMethods'] = IncomeExpenseManager::getPaymentMethods();
		$arg['amount'] = $_POST['amount'];
		$arg['date'] = $_POST['date'];
		$arg['comment'] = $_POST['comment'];
		
		View::renderTemplate('Expense/index.html', $arg);
		}
	}
	
	public function getCategoryLimitAction() 
	{
		$id = $this->route_params['id'];
		$data = IncomeExpenseManager::getLimit($id);
		
		echo json_encode($data);
	}
	
	public function getExpensesDateAction()
	{
		$id = $this->route_params['id'];
		$date = $this->route_params['date'];
		
		$dates = IncomeExpenseManager::findStartAndEndDate($date);
		$monthlySpend = IncomeExpenseManager::getMonthlyExpenses($id, $date, $dates);
		
		echo json_encode($monthlySpend);
	}
}