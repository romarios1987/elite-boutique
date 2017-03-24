<?php
$query = "SELECT * FROM categories WHERE parent = 0";
$res = $db->query($query);
?>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Elite Boutique</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">

                <?php while ($parent = mysqli_fetch_assoc($res)): ?>
                    <?php
                    $parent_id = $parent['id'];
                    $query2 = "SELECT * FROM categories WHERE parent = '$parent_id'";
                    $res2 = $db->query($query2);
                    ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $parent['category']; ?><span
                                    class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php while ($child = mysqli_fetch_assoc($res2)): ?>
                                <li><a href="category.php?cat=<?= $child['id']; ?>"><?= $child['category']; ?></a></li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
                <?php endwhile; ?>
                <li><a href="cart.php"><span class="glyphicon glyphicon-shopping-cart"></span>Корзина</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
</nav>









