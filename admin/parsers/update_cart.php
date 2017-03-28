<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/init.php';
$mode = sanitize($_POST['mode']);
$edit_size = sanitize($_POST['edit_size']);
$edit_id = sanitize($_POST['edit_id']);
$cart_query = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
$res = mysqli_fetch_assoc($cart_query);
$items = json_decode($res['items'], true);
$updated_items = [];
$domain = (($_SERVER['HTTP_HOST'] != 'localhost') ? '.' . $_SERVER['HTTP_HOST'] : false);

if ($mode == 'remove_one') {
    foreach ($items as $item) {
        if ($item['id'] == $edit_id && $item['size'] == $edit_size) {
            $item['quantity'] = $item['quantity'] - 1;
        }
        if ($item['quantity'] > 0) {
            $updated_items[] = $item;
        }
    }
}


if ($mode == 'add_one') {
    foreach ($items as $item) {
        if ($item['id'] == $edit_id && $item['size'] == $edit_size) {
            $item['quantity'] = $item['quantity'] + 1;
        }
        $updated_items[] = $item;
    }
}

if (!empty($updated_items)) {
    $json_updated = json_encode($updated_items);
    $db->query("UPDATE cart SET items = '{$json_updated}' WHERE id = '{$cart_id}'");
    $_SESSION['success_flash'] = 'Ваша корзина обновлена'; //'Your shopping cart has been updated!';
}
if (empty($updated_items)) {
    $db->query("DELETE FROM cart WHERE id = '{$cart_id}'");
    setcookie(CART_COOKIE, '', 1, '/', $domain, false);
}
