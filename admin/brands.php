<?php
require_once '../core/init.php';


if (!is_logged_in()) {
    login_error_redirect();
}

//get brands from database
$query = "SELECT * FROM brand ORDER BY brand";
$result = $db->query($query);
$errors = [];

// Edit brand
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_id = sanitize($edit_id);
    $query2 = "SELECT * FROM brand WHERE id = '$edit_id'";
    $edit_result = $db->query($query2);
    $eBrand = mysqli_fetch_assoc($edit_result);
}

// Delete brand
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_id = sanitize($delete_id);
    $query = "DELETE FROM brand WHERE id = '$delete_id'";
    $db->query($query);
    header('Location: brands.php');
}


//If add form submitted (Если добавить форма будет отправлена)
if (isset($_POST['add_submit'])) {
    $brand = sanitize($_POST['brand']);
    // check if brand is blank(проверить, если бренд является пустым)
    if ($_POST['brand'] == '') {
        $errors[] .= 'Вы должны ввести марку'; //'You must enter a brand';
    }
    // check if brand exists in database (проверить, если бренд существует в базе данных)
    $query = "SELECT * FROM brand WHERE brand = '$brand'";
    if (isset($_GET['edit'])) {
        $query = "SELECT * FROM brand WHERE brand = '$brand' AND id != '$edit_id'";
    }
    $result = $db->query($query);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
        $errors[] .= $brand . ' Этот бренд уже существует. Пожалуйста, выберите другую марку'; //brand already exists. Please chose another brand name ...';
    }

    // display errors
    if (!empty($errors)) {
        echo display_errors($errors);
    } else {
        // Add brand to database
        $query = "INSERT INTO brand (brand) VALUE ('$brand')";
        if (isset($_GET['edit'])) {
            $query = "UPDATE brand SET brand = '$brand' WHERE id = '$edit_id'";
        }
        $db->query($query);
        header('Location: brands.php');
    }
}

include 'includes/head.php';
include 'includes/navigation.php';
?>

    <h2 class="text-center">Бренды</h2>
    <hr>
    <!--Brand form-->
    <div class="text-center">
        <form action="brands.php<?= ((isset($_GET['edit'])) ? '?edit=' . $edit_id : ''); ?>" class="form-inline"
              method="post">
            <div class="form-group">
                <?php
                $brand_value = '';
                if (isset($_GET['edit'])) {
                    $brand_value = $eBrand['brand'];
                } else {
                    if (isset($_POST['brand'])) {
                        $brand_value = sanitize($_POST['brand']);
                    }
                }
                ?>
                <label for="brand"><?= ((isset($_GET['edit'])) ? 'Изменить' : 'Добавить'); ?> Бренд</label>
                <input type="text" name="brand" id="brand" class="form-control" value="<?= $brand_value; ?>">
                <?php if (isset($_GET['edit'])): ?>
                    <a href="brands.php" class="btn btn-default">Закрыть</a>
                <?php endif; ?>
                <input type="submit" name="add_submit"
                       value="<?= ((isset($_GET['edit'])) ? 'Изменить' : 'Добавить'); ?> Бренд"
                       class="btn btn-md btn-success">
            </div>
        </form>
    </div>
    <hr>

    <table class="table table-bordered table-striped table-auto">
        <thead>
        <th></th>
        <th>Бренд</th>
        <th></th>
        </thead>
        <tbody>
        <?php while ($brand = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><a href="brands.php?edit=<?= $brand['id']; ?>" class="btn btn-xs btn-default"><span
                                class="glyphicon glyphicon-pencil"></span></a></td>
                <td><?= $brand['brand']; ?></td>
                <td><a href="brands.php?delete=<?= $brand['id']; ?>" class="btn btn-xs btn-default"><span
                                class="glyphicon glyphicon-remove-sign"></span></a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php include 'includes/footer.php';