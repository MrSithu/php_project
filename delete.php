<?php
require_once 'inc/header.php';
$slug = $_GET['slug'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $res = Post::delete($slug);
}
?>

<div class="card card-dark">
    <div class="card-header bg-warning text-center">
        <h5>Are you sure to delete?</h5>
    </div>
    <div class="card-body">

        <form action="" method="post" enctype="multipart/form-data" class="text-center">
            <?php
            if (isset($res) and $res == 'successDeleted') {
            ?>
                <div class="alert alert-success">Article deleted success!</div>
            <?php
            }
            ?>
            <input id="cancel" type="submit" value="Cancel" class="btn  btn-outline-warning">
            <input type="submit" value="Delete" class="btn  btn-outline-warning">
        </form>
    </div>
</div>

<?php
require_once 'inc/footer.php';
?>

<script>
    var cancel = document.getElementById('cancel');
    cancel.addEventListener("click", function() {
        alert("hi");
        location.href = "index.php";
    })
</script>