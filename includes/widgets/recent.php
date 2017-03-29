<h3 class="text-center" style="margin-top: 35px;">Популярные товары</h3>
<?php
$trans_query = $db->query("SELECT * FROM cart WHERE paid = 0 ORDER BY id DESC LIMIT 5");
$res = [];
while ($row = mysqli_fetch_assoc($trans_query)) {
    $res[] = $row;
}
$row_count = $trans_query->num_rows;
$used_ids = [];
for ($i = 0; $i < $row_count; $i++) {
    $json_items = $res[$i]['items'];
    $items = json_decode($json_items, true);
    foreach ($items as $item) {
        if (!in_array($item['id'], $used_ids)) {
            $used_ids[] = $item['id'];
        }
    }
}
?>
<div id="recent_widget">
    <table class="table table-condensed">
        <?php foreach ($used_ids as $id):
            $product_query = $db->query("SELECT id,title FROM products WHERE id = '{$id}'");
            $product = mysqli_fetch_assoc($product_query);
            ?>
            <tr>
                <td>
                    <?= $product['title']; ?>
                </td>
                <td>
                    <button type="button" class="btn btn-xs btn-primary" onclick="detailsModal('<?= $id; ?>')">
                        Детали
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
