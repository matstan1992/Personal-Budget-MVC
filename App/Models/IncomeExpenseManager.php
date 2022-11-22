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

	public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

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
	
	/**
	 * Get categories of expenses from database 
	 * 
	 * @return array with categories 
	 */
	public static function getExpensesCategories()
	{
		if (isset($_SESSION['user_id'])) {
						
			$sql = "SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id =".$_SESSION['user_id'];
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->execute();

			return $stmt->fetchAll();
		}
	}
	
	/**
	 * Get payment methods from database 
	 * 
	 * @return array with payment methods 
	 */
	public static function getPaymentMethods()
	{
		if (isset($_SESSION['user_id'])) {
						
			$sql = "SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id =".$_SESSION['user_id'];
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->execute();

			return $stmt->fetchAll();
		}
	}
	
	/**
	 * Add new income to database 
	 *
	 * @return True if success, False otherwise
	 */
	public static function saveExpense()
	{
		if (static::validate()) {
			
			$sql = "INSERT INTO expenses (id, user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) 
			VALUES ('NULL', :user_id, :category, :paymentMethod, :amount, :date, :comment)";
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':amount', $_POST['amount'], PDO::PARAM_STR);
            $stmt->bindValue(':date', $_POST['date'], PDO::PARAM_STR);
            $stmt->bindValue(':paymentMethod', $_POST['paymentMethod'], PDO::PARAM_INT);
            $stmt->bindValue(':category', $_POST['category'], PDO::PARAM_INT);
            $stmt->bindValue(':comment', $_POST['comment'], PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

            return $stmt->execute();
		}
		
		return false;
		
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
	
	protected function validateNewIncomeCategoryName()
	{	
		$allGood = true;
		
		//First letter uppercase, the rest lowercase
		$_POST['newIncomeCategory'] = ucfirst($_POST['newIncomeCategory']);
		
		if (strlen($_POST['newIncomeCategory']) < 1 || strlen($_POST['newIncomeCategory']) > 40) {
			Flash::addMessage('Nazwa kategorii musi zawierać od 1 do 40 znaków!', Flash::DANGER);
			$allGood = false;
		}
		
		$sql = "SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :incomeName";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':incomeName', $_POST['newIncomeCategory'], PDO::PARAM_STR);
		
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($result) == 1) {
			Flash::addMessage('Podana kategoria już istnieje!', Flash::DANGER);
			$allGood = false;
		}
		
		return $allGood;
	}
	
	public function addNewIncomeCategory()
	{
		if (static::validateNewIncomeCategoryName()) {
			
			$sql = "INSERT INTO incomes_category_assigned_to_users (id, user_id, name) 
			VALUES ('NULL', :user_id, :name)";
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $_POST['newIncomeCategory'], PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

            return $stmt->execute();
		}
		
		return false;
	}
	
	public function updateIncomeCategory()
	{
		$allGood = true;
		
		$_POST['incomeCategory'] = ucfirst($_POST['incomeCategory']);
		
		if (strlen($_POST['incomeCategory']) < 1 || strlen($_POST['incomeCategory']) > 40) {
			Flash::addMessage('Nazwa kategorii musi zawierać od 1 do 40 znaków!', Flash::DANGER);
			$allGood = false;
		}
		
		$sql = "SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :incomeName AND id <> :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':id', $_POST['incomeCategoryId'], PDO::PARAM_INT);
		$stmt->bindValue(':incomeName', $_POST['incomeCategory'], PDO::PARAM_STR);
		
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($result) == 1) {
			Flash::addMessage('Podana kategoria już istnieje!', Flash::DANGER);
			$allGood = false;
		}
		
		if ($allGood == true)
		{
			$sql = "UPDATE incomes_category_assigned_to_users SET name = :name WHERE id = :id";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':id', $_POST['incomeCategoryId'], PDO::PARAM_INT);
			$stmt->bindValue(':name', $_POST['incomeCategory'], PDO::PARAM_STR);
			
			return $stmt->execute();
		}
		
		return false;
	}
	
	protected function validateNewExpenseCategoryName()
	{	
		$allGood = true;
		
		//First letter uppercase, the rest lowercase
		$_POST['newExpenseCategory'] = ucfirst($_POST['newExpenseCategory']);
		
		if (strlen($_POST['newExpenseCategory']) < 1 || strlen($_POST['newExpenseCategory']) > 40) {
			Flash::addMessage('Nazwa kategorii musi zawierać od 1 do 40 znaków!', Flash::DANGER);
			$allGood = false;
		}
		
		$sql = "SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :expenseName";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':expenseName', $_POST['newExpenseCategory'], PDO::PARAM_STR);
		
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result) == 1) {
			Flash::addMessage('Podana kategoria już istnieje!', Flash::DANGER);
			$allGood = false;
		}
		
		return $allGood;
	}
	
	public function addNewExpenseCategory()
	{
		if (static::validateNewExpenseCategoryName()) {
			
			$sql = "INSERT INTO expenses_category_assigned_to_users (id, user_id, name) 
			VALUES ('NULL', :user_id, :name)";
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $_POST['newExpenseCategory'], PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

            return $stmt->execute();
		}
		
		return false;
	}
	
	public function updateExpenseCategory()
	{
		$allGood = true;
		
		$_POST['expenseCategory'] = ucfirst($_POST['expenseCategory']);
		
		if (strlen($_POST['expenseCategory']) < 1 || strlen($_POST['expenseCategory']) > 40) {
			Flash::addMessage('Nazwa kategorii musi zawierać od 1 do 40 znaków!', Flash::DANGER);
			$allGood = false;
		}
		
		$sql = "SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :expenseName AND id <> :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':id', $_POST['expenseCategoryId'], PDO::PARAM_INT);
		$stmt->bindValue(':expenseName', $_POST['expenseCategory'], PDO::PARAM_STR);
		
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($result) == 1) {
			Flash::addMessage('Podana kategoria już istnieje!', Flash::DANGER);
			$allGood = false;
		}
		
		if ($allGood == true)
		{
			$sql = "UPDATE expenses_category_assigned_to_users SET name = :name WHERE id = :id";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':id', $_POST['expenseCategoryId'], PDO::PARAM_INT);
			$stmt->bindValue(':name', $_POST['expenseCategory'], PDO::PARAM_STR);
			
			return $stmt->execute();
		}
		
		return false;
	}	
	
	protected function validateNewPaymentMethodName()
	{	
		$allGood = true;
		
		//First letter uppercase, the rest lowercase
		$_POST['newPaymentMethod'] = ucfirst($_POST['newPaymentMethod']);
		
		if (strlen($_POST['newPaymentMethod']) < 1 || strlen($_POST['newPaymentMethod']) > 40) {
			Flash::addMessage('Nazwa sposobu płatności musi zawierać od 1 do 40 znaków!', Flash::DANGER);
			$allGood = false;
		}
		
		$sql = "SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :paymentName";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':paymentName', $_POST['newPaymentMethod'], PDO::PARAM_STR);
		
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($result) == 1) {
			Flash::addMessage('Podany sposób płatności już istnieje!', Flash::DANGER);
			$allGood = false;
		}
		
		return $allGood;
	}
	
	public function addNewPaymentMethod()
	{
		if (static::validateNewPaymentMethodName()) {
			
			$sql = "INSERT INTO payment_methods_assigned_to_users (id, user_id, name) 
			VALUES ('NULL', :user_id, :name)";
			
			$db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $_POST['newPaymentMethod'], PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

            return $stmt->execute();
		}
		
		return false;
	}
	
	public function updatePaymentMethod()
	{
		$allGood = true;
		
		$_POST['paymentMethod'] = ucfirst($_POST['paymentMethod']);
		
		if (strlen($_POST['paymentMethod']) < 1 || strlen($_POST['paymentMethod']) > 40) {
			Flash::addMessage('Nazwa sposobu płatności musi zawierać od 1 do 40 znaków!', Flash::DANGER);
			$allGood = false;
		}
		
		$sql = "SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :paymentName AND id <> :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':id', $_POST['paymentMethodId'], PDO::PARAM_INT);
		$stmt->bindValue(':paymentName', $_POST['paymentMethod'], PDO::PARAM_STR);
		
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($result) == 1) {
			Flash::addMessage('Podany sposób płatności już istnieje!', Flash::DANGER);
			$allGood = false;
		}
		
		if ($allGood == true)
		{
			$sql = "UPDATE payment_methods_assigned_to_users SET name = :name WHERE id = :id";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':id', $_POST['paymentMethodId'], PDO::PARAM_INT);
			$stmt->bindValue(':name', $_POST['paymentMethod'], PDO::PARAM_STR);
			
			return $stmt->execute();
		}
		
		return false;
	}
	
}