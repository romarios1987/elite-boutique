<?php
require_once 'core/init.php';
include 'includes/head.php';
include 'includes/header_partial.php';
include 'includes/navigation.php';

if ($cart_id != '') {
    $cart_query = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
    $res = mysqli_fetch_assoc($cart_query);
    $items = json_decode($res['items'], true);
    $i = 1;
    $sub_total = 0;
    $item_count = 0;
}


//var_dump($cart_id );

?>



<!--<div class="col-md-12">
    <div class="row">-->


        <h2 class="text-center">Мои заказы</h2>
        <hr>
        <?php if ($cart_id == ''): ?>
            <div class="bg-danger">
                <p class="text-center text-danger">Ваша корзина пуста!</p>
            </div>
        <?php else: ?>
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                <th>#</th>
                <th>Заказ</th>
                <th>Цена</th>
                <th>Количество</th>
                <th>Размер</th>
                <th>Промежуточный итог</th>
                </thead>
                <tbody>
                <?php
                foreach ($items as $item) {
                    $product_id = $item['id'];
                    $product_query = $db->query("SELECT * FROM products WHERE id = '{$product_id}'");
                    $product = mysqli_fetch_assoc($product_query);
                    $size_array = explode(',', $product['sizes']);
                    foreach ($size_array as $sizes_string) {
                        $s = explode(':', $sizes_string);
                        if ($s[0] == $item['size']) {
                            $available = $s[1];
                        }
                    }
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= $product['title']; ?></td>
                        <td><?= money($product['price']); ?></td>
                        <td>
                            <button class="btn btn-default btn-xs"
                                    onclick="update_cart('removeone','<?= $product['id']; ?>','<?= $item['size']; ?>' );">
                                -
                            </button>
                            <?= $item['quantity']; ?>
                            <?php if ($item['quantity'] < $available): ?>
                            <button class="btn btn-default btn-xs"
                                    onclick="update_cart('addone','<?= $product['id']; ?>','<?= $item['size']; ?>' );">
                                +
                            </button>
                            <?php else: ?>
                                <span class="text-danger">Max</span>

                        <?php endif; ?>
                        </td>
                        <td><?= $item['size']; ?></td>
                        <td><?= money($item['quantity'] * $product['price']); ?></td>
                    </tr>
                    <?php
                    $i++;
                    $item_count += $item['quantity'];
                    $sub_total += ($product['price'] * $item['quantity']);
                } // foreach ($items as $item)
                $tax = TAXRATE * $sub_total;
                $tax = number_format($tax, 2);
                $grand_total = $tax + $sub_total;
                ?>
                </tbody>

            </table>
            <table class="table table-bordered table-condensed text-right">
                <legend>Итого</legend>
                <thead class="totals-table-header">
                <th>Всего заказов</th>
                <th>Промежуточный итог</th>
                <th>Налоги</th>
                <th>Общая сумма</th>
                </thead>
                <tbody>
                <tr>
                    <td><?= $item_count; ?></td>
                    <td><?= money($sub_total); ?></td>
                    <td><?= money($tax); ?></td>
                    <td class="bg-success"><?= money($grand_total); ?></td>
                </tr>
                </tbody>
            </table>
            <!-- Check Out Button -->
            <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#checkOutkModal">
                <span class="glyphicon glyphicon-shopping-cart"></span> Check Out >>
            </button>

            <!-- Modal -->
            <div class="modal fade" id="checkOutkModal" tabindex="-1" role="dialog"
                 aria-labelledby="checkOutkModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="checkOutkModalLabel">Shipping Address</h4>
                        </div>
                        <div class="modal-body">
                            ...
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

<?php
include 'includes/footer.php'; ?>
