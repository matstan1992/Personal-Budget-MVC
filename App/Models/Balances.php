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
	 * Get incomes from datebase (selected period)
	 * @param string $dateStart, $dateEnd
	 * @return array 
	 */
	private static function getIncomes($dateStart, $dateEnd)
	{
		$getIncomes = 'SELECT c.name, SUM(i.amount) FROM incomes_category_assigned_to_users c INNER JOIN incomes i ON i.income_category_assigned_to_user_id = c.id WHERE i.user_id = '.$_SESSION['user_id'].' AND i.date_of_income >= STR_TO_DATE(:dateStart, "%Y-%m-%d") AND i.date_of_income <= STR_TO_DATE(:dateEnd, "%Y-%m-%d") AND i.user_id = '.$_SESSION['user_id'].' GROUP BY c.name ORDER BY SUM(i.amount) DESC';
		
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
		$getExpenses = 'SELECT c.name, SUM(e.amount) FROM expenses_category_assigned_to_users c INNER JOIN expenses e ON e.expense_category_assigned_to_user_id = c.id WHERE e.user_id = '.$_SESSION['user_id'].' AND e.date_of_expense >= STR_TO_DATE(:dateStart, "%Y-%m-%d") AND e.date_of_expense <= STR_TO_DATE(:dateEnd, "%Y-%m-%d") AND e.user_id = '.$_SESSION['user_id'].' GROUP BY c.name ORDER BY SUM(e.amount) DESC';
		
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
		$getIncomesDetails = 'SELECT i.date_of_income, c.name, i.amount, i.income_comment FROM incomes i INNER JOIN incomes_category_assigned_to_users c ON i.income_category_assigned_to_user_id = c.id WHERE i.user_id = '.$_SESSION['user_id'].' AND i.date_of_income >= STR_TO_DATE(:dateStart, "%Y-%m-%d") AND i.date_of_income <= STR_TO_DATE(:dateEnd, "%Y-%m-%d") AND i.user_id = '.$_SESSION['user_id'].' ORDER BY i.date_of_income';
		
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
		$getExpensesDetails = 'SELECT e.date_of_expense, c.name, p.name, e.amount, e.expense_comment FROM expenses e INNER JOIN expenses_category_assigned_to_users c ON expense_category_assigned_to_user_id = c.id INNER JOIN payment_methods_assigned_to_users p ON e.payment_method_assigned_to_user_id = p.id WHERE e.user_id = '.$_SESSION['user_id'].' AND e.date_of_expense >= STR_TO_DATE(:dateStart, "%Y-%m-%d") AND e.date_of_expense <= STR_TO_DATE(:dateEnd, "%Y-%m-%d") AND e.user_id = '.$_SESSION['user_id'].' ORDER BY e.date_of_expense';
		
		$db = static::getDB();
		$stmt = $db->prepare($getExpensesDetails);
		
		$stmt->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
		$stmt->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);
		
		$stmt->execute();

		return $stmt->fetchAll();
	}
}