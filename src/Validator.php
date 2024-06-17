<?php

namespace Ilem\Validator;

use PDO;

/**
 * @author ilem Ilem <ilemsroom@gmail.com>
 * Validator
 */
class Validator
{

    public $validate;
    public $errors = [];
    public $cleaned_data = [];
    private $name = '';
    
    private $value = '';
    protected $request;
    private $table;

    private string $host;
    private string $engine;
    private string $database_name;
    private string $username;
    private string $password;
    private int $port;

    protected $db_config = [
        'host' => 'localhost',
        'engine' => 'mysql',
        'database_name' => '',
        'username' => 'root',
        'password' => '',
        'port' => 3306,
    ];

    protected $err_array = [
        'text_errror' => 'Only Latters, numbers and text required',
        'number_error' => 'Only numbers needed but Latters are provided',
        'unique' => 'This data already exist',
        'empty' => 'This field is required',
    ];

    protected array $characters = [
        '~', '`', '@', '!', '#', '$', '%', '^', '&', '*', '(', ')', '_',
        '-', '+', '=', '[', ']', '{', '}', '\'', ':', '"', ';', '>', '<',
        '.', ',', '/', '|', '\\', '?'
    ];

    public function __construct(array $db_config = [])
    {

        $this->request = array_merge($_POST, $_GET);

        foreach ($this->request as $key => $value) {
            $this->$key = $value;
        }

        if (!empty($db_config)) {
            foreach ($db_config as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                } else {
                    throw new \Exception('UnKnow Configuration ' . $key, 0);
                }
            }

        } else {
            foreach ($db_config as $key => $value) {
                $this->$key = $value;
            }
        }
    }
    
  /**
   * config()
   *
   * @param string $host
   * @param string $engine
   * @param string $database_name
   * @param string $username
   * @param string $password
   * @param int $port
   * @return \Ilem\Validator\Validatr
   * 
   * The Config() method takes in the database config info, for those that do not wish to use the 
   * Array argument of the __constructor
   */
   public function config(
        $host,
        $engine,
        $database_name,
        $username,
        $password,
        $port
    ):\Ilem\Validator\Validator {
        $this->host          = $host;
        $this->engine        = $engine;
        $this->database_name = $database_name;
        $this->username      = $username;
        $this->password      = $password;
        $this->port          = $port;

        return $this;
    }


    /**
     * generateDsn()
     *
     * @return string
     */
    private function generateDsn():string
    {
        return $this->engine . ':host=' . $this->host . ';dbname=' . $this->database_name.';';
    }

    /**
     * connect()
     * Private method, Responsible for database connection and returns a pdo object
     *
     * @return object
     */
    private function connect(): object
    {
        $dsn = $this->generateDsn();
        try {
            $conn = new PDO($dsn, $username = $this->username, $password = $this->password);
        } catch (\Exception $e) {
            throw new \Exception('Cannot Connect to Database', 0);
        }

        return $conn;
    }

/**
 * validate
 *
 * @param string $name
 * @return \Ilem\Validator\Validator
 * 
 *The main method of the Validator class, it takes and stores the name of the key value pair to be 
 *validated t a given time.
 *All binding begins with the validator method. If not called, 
 *the entire validation process will be to no avial
 */
    public function validate(string $name):\Ilem\Validator\Validator
    {

        if (property_exists($this, $name)) {
            $this->name  = $name;
            $this->value = $this->$name;
        } else {
            throw new \Exception('this name "' . $name . '" does note exist in the request body', 1);
        }
        return $this;
    }

    public function isRequired($error = ''):\Ilem\Validator\Validator
    {
        $value = trim($this->value, ' ');
        if ($value == '') {
            empty($error) ?
                $this->errors[$this->name] = $this->err_array['empty'] :
                $this->errors[$this->name] = $error;
        }
        return $this;
    }

    public function maxLength(int $max, string $error = ''):\Ilem\Validator\Validator
    {
        $this->isRequired();
        if (strlen($this->value) > $max && !array_key_exists($this->name, $this->errors)) {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' is more than the required lenght of ' . $max :
                $this->errors[$this->name] = $error;
        }

        return $this;
    }

    public function minLength(int $min, $error = ''):\Ilem\Validator\Validator
    {
        if (
            !array_key_exists($this->name, $this->errors) &&
            strlen(str_replace(' ', '', $this->value)) < $min ||
            strlen($this->value) < $min 
        ) {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' is less than the required lenght of ' . $min :
                $this->errors[$this->name] = $error;
        }
        return $this;
    }

    public function isSimilar(string $similar_value, $error = ''):\Ilem\Validator\Validator
    {
        $this->isRequired();
        if ($this->value === $similar_value && !array_key_exists($this->name, $this->errors)) {
        } else {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' is not similar to the provided value' :
                $this->errors[$this->name] = $error;
        }
        return $this;
    }

    public function isEmail(string $error = ''):\Ilem\Validator\Validator
    {
        if ($this->value === '') {
            empty($error) ?
                $this->errors[$this->name] = $this->err_array['empty'] :
                $this->errors[$this->name] = $error;
        } elseif (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            empty($error) ?
                $this->errors[$this->name] = 'Invalid Email address' :
                $this->errors[$this->name] = $error;
        }
        return $this;
    }

    public function isAlpha($error = ''):\Ilem\Validator\Validator
    {
        if (!preg_match("/^[a-zA-Z]*$/", $this->value)) {
            if (empty($error)) {
                $this->errors = [$this->name => $this->name . ' should contain Latters only'];
            } else {
                $this->errors = [$this->name => $error];
            }
        }
        return $this;
    }

    public function hasCaps($error = ''):\Ilem\Validator\Validator
    {
        if (
            !preg_match("/[A-Z]/", $this->value) &&
            !array_key_exists($this->name, $this->errors)
        ) {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' should contain at least one Upper or Lower cases' :
                $this->errors[$this->name] = $error;
        }
        return $this;
    }

    public function hasNumbers($error = ''):\Ilem\Validator\Validator
    {
        if (
            !preg_match("/[0-9]/", $this->value) &&
            !array_key_exists($this->name, $this->errors)
        ) {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' should contain at least one Number' :
                $this->errors[$this->name] = $error;
        }
        return $this;

    }

    public function noNumbers($error = ''):\Ilem\Validator\Validator
    {
        if (
            preg_match("/[0-9]/", $this->value) &&
            !array_key_exists($this->name, $this->errors)
        ) {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' should Not contain  Numbers' :
                $this->errors[$this->name] = $error;
        }
        return $this;

    }

    public function isNumeric($error = ''):\Ilem\Validator\Validator
    {
        if (
            !preg_match("/^[0-9]*$/", $this->value) &&
            !array_key_exists($this->name, $this->errors)
        ) {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' should be only numbers' :
                $this->errors[$this->name] = $error;
        }
        return $this;

    }

    public function hasSymbols($error = ''):\Ilem\Validator\Validator
    {
        if (
            !preg_match("/[\W+]/", $this->value) &&
            !array_key_exists($this->name, $this->errors)
        ) {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' should contain at least one Symbol' :
                $this->errors[$this->name] = $error;
        }
        return $this;

    }

    public function noSymbols($error = ''):\Ilem\Validator\Validator
    {
        if (
            preg_match("/[\W+]/", $this->value) &&
            !array_key_exists($this->name, $this->errors)
        ) {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' should Not contain Symbols' :
                $this->errors[$this->name] = $error;
        }
        return $this;

    }


    public function isUnique(string $table, string $column_name = '', ):\Ilem\Validator\Validator
    {
        if(empty($column_name)){
            $column_name = $this->name;
        }

        try {
            $sql    = 'SELECT * FROM ' . $table . ' WHERE ' . $column_name . '= :email';
        
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':email', $this->value);
            $stmt->execute();
            $data   = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($data) {
                $this->errors[$this->name] = $this->err_array['unique'];
            }
    
        } catch (\Throwable $e) {
             throw new \Exception($e->getMessage(), 1);
             
        }
      
        return $this;
    }


    public function noSpace():\Ilem\Validator\Validator{
        if (
           str_contains($this->value, ' ') &&
            !array_key_exists($this->name, $this->errors)
        ) {
            empty($error) ?
                $this->errors[$this->name] = $this->name . ' Space not allowed' :
                $this->errors[$this->name] = $error;
        }
        return $this;
    }

    public function cleanInput($charscters = []):\Ilem\Validator\Validator{
        if(empty($characters)){
            $characters = $this->characters;
        }

        $new_value = str_replace($characters, '_', $this->value);
        $this->value = $new_value;

        return $this;
    }


    public function isValid():bool
    {
        $cond = true;
        if (empty($this->errors) || count($this->errors) < 1) {
            $cond = true;
        } else {
            $cond = false;
        }
        return $cond;
    }

    public function clean_data(): array
    {
        return $this->cleaned_data;
    }

    public function get($data): string
    {
        if (array_key_exists($data, $this->clean_data())) {
            return $this->clean_data()[$data];
        } else {
            return '';
        }
    }

    public function store():void
    {
        $errors = $this->errors;
        if (array_key_exists($this->name, $errors)) {
            if ($errors[$this->name] == '') {
                unset($errors[$this->name]);
                $this->cleaned_data[$this->name] = $this->value;
            }
        } else {
            $this->cleaned_data[$this->name] = $this->value;
        }
    }

}
