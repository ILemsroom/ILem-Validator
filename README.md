# ILem-Validator

A simple PHP post Data Validator

Ilems-validator is a very simple PHP OOP validator library, It is to empower the user while staying concise and simple.

**Note:** this library requires php 8.0 and above to function im the right way

to use the lirary you will add an autoloader

# Adding Autoloader

```PHP
<?php
require 'vendor/autoload.php';
```

If your autoloader is not in the autoload file

```PHP
<?php
require 'path to autoloader/autoload.php';
```

# Usage

# Creating a validator instance

```PHP
<?php
require 'vendor/autoload.php';

$validator = new \Ilem\Validator\Validator;
```

# OR

```PHP
<?php
require 'vendor/autoload.php';
use Ilem\Validator\Validator;


$validator = new Validator;
```

# Validating

to validate any given date we first call the validate methode from the validator objcet

```PHP

SInce the Validator Validates data from the $_POST global variable,  The data array is already available thus,
the user only has to input the name on the $name variable

<?php
$validator = new Validator();
$validator->validate($name);
```

the validator stores it errors in the error property which can be accessed by

```PHP
<?php
$error = $validator->errors;
```

# method binding

The validator object works with method binding

```PHP
#lets validate an input with name username and a value
$validator =  new Validator();
#the first required() checkes if the value is empty
#used if a field is required
$validate->validate('username')->isRequired();
#if the value is empty an error will be stored in the errors property
```

# checking min and max

min and max values or value lenght can be checked using the min() and max() methods
etc

```PHP
#lets validate an input with name username and a value
$validator =  new Validator();
#the mean takes in an integer as the parameter
$validate->validate('username')->minLenght(5);
#if the value is less than 5 an error will be stored in the errors property
#for max
$validate->validate('username')->maxLenght(5);
#we can use all thress as
$validate->validate('username')->isRequired()
    ->minLenght(4)->maxLenght(12);
```

# working with Emails

to validate an email we use the isEmail() method

```PHP
#lets validate an input with name email and a value
$validator =  new Validator;
$validate->validate('email')->isRequired()->isEmail();
```

# working with password

password using the above methods as well but with two extra methods
secure() and like() methods are password specific but the like() method can be used for
other form of validation

```PHP
#lets validate an input with name password and a value
$validator =  new Validator;
$validate->validate('password')->Isequired();
#to confirm your password
$validator->validate('confirm_password', $_POST['confirm_password'])->required()
    ->like($_POST['password'], 'password');
```

**NOTE:** to store the value in the $Validator->data property you bind the store method
to the end of the chain

```PHP
$validator =  new Validator();
$validate->validate('username')->isRequired()
    ->minLenght(4)->maxLenght(12)->store();
```

## working with database Using

There are two methods to connect to database with the Validator class

## 1 passing your config as an array to the Validator class

```PHP
$config = [
        'host' => 'localhost',
        'engine' => 'mysql',
        'database_name' => '',
        'username' => 'root',
        'password' => '',
        'port' => 3306,
    ];
$validator = new Validator($config);
```

##2 Passing your Database configuration to the config() method

```PHP
$validator = new Validator();
$validator->config($host,$engine,$database_name,$username, $password,$port);
```

# check if data exist in the database

to perform this action we bind the isUnique($table_name) methodr to the chain

```PHP
$validator = new Validator();
$validator->config($host,$engine,$database_name,$username, $password,$port);
$validate->validate('username')->isRequired()
    ->minLenght(4)->maxLenght(12)->isUnique('table name')->store();
```

Here the column name if kept blank, the name of the name passed to the validate method will be used

## Default error messages

default messages is passed as the last or only parameter to the chained method except
for the secure method

## Checking if all Validations are valid

To check that all Validator commands are valid and there are no errors in the submitted date use the is_valid() methode
this class will return true if there are no errors

```PHP
if($validator->isValid()){
    //your code
}
```

## Stored Data

to access the stored data ( cleaned data ) you use the

```PHP
clean_data()

$data = $validator->clean_data();
# to get a single data from the stored data, use the get() method and pass the name of the dta required e.g
$username = $validator->get('username');
#this should be done when the validation is successful else the date will not be stored
```

# All methods include

```PHP
cleanInput() 
config($host,$engine,$database_name,$username,$password,$port);#confiqure Database
validate(string $name);#set the data to be valdated
isRequired($error = '');#check if the data is empty or if it exist
maxLength(int $max, string $error = '');#limit the input lenght
minLength(int $min, $error = '');# least input lenght
isSimilar(string $similar_value, $error = '');# check if two strings are similar especially in password confirmation
isEmail(string $error = '');:#valiudate for valid email
isAlpha($error = '');#check if input is letters only
hasCaps($error = '');#checks if input scontains capital latters
hasNumbers($error = '');#checks if input contains number
noNumbers($error = '');#makes sure no didgit is passed to the input
isNumeric($error = '');# checks if the input is digits only
hasSymbols($error = '');# checks if input has symbol
noSymbols($error = '');# makes sure input has no symbols
isUnique(string $table, string $column_name = '', );# check if data already exist in the database
noSpace()# make sure no space is passed in the input
cleanInput($charscters = []);#checks and removes special characters
isValid();# check if all input are valid
clean_data();#returns an array of all the validated input
get($data);# gets a singke validated input
store();# Stores the validated input if all validation are successful
```

-------

MIT, see LICENSE.
