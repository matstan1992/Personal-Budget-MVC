<?php

namespace App\Models;

use PDO;
use \App\Token;

/**
 * User model
 *
 * PHP version 7.3
 */
class User extends \Core\Model
{
   /**
     * Error messages
     *
     * @var array
     */
    public $error_name = [];
    public $error_email = [];
    public $error_password = [];
	
	/**
     * Class constructor
     *
     * @param array $data  Initial property values
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
		$success = true;
		
        $this->validate();

        if (empty($this->error_name) && empty($this->error_email) && empty($this->error_password)) {

            $password_hash = password_hash($this->password1, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (username, email, password)
                    VALUES (:name, :email, :password_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            $success =$success && $stmt->execute();
			
			$user_id = $db->lastInsertId();
			
			$add_default_payment_methods = "INSERT INTO payment_methods_assigned_to_users (user_id, name) SELECT $user_id, name FROM payment_methods_default";
			$stmt = $db->prepare($add_default_payment_methods);
			$success =$success && $stmt->execute();
			
			$add_default_expenses_category = "INSERT INTO expenses_category_assigned_to_users (user_id, name) SELECT $user_id, name FROM expenses_category_default";
			$stmt = $db->prepare($add_default_expenses_category);
			$success =$success && $stmt->execute();
			
			$add_default_incomes_category = "INSERT INTO incomes_category_assigned_to_users (user_id, name) SELECT $user_id, name FROM incomes_category_default";
			$stmt = $db->prepare($add_default_incomes_category);
			$success =$success && $stmt->execute();
			
			return $success;
        }

        return false;
    }
	
    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        // Name
        if ($this->name == '') {
            $this->error_name[] = 'Imię jest wymagane!';
        }
		
		//First letter uppercase, the rest lowercase
		$this->name = mb_convert_case($this->name, MB_CASE_TITLE, "UTF-8");
		
		//Length username
		if ((strlen($this->name) < 3) || (strlen($this->name) > 20)) {
			$this->error_name[] = "Imię musi posiadać od 3 do 20 znaków!";
		}
		
		$alphabet = '/^[a-ząęółśżźćńA-ZĄĘÓŁŚŹŻĆŃ]+$/';	//regular expression
		
		if (!preg_match($alphabet, $this->name)) {
			$this->error_name[] = "Imię musi składać się tylko ze znaków polskiego alfabetu!";
		}

        // email address
		$emailB = filter_var($this->email, FILTER_SANITIZE_EMAIL);
		
        if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) === false) || ($emailB != $this->email)) {
            $this->error_email[] = 'Niepoprawny email!';
        }
        if (static::emailExists($this->email)) {
            $this->error_email[] = 'Podany email jest już zajęty!';
        }

        // Password
        if ((strlen($this->password1) < 6) || (strlen($this->password1) > 20)) {
            $this->error_password[] = 'Hasło musi posiadać od 8 do 20 znaków!';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password1) == 0) {
            $this->error_password[] = 'Hasło musi posiadać przynajmniej jedną literę!';
        }

        if (preg_match('/.*\d+.*/i', $this->password1) == 0) {
            $this->error_password[] = 'Hasło musi posiadać przynajmniej jedną cyfrę!';
        }
		
		if ($this->password1 != $this->password2) {
			$this->error_password[] = 'Hasła muszą być takie same!';
		}
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
     public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

    /**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	
	/**
     * Authenticate a user by email and password.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }
	
	/**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	
	/**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }
}
