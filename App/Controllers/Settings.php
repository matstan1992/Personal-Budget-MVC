<?php

namespace App\Controllers; 

use \Core\View;
use \App\Auth;
use \App\Flash;
use App\Models\IncomeExpenseManager;

/** 
 * Settings controller 
 * 
 * PHP version 7.3 
 */ 
class Settings extends Authenticated
{
	
	/**
	 * Before filter - called before each action method 
	 *
	 * @return void 
	 */
	protected function before()
	{
		parent::before();
		
		$this->user = Auth::getUser();
	}
	
	/** 
	 * Show the settings page
	 * 
	 * @return void 
	 */ 
	public function indexAction()
	{
		$arg['user'] = $this->user;
		$arg['incomesCategories'] = IncomeExpenseManager::getIncomesCategories();
		$arg['expensesCategories'] = IncomeExpenseManager::getExpensesCategories();
		$arg['paymentMethods'] = IncomeExpenseManager::getPaymentMethods();
		View::renderTemplate('Settings/index.html', $arg);
	}
	
	public function addNewIncomeCategory()
	{
		if (isset($_POST['newIncomeCategory'])) {
			$incomeCategory = new IncomeExpenseManager($_POST);
			
			if ($incomeCategory->addNewIncomeCategory()) {
				
				Flash::addMessage('Nowa kategoria przychodów została dodana pomyślnie!');
				$this->redirect('/settings/index');
				
			} else {		
				$this->redirect('/settings/index');
			}
		} else {
			$this->redirect('/settings/index');
		}
	}
	
	public function updateIncomeCategory()
	{
		if (isset($_POST['incomeCategoryId'])) {
			$incomeCategory = new IncomeExpenseManager($_POST);
			
			if ($incomeCategory->updateIncomeCategory()) {
				
				Flash::addMessage('Kategoria przychodów została zedytowana!');
				$this->redirect('/settings/index');
				
			} else {		
				$this->redirect('/settings/index');
			}
		} else {
			$this->redirect('/settings/index');
		}
	}
	
	public function addNewExpenseCategory()
	{
		if (isset($_POST['newExpenseCategory'])) {
			$expenseCategory = new IncomeExpenseManager($_POST);
			
			if ($expenseCategory->addNewExpenseCategory()) {
				
				Flash::addMessage('Nowa kategoria wydatków została dodana pomyślnie!');
				$this->redirect('/settings/index');
				
			} else {		
				$this->redirect('/settings/index');
			}
		} else {
			$this->redirect('/settings/index');
		}
	}
	
	public function updateExpenseCategory()
	{
		if (isset($_POST['expenseCategoryId'])) {
			$expenseCategory = new IncomeExpenseManager($_POST);
			
			if ($expenseCategory->updateExpenseCategory()) {
				
				Flash::addMessage('Kategoria wydatków została zedytowana!');
				$this->redirect('/settings/index');
				
			} else {		
				$this->redirect('/settings/index');
			}
		} else {
			$this->redirect('/settings/index');
		}
	}
	
	public function deleteExpenseCategory()
	{
		if (isset($_POST['expenseCategoryId'])) {
			$paymentMethod = new IncomeExpenseManager($_POST);
			
			if ($paymentMethod->deleteExpenseCategory()) {
				
				Flash::addMessage('Kategoria wydatków została usunięta. Wszystkie wydatki przypisane do tej kategorii zostały przeniesione do kategorii "Inne".');
				$this->redirect('/settings/index');
				
			} else {		
				$this->redirect('/settings/index');
			}
		} else {
			$this->redirect('/settings/index');
		}
	}
	
	public function addNewPaymentMethod()
	{
		if (isset($_POST['newPaymentMethod'])) {
			$paymentMethod = new IncomeExpenseManager($_POST);
			
			if ($paymentMethod->addNewPaymentMethod()) {
				
				Flash::addMessage('Nowa metoda płatności została dodana pomyślnie!');
				$this->redirect('/settings/index');
				
			} else {		
				$this->redirect('/settings/index');
			}
		} else {
			$this->redirect('/settings/index');
		}
	}
	
	public function updatePaymentMethod()
	{
		if (isset($_POST['paymentMethodId'])) {
			$paymentMethod = new IncomeExpenseManager($_POST);
			
			if ($paymentMethod->updatePaymentMethod()) {
				
				Flash::addMessage('Sposób płatności został zedytowany!');
				$this->redirect('/settings/index');
				
			} else {		
				$this->redirect('/settings/index');
			}
		} else {
			$this->redirect('/settings/index');
		}
	}
	
	public function deletePaymentMethod()
	{
		if (isset($_POST['paymentMethodId'])) {
			$paymentMethod = new IncomeExpenseManager($_POST);
			
			if ($paymentMethod->deletePaymentMethod()) {
				
				Flash::addMessage('Sposób płatności został usunięty. Wydatki, które posiadały ten sposób płatności od teraz posiadają sposób płatności o nazwie "Inne".');
				$this->redirect('/settings/index');
				
			} else {		
				$this->redirect('/settings/index');
			}
		} else {
			$this->redirect('/settings/index');
		}
	}
	
	/**
	 * Show the form for editing the profile 
	 * 
	 * @return void 
 
	public function editAction()
	{
		View::renderTemplate('Profile/edit.html', [
			'user' => $this->user
		]);
	}
		 */
	/**
	 * Update the profile 
	 * 
	 * @return void 

	public function updateAction() 
	{
		
		if ($this->user->updateProfile($_POST)) {
			
			Flash::addMessage('Zmiany zapisano pomyślnie');
			
			$this->redirect('/settings');
			
		} else {
			
			View::renderTemplate('Profile/edit.html', [
				'user' => $this->user
			]);
			
		}
	}	 */ 
}