<?php
require_once 'inc/header.php';

//redirect index if login
if (User::auth()) {
    Helper::redirect('index.php');
}

//login process
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = new User();
    $user = $u->login($_POST);

    if ($user == "success") {
        Helper::redirect('index.php');
    }
}

?>

<div class="card card-dark">
    <div class="card-header bg-warning">
        <h3>Login</h3>
    </div>
    <div class="card-body">
        <?php
        if (isset($user) and is_array($user)) {
            foreach ($user as $u) {
        ?>
                <div class="alert alert-warning"><?php echo $u; ?></div>

        <?php
            }
        }
        ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="" class="text-white">Enter Username</label>
                <input type="name" name="email" class="form-control" placeholder="enter username" />
            </div>
            <div class="form-group">
                <label for="" class="text-white">Enter Password</label>
                <input type="password" name="password" class="form-control" placeholder="enter password" />
            </div>
            <input type="submit" value="Login" class="btn btn-outline-warning" />
        </form>
    </div>
</div>

<?php
require_once 'inc/footer.php';
?>