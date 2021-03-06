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
                <li><a href="brands.php">Бренды</a></li>
                <li><a href="categories.php">Категории</a></li>
                <li><a href="products.php">Товары</a></li>
                <?php if (has_permission('admin')): ?>
                    <li><a href="users.php">Пользователи</a></li>
                <?php endif; ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Привет <?= $user_data['first']; ?>! <span
                                class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="change_password.php">Изменить пароль</a></li>
                        <li><a href="logout.php">Выход</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
</nav>













