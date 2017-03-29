<?php
require_once 'core/init.php';
include 'includes/head.php';
/*include 'includes/navigation.php';*/
include 'includes/header_partial.php';
include 'includes/left_bar.php';

$sql = "SELECT * FROM products";
$cat_id = (($_POST['cat'] != '') ? sanitize($_POST['cat']) : '');
if ($cat_id == '') {
    $sql .= " WHERE deleted = 0";
} else {
    $sql .= " WHERE categories = '{$cat_id}' AND deleted = 0";
}
$price_sort = (($_POST['price_sort'] != '') ? sanitize($_POST['price_sort']) : '');
$min_price = (($_POST['min_price'] != '') ? sanitize($_POST['min_price']) : '');
$max_price = (($_POST['max_price'] != '') ? sanitize($_POST['max_price']) : '');
$brand = (($_POST['brand'] != '') ? sanitize($_POST['brand']) : '');

if ($min_price != '') {
    $sql .= " AND price >= '{$min_price}'";
}
if ($max_price != '') {
    $sql .= " AND price <= '{$max_price}'";
}

if ($brand != '') {
    $sql .= " AND brand = '{$brand}'";
}

if ($price_sort == 'low') {
    $sql .= " ORDER BY price";
}
if ($price_sort == 'high') {
    $sql .= " ORDER BY price DESC";
}
$productQ = $db->query($sql);
$category = get_category($cat_id);

?>
    <!--Main Content-->
    <div class="col-md-8 col-sm-12">
        <div class="row">
            <div class="wrap-products clearfix">
                <?php if ($cat_id != ''): ?>
                    <h2 class="text-center"
                        style="margin-bottom: 20px;"><?= $category['parent'] . ' ' . $category['child']; ?></h2>
                <?php else: ?>
                    <h2 class="text-center" style="margin-bottom: 20px;">Elite Boutique</h2>

                <?php endif; ?>
                <?php while ($product = mysqli_fetch_assoc($productQ)): ?>
                    <div class="col-md-4 col-sm-4 col-xs-6">
                        <div class="product-item">
                            <h4><?= $product['title'] ?></h4>
                            <?php $photos = explode(',', $product['image']); ?>
                            <img src="<?= $photos[0]; ?>" alt="<?= $product['title'] ?>" class="img-thumb">
                            <p class="list-price text-danger">Старая цена: <s><?= $product['list_price'] ?></s> руб</p>
                            <p class="price">Цена: <?= $product['price'] ?> руб</p>
                            <button type="button" class="btn btn-sm btn-success"
                                    onclick="detailsModal(<?= $product['id']; ?>)">
                                Детали
                            </button>
                        </div><!--/.product-item-->
                    </div><!--wrap-products-->
                <?php endwhile; ?>
            </div>
        </div><!--row-->
    </div>
    <!--Main Content-->

<?php
include 'includes/right_bar.php';
include 'includes/footer.php';
?>