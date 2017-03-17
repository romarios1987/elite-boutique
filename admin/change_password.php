<?php
ob_start();
require_once '../core/init.php';
include 'includes/head.php';
if (!is_logged_in()) {
    login_error_redirect();
}

$hashed = $user_data['password'];
$old_password = (isset($_POST['old_password']) ? sanitize($_POST['old_password']) : '');
$old_password = trim($old_password);

$confirm = (isset($_POST['confirm']) ? sanitize($_POST['confirm']) : '');
$confirm = trim($confirm);

$password = (isset($_POST['password']) ? sanitize($_POST['password']) : '');
$password = trim($password);

$new_hashed = password_hash($password, PASSWORD_DEFAULT);
$user_id = $user_data['id'];

$errors = [];
?>
<div id="login-form">
    <div>
        <?php if ($_POST) {
            //form validation
            if (empty($_POST['old_password']) || empty($_POST['password']) || empty($_POST['confirm'])) {
                $errors[] = "Вы должны заполнить все поля!";
            }

            // password is more than 6 characters
            if (strlen($password) < 6) {
                $errors[] = "Пароль должен иметь мин 6 символов.";
            }
            // if new password matches confirm
            if ($password != $confirm) {
                $errors[] = "Пароль не соответствует нашим записям. Пожалуйста, попробуйте еще раз"; //The password does not match our records. Please try again
            }


            if (!password_verify($old_password, $hashed)) {
                $errors[] = "Старый пароль не совпадает с зарегистрированным"; //The password does not match our records
            }

            // check for errors
            if (!empty($errors)) {
                echo display_errors($errors);
            } else {
                //change password
                $db->query("UPDATE users SET password = '$new_hashed' WHERE id = '$user_id'");
                $_SESSION['success_flash'] = "Ваш пароль обновлен!";
                header('Location: index.php');

            }
        }


        ?>
    </div>
    <h2 class="text-center">Change password</h2>
    <hr>
    <form action="change_password.php" method="post">
        <div class="form-group">
            <label for="old_password">Old Password:</label>
            <input type="password" name="old_password" id="old_password" class="form-control"
                   value="<?= $old_password; ?>">
        </div>
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" name="password" id="password" class="form-control" value="<?= $password; ?>">
        </div>
        <div class="form-group">
            <label for="confirm">Confirm New Password:</label>
            <input type="password" name="confirm" id="confirm" class="form-control" value="<?= $confirm; ?>">
        </div>
        <div class="form-group">
            <a href="index.php" class="btn btn-default">Cancel</a>
            <input type="submit" class="btn btn-primary" value="Login">
        </div>
    </form>
    <p class="text-right"><a href="../index.php">Visit Site</a></p>
</div><!--#/login-form-->


<?php include 'includes/footer.php';
ob_end_flush();
?>
