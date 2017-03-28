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


<h2 class="text-center" xmlns="http://www.w3.org/1999/html">Мои заказы</h2>
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
                            onclick="update_cart('remove_one','<?= $product['id']; ?>','<?= $item['size']; ?>' );">
                        -
                    </button>
                    <?= $item['quantity']; ?>
                    <?php if ($item['quantity'] < $available): ?>
                        <button class="btn btn-default btn-xs"
                                onclick="update_cart('add_one','<?= $product['id']; ?>','<?= $item['size']; ?>' );">
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
    <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#checkOutModal">
        <span class="glyphicon glyphicon-shopping-cart"></span> Оформить Заказ
    </button>

    <!-- Modal -->
    <div class="modal fade" id="checkOutModal" tabindex="-1" role="dialog" aria-labelledby="checkOutModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="checkOutModalLabel">Адрес доставки</h4><!--Shipping Address-->
                </div><!--/.modal-header-->
                <div class="modal-body">
                    <div class="row">
                        <form action="thank_you.php" method="post" id="payment-form">
                            <span class="bg-danger" id="payment-errors"></span>
                            <div id="step_1" style="display: block">
                                <div class="form-group col-md-6">
                                    <label for="full_name">Полное имя:</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email">Email:</label>
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="street">Адрес улицы:</label><!--Street Address:-->
                                    <input type="text" class="form-control" id="street" name="street">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="street_2">Адрес улицы 2:</label><!--Street Address:-->
                                    <input type="text" class="form-control" id="street_2" name="street2">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="city">Город:</label>
                                    <input type="text" class="form-control" id="city" name="city">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="state">Государство:</label>
                                    <input type="text" class="form-control" id="state" name="state">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="country">Страна:</label>
                                    <input type="text" class="form-control" id="country" name="country">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="zip_code">Почтовый Индекс:</label>
                                    <input type="text" class="form-control" id="zip_code" name="zip_code">
                                </div>
                            </div><!--/#step_1-->

                            <div id="step_2" style="display: none">
                                <div class="form-group col-md-3">
                                    <label for="name">Name on Card:</label>
                                    <input type="text" id="name" class="form-control">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="number">Card Number:</label>
                                    <input type="text" id="number" class="form-control">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="cvc">CVC:</label>
                                    <input type="text" id="cvc" class="form-control">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="exp-month">Expire Month:</label>
                                    <select name="exp-month" id="exp-month" class="form-control">
                                        <option value=""></option>
                                        <?php for ($i = 1; $i < 13; $i++): ?>
                                            <option value="<?= $i; ?>"><?= $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="exp-year">Expire Year:</label>
                                    <select name="exp-year" id="exp-year" class="form-control">
                                        <option value=""></option>
                                        <?php
                                        $year = date("Y");
                                        for ($i = 0; $i < 11; $i++): ?>
                                            <option value="<?= $year + $i; ?>"><?= $year + $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div><!--/#step_2-->
                    </div><!--row-->
                </div><!--/.modal-body-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" onclick="check_address();" id="next_button">Дальше
                    </button>
                    <button type="button" class="btn btn-primary" onclick="back_address();" id="back_button"
                            style="display: none">Назад
                    </button>
                    <button type="submit" class="btn btn-primary" id="check_out_button" style="display: none;">Check
                        Out
                    </button>
                    </form><!--/#payment-form-->

                </div><!--/.-modal-footer-->
            </div><!--/.modal-content-->
        </div><!--/.modal-dialog-->
    </div><!--/#checkOutModal-->
<?php endif; ?>


<script>
    function back_address() {
        $('#payment-errors').html("");
        $('#step_1').css("display", "block");
        $('#step_2').css("display", "none");
        $('#next_button').css("display", "inline-block");
        $('#back_button').css("display", "none");
        $('#check_out_button').css("display", "none");
        $('#checkOutModalLabel').html("Адрес доставки"); //Enter Your Card Details
    }


    function check_address() {
        var data = {
            'full_name': $('#full_name').val(),
            'email': $('#email').val(),
            'street': $('#street').val(),
            'street_2': $('#street_2').val(),
            'city': $('#city').val(),
            'state': $('#state').val(),
            'zip_code': $('#zip_code').val(),
            'country': $('#country').val(),
        };
        $.ajax({
            url: 'admin/parsers/check_address.php',
            method: 'POST',
            data: data,
            success: function (data) {
                if (data != 'passed') {
                    $('#payment-errors').html(data);
                }
                if (data == 'passed') {
                    $('#payment-errors').html("");
                    $('#step_1').css("display", "none");
                    $('#step_2').css("display", "block");
                    $('#next_button').css("display", "none");
                    $('#back_button').css("display", "inline-block");
                    $('#check_out_button').css("display", "inline-block");
                    $('#checkOutModalLabel').html("Введите данные своей карты"); //Enter Your Card Details
                }
            },
            error: function () {
                alert("Что то пошло не так")
            }
        });
    }
</script>


<?php include 'includes/footer.php'; ?>
