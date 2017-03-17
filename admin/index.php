<?php
require_once '../core/init.php';
if (!is_logged_in()){
    header('Location: login.php');
}

include 'includes/head.php';

//session_destroy();
?>

Administrator Home


<?php include 'includes/footer.php';