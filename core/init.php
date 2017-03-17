<?php

$db = mysqli_connect('localhost', 'root', '', 'e_boutique');
if (mysqli_connect_errno()) { // Возвращает код ошибки последней попытки соединения
    echo 'Подключение к базе данных не удалось со следующими ошибками: ' . mysqli_connect_error();
    die();
}
mysqli_set_charset($db, "utf8") or die ("Не установлена кодировка соединния");

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once BASE_URL . '/helpers/helpers.php';

$cart_id = '';
if (isset($_COOKIE[CART_COOKIE])){
    $cart_id = sanitize($_COOKIE[CART_COOKIE]);
}



if (isset($_SESSION['SBUser'])){
    $user_id = $_SESSION['SBUser'];
    $query = $db->query("SELECT * FROM users WHERE id = '$user_id'");
    $user_data = mysqli_fetch_assoc($query);
    $full_name = explode(' ', $user_data['full_name']);
    $user_data['first'] = $full_name[0];
    //$user_data['last'] = $full_name[1];
}
//var_dump($user_data);

if (isset($_SESSION['success_flash'])) {
    echo '<div class="bg-success"><p class="text-success text-center">' . $_SESSION['success_flash'] . '</p></div>';
    unset($_SESSION['success_flash']);
}
if (isset($_SESSION['error_flash'])) {
    echo '<div class="bg-danger"><p class="text-danger text-center">' . $_SESSION['error_flash'] . '</p></div>';
    unset($_SESSION['error_flash']);
}

//session_destroy();


