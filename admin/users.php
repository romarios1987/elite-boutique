<?php
ob_start();
require_once '../core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';
if (!is_logged_in()) {
    login_error_redirect();
}
if (!has_permission('admin')) {
    permission_error_redirect('index.php');
}

//Удаления пользователя
if (isset($_GET['delete'])) {
    $delete_id = sanitize($_GET['delete']);
    $db->query("DELETE FROM users WHERE id = '$delete_id'");
    $_SESSION['success_flash'] = 'Пользователь удален'; //"User has been deleted";
    header('Location: users.php');
}
if (isset($_GET['add'])) {
    $name = ((isset($_POST['name'])) ? sanitize($_POST['name']) : '');
    $email = ((isset($_POST['email'])) ? sanitize($_POST['email']) : '');
    $password = ((isset($_POST['password'])) ? sanitize($_POST['password']) : '');
    $confirm = ((isset($_POST['confirm'])) ? sanitize($_POST['confirm']) : '');
    $permissions = ((isset($_POST['permissions'])) ? sanitize($_POST['permissions']) : '');

    $errors = [];

    if ($_POST) {

        // Валидация данных вввода


        //проверка емайла
        $email_query = $db->query("SELECT * FROM users WHERE email = '$email'");
        $email_count = mysqli_num_rows($email_query);

        if ($email_count != 0) {
            $errors[] = 'Этот адрес электронной почты уже существует в нашей базе данных'; //'That email already exists in our database.';
        }

        //Заполнение всех полей
        $required = ['name', 'email', 'password', 'confirm', 'permissions'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $errors[] = 'Вы должны заполнить все поля';  //You must fill all fields
                break;
            }
        }

        // password is more than 6 characters
        if (strlen($password) < 6) {
            $errors[] = 'Пароль должен иметь мин 6 символов.';
        }
        if ($password != $confirm) {
            $errors[] = 'Your password do not match.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Вы должны ввести валидный адрес электронной почты'; //'You must enter a valid email.';
        }

        if (!empty($errors)) {
            echo display_errors($errors);
        } else {
            //Add user to database
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $db->query("INSERT INTO users (full_name,email,password,permissions) VALUES ('$name','$email','$hashed','$permissions')");
            $_SESSION['success_flash'] = 'Пользователь добавлен'; //'User has been added!';
            header('Location: users.php');
        }
    } //if ($_POST)
    ?>

    <h2 class="text-center">Добавить нового пользователя</h2>
    <hr>
    <form action="users.php?add=1" method="post">
        <div class="form-group col-md-6">
            <label for="name">Полное имя:</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= $name; ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="email">Email:</label>
            <input type="text" name="email" id="email" class="form-control" value="<?= $email; ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" class="form-control" value="<?= $password; ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="confirm">Подтвердите пароль:</label>
            <input type="password" name="confirm" id="confirm" class="form-control" value="<?= $confirm; ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="confirm">Разрешения:</label>
            <select class="form-control" name="permissions" id="permissions">
                <option value=""<?= (($permissions == '') ? ' selected' : ''); ?>></option>
                <option value="editor"<?= (($permissions == 'editor') ? ' selected' : ''); ?>>Editor</option>
                <option value="admin,editor"<?= (($permissions == 'admin,editor') ? ' selected' : ''); ?>>Admin,Editor
                </option>
            </select>
        </div>
        <div class="form-group col-md-6 text-right" style="margin-top: 25px;">
            <a href="users.php" class="btn btn-default">Отмена</a>
            <input type="submit" value="Добавить пользователя" class="btn btn-primary">
        </div>
    </form>
<?php } //(isset($_GET['add'])
else {
    $user_query = $db->query("SELECT * FROM users ORDER BY full_name"); ?>
    <h2 class="text-center">Пользователи</h2>
    <a href="users.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Добавить нового пользователя</a>
    <div class="clearfix"></div>
    <hr>
    <table class="table table-bordered table-striped table-condensed">
        <thead>
        <th></th>
        <th>Имя</th>
        <th>Email</th>
        <th>Дате вступления</th><!--Join Date-->
        <th>Последний Вход</th><!--Last Login-->
        <th>Разрешения</th>
        </thead>
        <tbody>
        <?php while ($user = mysqli_fetch_assoc($user_query)): ?>
            <tr>
                <td>
                    <?php if ($user['id'] != $user_data['id']): ?>
                        <a href="users.php?delete=<?= $user['id'] ?>" class="btn btn-default btn-xs"><span
                                    class="glyphicon glyphicon-remove-sign"></span></a>
                    <?php endif; ?>
                </td>
                <td><?= $user['full_name']; ?></td>
                <td><?= $user['email']; ?></td>
                <td><?= pretty_date($user['join_date']); ?></td>
                <td><?= (($user['last_login'] == '0000-00-00 00:00:00') ? 'Never' : pretty_date($user['last_login'])); ?></td>
                <td><?= $user['permissions']; ?></td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

<?php }
include 'includes/footer.php';
ob_end_flush();
?>