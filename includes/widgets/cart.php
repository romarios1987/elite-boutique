<h3 class="text-center">Корзина</h3>
<div class="">
    <?php if (empty($cart_id)): ?>
        <p>Ваша корзина пуста!</p>
    <?php else:
        $cart_query = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
        $res = mysqli_fetch_assoc($cart_query);
        $items = json_decode($res['items'], true);
        $i = 1;
        $sub_total = 0;
        ?>
        <table class="table table-condensed" id="cart_widget">
            <tbody>
            <?php foreach ($items as $item):
                $product_query = $db->query("SELECT * FROM products WHERE id = '{$item['id']}'");
                $product = mysqli_fetch_assoc($product_query);
                ?>
                <tr>
                    <td><?= $item['quantity']; ?></td>
                    <td><?= $product['title']; ?></td>
                    <td><?= money($item['quantity'] * $product['price']); ?></td>
                </tr>
                <?php
                $i++;
                $sub_total += ($item['quantity'] * $product['price']);
            endforeach; ?>
            <tr>
                <td></td>
                <td><b>Общая сумма</b></td>
                <td><?= money($sub_total); ?></td>
            </tr>
            </tbody>
        </table>
        <a href="cart.php" class="btn btn-xs btn-primary pull-right">Просмотр корзины</a>
        <div class="clearfix"></div>
    <?php endif; ?>
</div>