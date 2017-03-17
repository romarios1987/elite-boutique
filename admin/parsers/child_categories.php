<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/init.php';
$parentID = (int)$_POST['parentID'];
$child_query = $db->query("SELECT * FROM categories WHERE parent = '$parentID' ORDER BY category");
$selected = sanitize($_POST['selected']);
ob_start(); ?>
<option value=""></option>
<?php while ($child = mysqli_fetch_assoc($child_query)): ?>
    <option value="<?= $child['id'] ?>" <?=(($selected == $child['id'])?' selected':'');?>><?= $child['category'] ?></option>
<?php endwhile; ?>
<?php echo ob_get_clean(); ?>
