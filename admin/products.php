<?php
ob_start();
require_once '../core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';

if (!is_logged_in()) {
    login_error_redirect();
}

// Добавления товара
if (isset($_GET['add']) || isset($_GET['edit'])) {
    $brand_query = $db->query("SELECT * FROM brand ORDER BY brand");
    $parent_query = $db->query("SELECT * FROM categories WHERE parent = 0 ORDER BY category");

    $title = ((isset($_POST['title']) && $_POST['title'] != '') ? sanitize($_POST['title']) : '');
    $brand = ((isset($_POST['brand']) && !empty($_POST['brand'])) ? sanitize($_POST['brand']) : '');
    $parent = ((isset($_POST['parent']) && !empty($_POST['parent'])) ? sanitize($_POST['parent']) : '');
    $category = ((isset($_POST['child']) && !empty($_POST['child'])) ? sanitize($_POST['child']) : '');
    $price = ((isset($_POST['price']) && $_POST['price'] != '') ? sanitize($_POST['price']) : '');

    $list_price = ((isset($_POST['list_price']) && $_POST['list_price'] != '') ? sanitize($_POST['list_price']) : '');
    $description = ((isset($_POST['description']) && $_POST['description'] != '') ? sanitize($_POST['description']) : '');

    $sizes = ((isset($_POST['sizes']) && $_POST['sizes'] != '') ? sanitize($_POST['sizes']) : '');
    $sizes = rtrim($sizes, ',');
    $saved_img = '';

    if (isset($_GET['edit'])) {
        $edit_id = (int)$_GET['edit'];
        $product_results = $db->query("SELECT * FROM products WHERE id = '$edit_id'");
        $product = mysqli_fetch_assoc($product_results);
        if (isset($_GET['delete_image'])) {
            $img_inc = (int)$_GET['img_inc'] - 1;
            $images = explode(',', $product['image']);
            $image_url = $_SERVER['DOCUMENT_ROOT'] . $images[$img_inc];
            unlink($image_url);
            unset($images[$img_inc]);
            $image_string = implode(',', $images);
            $db->query("UPDATE products SET image = '{$image_string}' WHERE id = '$edit_id'");
            header('Location: products.php?edit=' . $edit_id);
        }
        $category = ((isset($_POST['child']) && $_POST['child'] != '') ? sanitize($_POST['child']) : $product['categories']);
        $title = ((isset($_POST['title']) && !empty($_POST['title'])) ? sanitize($_POST['title']) : $product['title']);
        $brand = ((isset($_POST['brand']) && !empty($_POST['brand'])) ? sanitize($_POST['brand']) : $product['brand']);
        $parent_q = $db->query("SELECT * FROM categories WHERE id = '$category'");
        $parent_result = mysqli_fetch_assoc($parent_q);
        $parent = ((isset($_POST['parent']) && !empty($_POST['parent'])) ? sanitize($_POST['parent']) : $parent_result['parent']);
        $price = ((isset($_POST['price']) && !empty($_POST['price'])) ? sanitize($_POST['price']) : $product['price']);
        $list_price = ((isset($_POST['list_price'])) ? sanitize($_POST['list_price']) : $product['list_price']);
        $description = ((isset($_POST['description'])) ? sanitize($_POST['description']) : $product['description']);
        $sizes = ((isset($_POST['sizes']) && !empty($_POST['sizes'])) ? sanitize($_POST['sizes']) : $product['sizes']);
        $sizes = rtrim($sizes, ',');
        $saved_img = (($product['image'] != '') ? $product['image'] : '');
        $db_path = $saved_img;
    }
    if (!empty($sizes)) {
        $sizeString = sanitize($sizes);
        $sizeString = rtrim($sizeString, ',');
        $sizes_array = explode(',', $sizeString);
        $s_array = [];
        $q_array = [];
        foreach ($sizes_array as $size_string) {
            $s = explode(':', $size_string);
            $s_array[] = $s[0];
            $q_array[] = $s[1];
        }
    }///if (!empty($_POST['sizes']))
    else {
        $sizes_array = [];
    }


    if ($_POST) {
        $errors = [];

        // Валидация
        $required = ['title', 'brand', 'price', 'parent', 'child', 'sizes'];
        $allowed = ['png', 'jpg', 'jpeg', 'gif'];
        $tmp_location = [];
        $upload_path = [];
        foreach ($required as $field) {
            if ($_POST[$field] == '') {
                $errors[] = 'Все поля обязательные для заполнения';
                break;
            }
        }
        $photo_count = count($_FILES['photo']['name']);
        if ($photo_count > 0) {
            for ($i = 0; $i < $photo_count; $i++) {
                $name = $_FILES['photo']['name'][$i];
                $name_array = explode('.', $name);
                $file_name = $name_array[0];
                $file_ext = $name_array[1];
                $mime = explode('/', $_FILES['photo']['type'][$i]);
                $mime_type = $mime[0];
                $mime_ext = $mime[1];
                $tmp_location[] = $_FILES['photo']['tmp_name'][$i];
                $file_size = $_FILES['photo']['size'][$i];
                $upload_name = md5(microtime()) . '.' . $file_ext;
                $upload_path[] = BASE_URL . '/images/products/' . $upload_name;
                if ($i != 0) {
                    $db_path .= ',';
                }
                $db_path .= '/images/products/' . $upload_name;


                if ($mime_type != 'image') {
                    $errors[] = "Файл должен быть изображение";
                }
                if (!in_array($file_ext, $allowed)) {
                    $errors[] = 'Расширение файла должно быть PNG, JPG, JPEG или GIF.';
                }
                if ($file_size > 15000000) {
                    $errors[] = 'Размер файла не должен превышать 15MB.';
                }
                if ($file_ext != $mime_ext && ($mime_ext == 'jpeg' && $file_ext != 'jpg')) {
                    $errors[] = 'Расширение файла не соответствует файл';
                }

            } //for
        }///if ($_FILES['photo']['name']

        if (!empty($errors)) {
            echo display_errors($errors);
        } else {
            // загрузить файл вставки в базу данных
            if ($photo_count > 0) {
                for ($i = 0; $i < $photo_count; $i++) {
                    move_uploaded_file($tmp_location[$i], $upload_path[$i]);
                }
            }
            $insert_sql = "INSERT INTO products (title, price, list_price, brand, categories, sizes, description, image) 
                          VALUES ('$title', '$price', '$list_price', '$brand', '$category', '$sizes', '$description', '$db_path')";
            if (isset($_GET['edit'])) {
                $insert_sql = "UPDATE products SET title = '$title', price = '$price', list_price = '$list_price', brand = '$brand', categories = '$category', sizes = '$sizes', image = '$db_path', description = '$description' WHERE id = '$edit_id'";
            }
            $db->query($insert_sql);
            header('Location: products.php');
        }


    } ///if ($_POST)


    ?>
    <h2 class="text-center"><?= ((isset($_GET['edit'])) ? 'Редактировать ' : 'Добавить новый'); ?> товар</h2>
    <hr>
    <form action="products.php?<?= ((isset($_GET['edit'])) ? 'edit=' . $edit_id : 'add=1'); ?>" method="post"
          enctype="multipart/form-data">
        <div class="form-group col-md-3">
            <label for="title">Название*:</label>
            <input type="text" name="title" id="title" class="form-control"
                   value="<?= $title; ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="brand">Бренд*:</label>
            <select class="form-control" name="brand" id="brand">
                <option value=""<?= (($brand == '') ? ' selected' : ''); ?>></option>
                <?php while ($b = mysqli_fetch_assoc($brand_query)): ?>
                    <option value="<?= ($b['id']) ?>"<?= (($brand == $b['id']) ? ' selected' : ''); ?>><?= $b['brand']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="parent">Родительская категория*:</label>
            <select class="form-control" name="parent" id="parent">
                <option value=""<?= (($parent == '') ? ' selected' : ''); ?>></option>
                <?php while ($p = mysqli_fetch_assoc($parent_query)): ?>
                    <option value="<?= $p['id'] ?>"<?= (($parent == $p['id']) ? ' selected' : ''); ?>><?= $p['category']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="child">Дочерняя категория*:</label>
            <select class="form-control" name="child" id="child"></select>
        </div>
        <div class="form-group col-md-3">
            <label for="price">Цена*:</label>
            <input type="text" name="price" id="price" class="form-control"
                   value="<?= $price; ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="list_price">Старая цена:</label>
            <input type="text" name="list_price" id="list_price" class="form-control"
                   value="<?= $list_price; ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="">Количество и размеры*:</label>
            <button class="btn btn-default form-control" onclick="jQuery('#sizesModal').modal('toggle'); return false;">
                Quantity & Sizes
            </button>
        </div>
        <div class="form-group col-md-3">
            <label for="sizes">Количество и размеры Preview</label>
            <input type="text" name="sizes" id="sizes" class="form-control"
                   value="<?= $sizes; ?>" readonly>
        </div>
        <div class="form-group col-md-6">
            <?php if ($saved_img != ''): ?>
                <?php $img_inc = 1;
                $images = explode(',', $saved_img); ?>
                <?php foreach ($images as $image): ?>
                    <div class="saved-image col-md-4"><img src="<?=$image;?>" alt="saved image"><br>
                        <a href="products.php?delete_image=1&edit=<?= $edit_id; ?>&img_inc=<?=$img_inc;?>" class="text-danger">Удалить фото</a>
                    </div>
                <?php
                $img_inc++;
                endforeach; ?>
            <?php else: ?>
                <label for="photo">Фотография продукта:</label>
                <input type="file" name="photo[]" id="photo" class="form-control" multiple>
            <?php endif; ?>
        </div>
        <div class="form-group col-md-6">
            <label for="description">Описание продукта:</label>
            <textarea name="description" id="description" class="form-control" rows="6">
                <?= $description; ?>
            </textarea>
        </div>
        <div class="form-group pull-right">
            <a href="products.php" class="btn btn-default">Отмена</a>
            <input type="submit" class="btn btn-success"
                   value="<?= ((isset($_GET['edit'])) ? 'Редактировать ' : 'Добавить новый'); ?> продукт">
        </div>
        <div class="clearfix"></div>
    </form>

    <!-- Modal -->
    <div class="modal fade" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="sizesModalLabel">Sizes & Quantity</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <div class="form-group col-md-4">
                                <label for="size<?= $i; ?>">Size:</label>
                                <input type="text" name="size<?= $i; ?>" id="size<?= $i; ?>"
                                       value="<?= ((!empty($s_array[$i - 1])) ? $s_array[$i - 1] : ''); ?>"
                                       class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="quantity<?= $i; ?>">Quantity:</label>
                                <input type="number" name="quantity<?= $i; ?>" id="quantity<?= $i; ?>"
                                       value="<?= ((!empty($q_array[$i - 1])) ? $q_array[$i - 1] : ''); ?>" min="0"
                                       class="form-control">
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary"
                            onclick="updateSizes();jQuery('#sizesModal').modal('toggle'); return false;">Сохранить
                        изменения
                    </button>
                </div>
            </div>
        </div>
    </div>


<?php } ///if (isset($_GET['add'])

else {
    ///delete products
    if (isset($_GET['delete'])) {
        $del_id = sanitize($_GET['delete']);
        $product_results = $db->query("SELECT * FROM products WHERE id = '$del_id'");
        $product = mysqli_fetch_assoc($product_results);
        $image_url = $_SERVER['DOCUMENT_ROOT'] . $product['image'];
        unlink($image_url);
        $db->query("UPDATE products SET image = '' WHERE id = '$del_id'");
        $db->query("DELETE FROM products WHERE id = '$del_id'");
        //$db->query("UPDATE products SET deleted = 1 WHERE id = '$id'");
        header('Location: products.php');

    }
    $db_path = '';

    $query = "SELECT * FROM products WHERE deleted = 0 ";
    $product_result = $db->query($query);
    if (isset($_GET['featured'])) {
        $id = (int)$_GET['id'];
        $featured = (int)$_GET['featured'];
        $featured_sql = "UPDATE products SET featured = '$featured' WHERE id = '$id'";
        $db->query($featured_sql);
        header('Location: products.php');
    }


    ?>
    <h2 class="text-center">Товары</h2>
    <a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Добавить товар</a>
    <div class="clearfix"></div>
    <hr>
    <table class="table table-bordered table-condensed table-striped">
        <thead>
        <th></th>
        <th>Товар</th>
        <th>Цена</th>
        <th>Категория</th>
        <th>Избранные</th>
        <th>Продан</th> <!--sold-->
        </thead>
        <tbody>
        <?php while ($product = mysqli_fetch_assoc($product_result)):
            $child_id = $product['categories'];
            $cat_sql = "SELECT * FROM categories WHERE id = '$child_id'";
            $result = $db->query($cat_sql);
            $child = mysqli_fetch_assoc($result);
            $parent_id = $child['parent'];
            $parent_sql = "SELECT * FROM categories WHERE id = '$parent_id'";
            $parent_result = $db->query($parent_sql);
            $parent = mysqli_fetch_assoc($parent_result);
            $category = $parent['category'] . ' ~ ' . $child['category'];
            ?>
            <tr>
                <td>
                    <a href="products.php?edit=<?= $product['id']; ?>" class="btn btn-xs btn-default"><span
                                class="glyphicon glyphicon-pencil"></span></a>
                    <a href="products.php?delete=<?= $product['id']; ?>" class="btn btn-xs btn-default"><span
                                class="glyphicon glyphicon-remove-sign"></span></a>
                </td>
                <td><?= $product['title']; ?></td>
                <td><?= money($product['price']) ?></td>
                <td><?= $category; ?></td>
                <td>
                    <a href="products.php?featured=<?= (($product['featured'] == 0) ? '1' : '0'); ?>&id=<?= $product['id'] ?>"
                       class="btn btn-xs btn-default"><span
                                class="glyphicon glyphicon-<?= (($product['featured'] == 1) ? 'minus' : 'plus'); ?>"></span></a>&nbsp <?= (($product['featured'] == 1) ? 'Featured Product' : ''); ?>
                </td>
                <td>0</td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>


<?php } // End else
include 'includes/footer.php';
ob_end_flush();
?>
<script>
    $('document').ready(function () {
        getChildOption('<?=$category;?>');
    });
</script>
