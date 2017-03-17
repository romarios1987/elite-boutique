<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/init.php';
$product_id = sanitize($_POST['product_id']);
$size = sanitize($_POST['size']);
$available = sanitize($_POST['available']);
$quantity = sanitize($_POST['quantity']);
$item = [];
$item[] = [
    'id' => $product_id,
    'size' => $size,
    'quantity' => $quantity,
];
$domain = (($_SERVER['HTTP_HOST'] != 'localhost') ? '.' . $_SERVER['HTTP_HOST'] : false);
$query = $db->query("SELECT * FROM products WHERE id = '{$product_id}'");
$product = mysqli_fetch_assoc($query);
$_SESSION['success_flash'] = $product['title'] . 'was added to your cart';

//check to see if the cart cookie exists
if ($cart_id != '') {
    $cart_query = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
    $cart = mysqli_fetch_assoc($cart_query);
    $previous_items = json_decode($cart['items'], true);
    $item_match = 0;
    $new_items = [];
    foreach ($previous_items as $p_item) {
        if ($item[0]['id'] == $p_item['id'] && $item['size'] == $p_item['size']) {
            $p_item['quantity'] = $p_item['quantity'] + $item[0]['quantity'];
            if ($p_item['quantity'] > $available) {
                $p_item['quantity'] = $available;
            }
            $item_match = 1;
        }
        $new_items[] = $p_item;
    }
    if ($item_match != 1) {
        $new_items = array_merge($item, $previous_items);
    }
    $items_json = json_encode($new_items);
    $cart_expire = date("Y-m-d H:i:s", strtotime("+30 days"));
    $db->query("UPDATE cart SET items = '{$items_json}', expire_date = '{$cart_expire}' WHERE  id = '{$cart_id}'");
    setcookie(CART_COOKIE, '', 1, "/", $domain, false);
    setcookie(CART_COOKIE, $cart_id, CART_COOKIE_EXPIRE, "/", $domain, false);

} else {
    // add the cart to the database and set cookie
    $items_json = json_encode($item);
    $cart_expire = date("Y-m-d H:i:s", strtotime("+30 days"));
    $db->query("INSERT INTO cart (items, expire_date) VALUES ('{$items_json}', '{$cart_expire}')");
    $cart_id = $db->insert_id;
    setcookie(CART_COOKIE, "$cart_id", CART_COOKIE_EXPIRE, "/", $domain, false);
}


