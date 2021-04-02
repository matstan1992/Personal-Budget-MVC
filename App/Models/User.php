<?php

namespace App\Models;

use PDO;

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
    public function __construct($data)
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

            return $stmt->execute();
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
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch() !== false;
    }
}
