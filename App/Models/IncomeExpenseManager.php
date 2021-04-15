<?php

namespace App\Models;

use PDO;
use \App\Flash;
use \App\Date;

/**
 * IncomeExpenseManager model
 *
 * PHP version 7.3
 */
class IncomeExpenseManager extends \Core\Model
{	
	/**
	 * Get categories of incomes from database 
	 * 
	 * @return array with categories 
	 */
	public static function getIncomesCategories()
	{
		if (isset($_SESSION['user_id'])) {
						
			$sql = "SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id =".$_SESSION['user_id'];
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->execute();

			return $stmt->fetchAll();
		}
	}
	
	/**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public static function validate()
	{
		$allGood = true;
		
		if ((!isset($_POST['amount'])) || (!isset($_POST['date'])) || (!isset($_POST['category']))) {
			Flash::addMessage('Wypełnij wymagane pola!', Flash::DANGER);
			$allGood = false;
		}
		
		//change separator (comma / dot)
		if (strpos($_POST['amount'], ",") == true) {
			$_POST['amount'] = str_replace(",", ".", $_POST['amount']);
		}

		//date
		if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $_POST['date'])) {
			Flash::addMessage('Nieprawidłowy format daty (DD.MM.RRRR)', Flash::DANGER);
			$allGood = false;
		}
		$incomeDate = $_POST['date'];
		
		$theLastDayOfTheCurrentMonth = date('Y-m-t');
		
		if ($incomeDate > $theLastDayOfTheCurrentMonth) {
			$allGood = false;
			Flash::addMessage('Maksymalna data do wyboru to ostatni dzień bieżącego miesiąca', Flash::DANGER);	
		}
		
		if ($incomeDate < '2000-01-01') {
			$allGood = false;
			Flash::addMessage('Minimalna data do wyboru to 01-01-2000', Flash::DANGER);	
		}
		
		//comment
		if (isset($_POST['comment'])) {
			
			$comment = htmlentities($_POST['comment'], ENT_QUOTES, "UTF-8");
			
			if (strlen($_POST['comment']) > 100) {
				Flash::addMessage('Komentarz może zawierać maksymalnie 100 znaków!', Flash::DANGER);
				$allGood = false;
			}
		}
		
		return $allGood;
	}
	
	/**
	 * Add new income to database 
	 *
	 * @return True if success, False otherwise
	 */
	public static function saveIncome()
	{
		if (static::validate()) {
			
			$sql = "INSERT INTO incomes (id, user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) 
			VALUES ('NULL', :user_id, :category, :amount, :date, :comment)";
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':amount', $_POST['amount'], PDO::PARAM_STR);
            $stmt->bindValue(':date', $_POST['date'], PDO::PARAM_STR);
            $stmt->bindValue(':category', $_POST['category'], PDO::PARAM_INT);
            $stmt->bindValue(':comment', $_POST['comment'], PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

            return $stmt->execute();
		}
		
		return false;
		
	}
}