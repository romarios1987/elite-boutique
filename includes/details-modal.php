<?php
require_once '../core/init.php';
$id = $_POST['id'];
$id = (int)$id;
$query = "SELECT * FROM products WHERE id = '$id'";
$result = $db->query($query);
$product = mysqli_fetch_assoc($result);

$brand_id = $product['brand'];
$query = "SELECT brand FROM brand WHERE id = '$brand_id'";
$brand_query = $db->query($query);
$brand = mysqli_fetch_assoc($brand_query);

$size_string = $product['sizes'];
$size_array = explode(',', $size_string);
?>

    <!--Details Modal-->
<?php ob_start(); ?>
    <div class="modal fade details-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" onclick="closeModal()" aria-label="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center"><?= $product['title']; ?></h4>
                </div><!--modal-header-->

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <span id="modal_errors" class="bg-danger"></span>
                            <div class="col-sm-6 fotorama">
                                <?php $photos = explode(',', $product['image']);
                                foreach ($photos as $photo): ?>
                                    <div class="center-block">
                                        <img src="<?= $photo; ?>" alt="<?= $product['title']; ?>"
                                             class="details img-responsive">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-sm-6">
                                <h4>Описание товара</h4>
                                <p><?= $product['description']; ?></p>
                                <hr>
                                <p>Цена: <?= $product['price']; ?> руб</p>
                                <p>Бренд: <?= $brand['brand']; ?></p>

                                <form action="/admin/parsers/add_cart.php" method="post" id="add_product_form">
                                    <input type="hidden" name="product_id" value="<?= $id; ?>">
                                    <input type="hidden" name="available" id="available" value="">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="quantity">Количество:</label>
                                                <input type="number" class="form-control" min="0" id="quantity"
                                                       name="quantity">
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="form-group">
                                                <label for="size">Размер: </label>
                                                <select name="size" id="size" class="form-control">
                                                    <option value=""></option>
                                                    <?php foreach ($size_array as $string) {
                                                        $string_array = explode(':', $string);
                                                        $size = $string_array[0];
                                                        $available = $string_array[1];
                                                        echo '<option value="' . $size . '" data-available="' . $available . '">' . $size . ' (' . $available . ' Доступно)</option>';
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div><!--modal-body-->

                <div class="modal-footer">
                    <button class="btn btn-default" onclick="closeModal()">Закрыть</button>
                    <button class="btn btn-warning" type="submit" onclick="add_to_cart(); return false;"><span
                                class="glyphicon glyphicon-shopping-cart"></span>
                        Добавить в корзину
                    </button>
                </div><!--modal-footer-->
            </div><!--modal-content-->
        </div><!--modal-dialog modal-lg-->

    </div><!--modal-->

    <script>
        $('#size').change(function () {
            var available = $('#size option:selected').data("available");
            $('#available').val(available);
        });

        /*fotorama*/
        $(function () {
            $('.fotorama').fotorama({
                'loop' : true,
                'autoplay': true
            });
        });


        /**Ф-я закрития модального окна**/
        function closeModal() {
            $('#details-modal').modal('hide');
            setTimeout(function () {
                $('#details-modal').remove();
                $('#modal-backdrop').remove();
            }, 500);
        }
    </script>
<?php echo ob_get_clean(); ?>