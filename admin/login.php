<?php
ob_start();
require_once '../core/init.php';
include 'includes/head.php';

/*
$password = 'password';
$hashed = password_hash($password,PASSWORD_DEFAULT);
echo $hashed;*/

$email = (isset($_POST['email']) ? sanitize($_POST['email']) : '');
$email = trim($email);
$password = (isset($_POST['password']) ? sanitize($_POST['password']) : '');
$password = trim($password);
$errors = [];
?>
<style>
    body {
        background-image: url("/images/login-bg.jpg");
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
</style>

<div id="login-form">
    <div>
        <?php if ($_POST) {
            //form validation
            if (empty($_POST['email']) || empty($_POST['password'])) {
                $errors[] = "Вы должны ввести электронный адрес и пароль.";
            }
            //validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Введите коректный email";
            }

            // password is more than 6 characters
            if (strlen($password) < 6) {
                $errors[] = "Пароль должен иметь мин 6 символов.";
            }

            // check email exists in the database
            $query = $db->query("SELECT * FROM users WHERE email = '$email'");
            $user = mysqli_fetch_assoc($query);
            $userCount = mysqli_num_rows($query);
            if ($userCount < 1) {
                $errors[] = 'Такого электронного адреса нет в базе данных';
            }

            if (!password_verify($password, $user['password'])) {
                $errors[] = "Пароль не совпадает с зарегистрированным";//The password does not match our records
            }

            // check for errors
            if (!empty($errors)) {
                echo display_errors($errors);
            } else {
                //log user in
                $user_id = $user['id'];
                login($user_id);
            }
        }

        ?>
    </div>
    <h2 class="text-center">Авторизоваться</h2>
    <hr>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" name="email" id="email" class="form-control" value="<?= $email; ?>">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" value="<?= $password; ?>">
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Вход">
            <p class="text-right"><a href="../index.php">Посетить сайт</a></p>
        </div>
    </form>

</div><!--#/login-form-->


<?php //include 'includes/footer.php';
ob_end_flush();
?>
