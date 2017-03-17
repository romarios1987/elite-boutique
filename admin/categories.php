<?php
ob_start();
require_once '../core/init.php';
include 'includes/head.php';
if (!is_logged_in()) {
    login_error_redirect();
}
$query = "SELECT * FROM categories WHERE parent = 0";
$result = $db->query($query);
$errors = [];
$category = '';
$post_parent = '';

/*** Edit Category ***/
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_id = sanitize($edit_id);
    $edit_sql = "SELECT * FROM categories WHERE id = '$edit_id'";
    $edit_result = $db->query($edit_sql);
    $edit_category = mysqli_fetch_assoc($edit_result);
}


/*** Delete Category ***/
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $delete_id = sanitize($delete_id);

    $query = "SELECT * FROM categories WHERE id = '$delete_id'";
    $result_s = $db->query($query);
    $category = mysqli_fetch_assoc($result_s);

    if ($category['parent'] == 0) {
        $query = "DELETE FROM categories WHERE parent = '$delete_id'";
    }
    $db->query($query);

    $del_sql = "DELETE FROM categories WHERE id = '$delete_id'";
    $db->query($del_sql);
    header('Location: categories.php');

}

// Process Form
if (isset($_POST) && !empty($_POST)) {
    $post_parent = sanitize($_POST['parent']);
    $category = sanitize($_POST['category']);
    $sql_form = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent'";
    if (isset($_GET['edit'])) {
        $id = $edit_category['id'];
        $sql_form = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent' AND id != '$id'";
    }
    $form_result = $db->query($sql_form);
    $count = mysqli_num_rows($form_result);
    // Check category is brand
    if ($category == '') {
        $errors[] .= 'Категорию нельзя оставлять пустыми'; //"The category cannot be left blank";
    }
    // if exists if the database
    if ($count > 0) {
        $errors[] .= $category . ' уже существует. Выберите новую категорию'; //' already exists. Please choose a new category';
    }


// Display errors update database
    if (!empty($errors)) {
        // display errors
        $display = display_errors($errors); ?>
        <script src="../js/jquery-3.2.0.min.js"></script>
        <script>
            jQuery(document).ready(function () {
                jQuery('#errors').html('<?=$display;?>');
            });
        </script>

    <?php } else {
        // update database
        $update_sql = "INSERT INTO categories (category, parent) VALUES ('$category', '$post_parent')";
        if (isset($_GET['edit'])) {
            $update_sql = "UPDATE categories SET category = '$category', parent = '$post_parent' WHERE id = '$edit_id'";
        }
        $db->query($update_sql);
        header('Location: categories.php');
    }
}
?>

    <h2 class="text-center">Категории</h2>
    <hr>
    <div class="row">
        <!-- Category Table-->
        <div class="col-md-6">
            <form action="categories.php<?= ((isset($_GET['edit'])) ? '?edit=' . $edit_id : ''); ?>" class="form"
                  method="post">
                <div class="form-group">
                    <legend><?= ((isset($_GET['edit'])) ? 'Изменить' : 'Добавить'); ?> Категорию</legend>
                    <div id="errors"></div>
                    <?php
                    $category_value = '';
                    $parent_value = 0;
                    if (isset($_GET['edit'])) {
                        $category_value = $edit_category['category'];
                        $parent_value = $edit_category['parent'];
                    } else {
                        /* If post is set during edit. */
                        if (isset($_POST)) {
                            $category_value = $category; /* $category is from line 37 */
                            $parent_value = $post_parent; /* $post_parent is from line 36 */
                        }
                    }
                    ?>
                    <label for="parent">Родительская категория</label>
                    <select name="parent" class="form-control" id="parent">
                        <option value="0"<?= (($parent_value == 0) ? ' selected="selected"' : ''); ?>>Родительская категория</option>
                        <?php while ($parent = mysqli_fetch_assoc($result)): ?>
                            <option value="<?= $parent['id']; ?>"<?= (($parent_value == $parent['id']) ? ' selected="selected"' : ''); ?>><?= $parent['category']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="category">Категория</label>
                    <input type="text" class="form-control" id="category" name="category"
                           value="<?= $category_value; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" value="<?= ((isset($_GET['edit'])) ? 'Изменить' : 'Добавить'); ?> Категорию"
                           class="btn btn-success">
                </div>
            </form>
        </div>
        <div class="col-md-6">

            <!-- Category Table-->
            <table class="table table-bordered">
                <thead>
                <th>Категория</th>
                <th>Родительская категория</th>
                <th></th>
                </thead>

                <tbody>
                <?php
                $query = "SELECT * FROM categories WHERE parent = 0";
                $result = $db->query($query);

                while ($parent = mysqli_fetch_assoc($result)) :
                    $parent_id = (int)$parent['id'];
                    $query2 = "SELECT * FROM categories WHERE parent = '$parent_id'";
                    $child_result = $db->query($query2);
                    ?>
                    <tr class="bg-primary">
                        <td><?= $parent['category']; ?></td>
                        <td>Родительская категория</td>
                        <td>
                            <a href="categories.php?edit=<?= $parent['id']; ?>" class="bnt btn-xs btn-default"><span
                                        class="glyphicon glyphicon-pencil"></span></a>
                            <a href="categories.php?delete=<?= $parent['id']; ?>" class="bnt btn-xs btn-default"><span
                                        class="glyphicon glyphicon-remove-sign"></span></a>
                        </td>
                    </tr>

                    <?php while ($child = mysqli_fetch_assoc($child_result)): ?>
                    <tr class="bg-info">
                        <td><?= $child['category']; ?></td>
                        <td><?= $parent['category']; ?></td>
                        <td>
                            <a href="categories.php?edit=<?= $child['id']; ?>" class="bnt btn-xs btn-default"><span
                                        class="glyphicon glyphicon-pencil"></span></a>
                            <a href="categories.php?delete=<?= $child['id']; ?>" class="bnt btn-xs btn-default"><span
                                        class="glyphicon glyphicon-remove-sign"></span></a>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>


<?php include 'includes/footer.php';
ob_end_flush();
?>