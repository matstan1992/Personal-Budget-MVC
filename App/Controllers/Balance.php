<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use App\Models\Balances;

/**
 * Balance controller
 * 
 * PHP version 7.3
 */
class Balance extends Authenticated
{
	/**
     * Display data for the current month on the balance page
     *
     * @return void
     */
    public function indexAction()
    {
		$arg = Balances::getCurrentMonth();
        View::renderTemplate('Balance/index.html', $arg);
    }

	/**
     * Display data for the previous month on the balance page
     *
     * @return void
     */
	public function previousMonthAction()
	{
		$arg = Balances::getPreviousMonth();
        View::renderTemplate('Balance/index.html', $arg);
	}
	
	/**
     * Display data for the current year on the balance page
     *
     * @return void
     */
	public function currentYearAction()
	{
		$arg = Balances::getCurrentYear();
        View::renderTemplate('Balance/index.html', $arg);
	}
	
	/**
     * Display data for the custom period on the balance page
     *
     * @return void
     */
	public function customPeriodAction()
	{
		$arg = [];

		if (Balances::validateDates()) {
			
			$dateStart = $_POST['date1'];
			$dateEnd = $_POST['date2'];
			$arg = Balances::getCustomPeriod($dateStart, $dateEnd);
		}
		
        View::renderTemplate('Balance/index.html', $arg);
	}
	
	public function deleteExpense()
	{
		if (isset($_POST['expenseId'])) {
			$expense = new Balances($_POST);
			
			if ($expense->deleteExpense()) {
				
				Flash::addMessage('Wydatek został usunięty.');
				$this->redirect('/balance/index');
				
			} else {
				$this->redirect('/balance/index');
			}
		} else {
			$this->redirect('/balance/index');
		}
	}
}