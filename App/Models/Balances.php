<?php

namespace App\Models;

use PDO;
use \App\Flash;
use \App\Date;
use DateTime;

/**
 * Balances model
 *
 * PHP version 7.3
 */
class Balances extends \Core\Model
{	
	public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	/**
	 * Get custom period 
	 * @param string $dateStart, $dateEnd
	 * @return array 
	 */ 
	public static function getCustomPeriod($dateStart, $dateEnd)
	{
		$arg['incomes'] = static::getIncomes($dateStart, $dateEnd);
		$arg['expenses'] = static::getExpenses($dateStart, $dateEnd);
		$arg['incomesDetails'] = static::getIncomesDetails($dateStart, $dateEnd);
		$arg['expensesDetails'] = static::getExpensesDetails($dateStart, $dateEnd);
		
		$start = new DateTime($dateStart);
		$end = new DateTime($dateEnd);
		$arg['caption'] = 'Bilans za wybrany okres ('.$start->format('d/m/Y').' - '.$end->format('d/m/Y').')';
		
		return $arg;
	}
	
	/**
	 * Get current month 
	 * @param string $dateStart, $dateEnd
	 * @return array 
	 */ 
	public static function getCurrentMonth()
	{
		$date = new DateTime();
		$dateStart = $date->format('Y-m-01');
		$dateEnd = $date->format('Y-m-t');
		
		$arg = static::getCustomPeriod($dateStart, $dateEnd);
		$arg['caption'] = 'Bilans za bieżący miesiąc ('.$date->format('01/m/Y').' - '.$date->format('t/m/Y').')';
		
		return $arg;
	}
	
	/**
	 * Get previous month 
	 * @param string $dateStart, $dateEnd
	 * @return array 
	 */ 
	public static function getPreviousMonth()
	{
		$date = new DateTime();
		$date->modify("-1 month");
		$dateStart = $date->format('Y-m-01');
		$dateEnd = $date->format('Y-m-t');
		
		$arg = static::getCustomPeriod($dateStart, $dateEnd);
		$arg['caption'] = 'Bilans za poprzedni miesiąc ('.$date->format('01/m/Y').' - '.$date->format('t/m/Y').')';
		
		return $arg;
	}
	
	/**
	 * Get current year 
	 * @param string $dateStart, $dateEnd
	 * @return array 
	 */ 
	public static function getCurrentYear()
	{
		$date = new DateTime();
		$dateStart = $date->format('Y-01-01');
		$dateEnd = $date->format('Y-m-t');
		
		$arg = static::getCustomPeriod($dateStart, $dateEnd);
		$arg['caption'] = 'Bilans za bieżący rok ('.$date->format('01/01/Y').' - '.$date->format('t/m/Y').')';
		
		return $arg;
	}
	
	/**
	 * Get incomes from datebase (selected period)
	 * @param string $dateStart, $dateEnd
	 * @return array 
	 */
	private static function getIncomes($dateStart, $dateEnd)
	{
		$getIncomes = 'SELECT c.name, SUM(i.amount) 
						FROM incomes_category_assigned_to_users c 
						INNER JOIN incomes i 
						ON i.income_category_assigned_to_user_id = c.id 
						WHERE i.user_id = '.$_SESSION['user_id'].' AND i.date_of_income >= STR_TO_DATE(:dateStart, "%Y-%m-%d") AND i.date_of_income <= STR_TO_DATE(:dateEnd, "%Y-%m-%d") AND i.user_id = '.$_SESSION['user_id'].' GROUP BY c.name ORDER BY SUM(i.amount) DESC';
		
		$db = static::getDB();
		$stmt = $db->prepare($getIncomes);
		
		$stmt->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
		$stmt->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);
		
		$stmt->execute();

		return $stmt->fetchAll();
	}
	
	/**
	 * Get expenses from datebase (selected period)
	 * @param string $dateStart, $dateEnd
	 * @return array 
	 */
	private static function getExpenses($dateStart, $dateEnd)
	{
		$getExpenses = 'SELECT c.name, SUM(e.amount) 
						FROM expenses_category_assigned_to_users c 
						INNER JOIN expenses e 
						ON e.expense_category_assigned_to_user_id = c.id 
						WHERE e.user_id = '.$_SESSION['user_id'].' AND e.date_of_expense >= STR_TO_DATE(:dateStart, "%Y-%m-%d") AND e.date_of_expense <= STR_TO_DATE(:dateEnd, "%Y-%m-%d") AND e.user_id = '.$_SESSION['user_id'].' GROUP BY c.name ORDER BY SUM(e.amount) DESC';
		
		$db = static::getDB();
		$stmt = $db->prepare($getExpenses);
		
		$stmt->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
		$stmt->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);
		
		$stmt->execute();

		return $stmt->fetchAll();
	}
	
	/**
	 * Get incomes details from datebase (selected period)
	 * @param string $dateStart, $dateEnd
	 * @return array 
	 */
	private static function getIncomesDetails($dateStart, $dateEnd)
	{
		$getIncomesDetails = 'SELECT i.date_of_income, c.name, i.amount, i.income_comment, i.id
								FROM incomes i 
								INNER JOIN incomes_category_assigned_to_users c 
								ON i.income_category_assigned_to_user_id = c.id 
								WHERE i.user_id = '.$_SESSION['user_id'].' AND i.date_of_income >= STR_TO_DATE(:dateStart, "%Y-%m-%d") AND i.date_of_income <= STR_TO_DATE(:dateEnd, "%Y-%m-%d") AND i.user_id = '.$_SESSION['user_id'].' ORDER BY i.date_of_income';
		
		$db = static::getDB();
		$stmt = $db->prepare($getIncomesDetails);
		
		$stmt->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
		$stmt->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);
		
		$stmt->execute();

		return $stmt->fetchAll();
	}
	
	/**
	 * Get expenses details from datebase (selected period)
	 * @param string $dateStart, $dateEnd
	 * @return array 
	 */
	private static function getExpensesDetails($dateStart, $dateEnd)
	{
		$getExpensesDetails = 'SELECT e.date_of_expense, c.name, p.name, e.amount, e.expense_comment, e.id
								FROM expenses e 
								INNER JOIN expenses_category_assigned_to_users c 
								ON expense_category_assigned_to_user_id = c.id 
								INNER JOIN payment_methods_assigned_to_users p 
								ON e.payment_method_assigned_to_user_id = p.id 
								WHERE e.user_id = '.$_SESSION['user_id'].' AND e.date_of_expense >= STR_TO_DATE(:dateStart, "%Y-%m-%d") AND e.date_of_expense <= STR_TO_DATE(:dateEnd, "%Y-%m-%d") AND e.user_id = '.$_SESSION['user_id'].' ORDER BY e.date_of_expense';
		
		$db = static::getDB();
		$stmt = $db->prepare($getExpensesDetails);
		
		$stmt->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
		$stmt->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);
		
		$stmt->execute();

		return $stmt->fetchAll();
	}
	
	/**
	 * Validation of dates entered in the form for custom period 
	 * @return boolean True = success
	 */
	public static function validateDates()
	{
		if (isset($_POST['date1'])) {
			$dateStart = new DateTime($_POST['date1']);
			$dateEnd = new DateTime($_POST['date2']);
			$lastDayOfTheMonth = date('Y-m-t');
			$allGood = true;
		
			if ($dateStart == NULL) {
				$allGood = false;
				Flash::addMessage("Wybierz datę początku okresu", Flash::DANGER);
			}
					
			if ($dateEnd == NULL) {
				$allGood = false;
				Flash::addMessage("Wybierz datę końca okresu", Flash::DANGER);
			}				
						
			if ($dateStart->format('Y-m-d') > $lastDayOfTheMonth) {
				$allGood = false;
				Flash::addMessage("Data początku okresu nie może przekraczać daty ostatniego dnia bieżącego miesiąca", Flash::DANGER);
			}
						
			if ($dateEnd->format('Y-m-d') > $lastDayOfTheMonth) {
				$allGood = false;
				Flash::addMessage("Data końca okresu nie może przekraczać daty ostatniego dnia bieżącego miesiąca", Flash::DANGER);
			}
			
			if ($dateStart->format('Y-m-d') < '2000-01-01') {
				$allGood = false;
				Flash::addMessage("Minimalna data początku okresu to 01-01-2000", Flash::DANGER);
			}	
			
			if ($dateEnd->format('Y-m-d') < '2000-01-01') {
				$allGood = false;
				Flash::addMessage("Minimalna data końca okresu to 01-01-2000", Flash::DANGER);
			}
			
			if ($dateEnd!=NULL && $dateStart!=NULL) {
				if($dateEnd < $dateStart) {
					$allGood = false;
					Flash::addMessage("Data końca okresu nie może być wcześniejsza od daty początku okresu", Flash::DANGER);
				}
			}
		}
		
		return $allGood;
	}
	
	public function deleteExpense()
	{
		$sql = 'DELETE FROM expenses
				WHERE user_id = :user_id AND id = :expenseId';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':expenseId', $_POST['expenseId'], PDO::PARAM_INT);
		
		return $stmt->execute();
	}
	
	public function deleteIncome()
	{
		$sql = 'DELETE FROM incomes
				WHERE user_id = :user_id AND id = :incomeId';
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':incomeId', $_POST['incomeId'], PDO::PARAM_INT);
		
		return $stmt->execute();
	}
}