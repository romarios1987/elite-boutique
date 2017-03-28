<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/init.php';

$name = sanitize($_POST['full_name']);
$email = sanitize($_POST['email']);
$street = sanitize($_POST['street']);
$street_2 = sanitize($_POST['street_2']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$zip_code = sanitize($_POST['zip_code']);
$country = sanitize($_POST['country']);

$errors = [];

$required = [
    'full_name' => 'Full name',
    'email' => 'Email',
    'street' => 'Street Address',
    'city' => 'City',
    'state' => 'State',
    'zip_code' => 'Zip Code',
    'country' => 'Country',

];
// Проверьте, заполнены ли все обязательные поля check //if all required fields are filled out//

foreach ($required as $field => $display) {
    if (empty($_POST[$field]) || $_POST[$field] == '') {
        $errors[] = $display . ' Обязательное поле.';
    }
}


// check if valid email Address
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Введите действительный адрес электронной почты'; //'Please enter valid email'
}


if (!empty($errors)) {
    echo display_errors($errors);
} else {
    echo 'passed';
}