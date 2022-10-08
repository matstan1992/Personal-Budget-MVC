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