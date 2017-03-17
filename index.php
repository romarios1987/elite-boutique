<?php
require_once 'core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/header_full.php';
include 'includes/left_bar.php';

$query = "SELECT * FROM products WHERE featured = 1";
$featured = $db->query($query);
?>

    <!--Main Content-->
    <div class="col-md-8">
        <div class="row">
            <h2 class="text-center">Рекомендуемые товары</h2>
            <?php while ($product = mysqli_fetch_assoc($featured)): ?>
                <div class="col-md-3 text-center">
                    <h4><?= $product['title']; ?></h4>
                    <img src="<?= $product['image']; ?>" alt="<?= $product['title']; ?>" class="img-thumb">
                    <p class="list-price text-danger">Старая цена: <s><?= $product['list_price']; ?></s> руб</p>
                    <p class="price">Цена: <?= $product['price'] ?> руб</p>
                    <button type="button" class="btn btn-sm btn-success" onclick="detailsModal(<?= $product['id']; ?>)">
                        Детали
                    </button>
                </div>
            <?php endwhile; ?>
        </div><!--row-->
    </div>
    <!--Main Content-->


<?php
include 'includes/right_bar.php';
include 'includes/footer.php';
