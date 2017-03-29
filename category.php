<?php
require_once 'core/init.php';
include 'includes/head.php';
/*include 'includes/navigation.php';*/
include 'includes/header_partial.php';
include 'includes/left_bar.php';


if (isset($_GET['cat'])) {
    $cat_id = sanitize($_GET['cat']);
} else {
    $cat_id = '';
}

$query = "SELECT * FROM products WHERE categories = '$cat_id'";
$productQ = $db->query($query);
$category = get_category($cat_id);

?>
    <!--Main Content-->
    <div class="col-md-8 col-sm-12">
        <div class="row">
            <div class="wrap-products clearfix">
                <h2 class="text-center"
                    style="margin-bottom: 20px;"><?='<span class="par_cat">' . $category['parent'] . '</span>' . ' ' . $category['child']; ?></h2>
                <?php while ($product = mysqli_fetch_assoc($productQ)): ?>
                    <div class="col-md-4 col-sm-4 col-xs-6">
                        <div class="product-item">
                            <h4><?= $product['title'] ?></h4>
                            <?php $photos = explode(',', $product['image']); ?>
                            <img src="<?= $photos[0] ?>" alt="<?= $product['title'] ?>" class="img-thumb">
                            <p class="list-price text-danger">Старая цена: <s><?= $product['list_price'] ?></s> руб</p>
                            <p class="price">Цена: <?= $product['price'] ?> руб</p>
                            <button type="button" class="btn btn-sm btn-success"
                                    onclick="detailsModal(<?= $product['id']; ?>)">Детали
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div><!--wrap-products-->
        </div><!--row-->
    </div>
    <!--Main Content-->

<?php
include 'includes/right_bar.php';
include 'includes/footer.php';
?>