<?php
ob_start();
require_once 'core/autoload.php';
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!--  Font Awesome for Bootstrap fonts and icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">

    <!-- Material Design for Bootstrap CSS -->
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css" integrity="sha384-wXznGJNEXNG1NFsbm0ugrLFMQPWswR3lds2VeinahP8N0zJw9VWSopbjv2x7WCvX" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <title>MM-Coder</title>
    <style>

    </style>
</head>

<body>
    <!-- Start Nav -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand text-warning" href="index.php">Blogging!</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Articles</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        User
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php
                        if (User::auth()) {
                        ?>
                            <a class="dropdown-item" href="#">Welcome <?php echo User::auth()->name; ?></a>
                            <a class="dropdown-item" href="edit_user.php?user=<?php echo User::auth()->slug; ?>">Edit User</a>
                            <a class="dropdown-item" href="logout.php">Logout</a>

                        <?php
                        } else {
                        ?>
                            <a class="dropdown-item" href="login.php">Login</a>
                            <a class="dropdown-item" href="register.php">Register</a>
                        <?php
                        }
                        ?>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?id=<?php echo User::auth()->id; ?>">My Articles</a>
                </li>

                <?php
                if (User::auth()) {
                ?>
                    <li class="nav-item ml-5">
                        <a class="nav-link btn btn-sm  btn-warning" href="create.php">
                            <i class="fas fa-plus"></i>
                            Create Article</a>
                    </li>

                <?php
                }
                ?>

            </ul>
            <form action="index.php" method="GET" class="form-inline my-2 my-lg-0">
                <input name="search" class="form-control mr-sm-2" type="search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>

    <!-- Start Header -->

    <div class="jumbotron jumbotron-fluid header">
        <div class="container">
            <h1 class="text-white">MM-Coder Technology Blogging..</h1>
            <br>
            <?php
            if (User::auth()) {
            ?>
                <h3 class="text-white">Welcome <?php echo User::auth()->name; ?></h3>
            <?php
            } else {
            ?>
                <a href="register.php" class="btn btn-warning">Create Account</a>
                <a href="login.php" class="btn btn-outline-success">Login</a>
            <?php
            }
            ?>

        </div>
    </div>

    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 pr-3 pl-3">
                <!-- Category List -->
                <div class="card card-dark">
                    <div class="card-header">
                        <h4>All Category</h4>
                    </div>

                    <div class="card-body">
                        <?php
                        $category = DB::raw(
                            'select *,
                                (select count(id) from articles where category_id = category.id ) as article_count
                                from category'
                        )->get();
                        // print_r($category);
                        ?>
                        <ul class="list-group">
                            <?php
                            foreach ($category as $c) {
                            ?>
                                <a href="index.php?category=<?php echo  $c->slug; ?>">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo $c->name; ?>
                                        <span class="badge badge-primary badge-pill">
                                            <?php echo $c->article_count; ?>
                                        </span>
                                    </li>
                                </a>

                            <?php
                            }
                            ?>

                        </ul>
                    </div>

                </div>
                <hr>
                <!-- Language List -->
                <div class="card card-dark">
                    <div class="card-header">
                        <h4>All Languages</h4>
                    </div>

                    <div class="card-body">
                        <?php
                        $languages = DB::raw('
                            select *, 
                            (select count(id) from article_language where languages.id = article_language.language_id)as article_count from languages
                            ')->get();
                        // print_r($languages);
                        ?>
                        <ul class="list-group">
                            <?php
                            foreach ($languages as $l) {
                            ?>
                                <a href="index.php?language=<?php echo $l->slug; ?>">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo $l->name; ?>
                                        <span class="badge badge-primary badge-pill"><?php echo $l->article_count; ?></span>
                                    </li>
                                </a>
                            <?php
                            }
                            ?>

                        </ul>
                    </div>

                </div>
            </div>

            <!-- Content -->
            <div class="col-md-8">