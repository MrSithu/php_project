<?php
require_once 'inc/header.php';

//fetch articles from db
// $post = Post::all();

if (isset($_GET['category'])) {
    $slug = $_GET['category'];
    $post = Post::articleByCategory($slug);
} elseif (isset($_GET['language'])) {
    $slug = $_GET['language'];
    $post = Post::articleByLanguage($slug);
} elseif (isset($_GET['search'])) {
    $search = $_GET['search'];
    $post = Post::search($search);
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
    $post = Post::postById($id);
} else {
    $post = Post::all();
}

?>

<div class="card card-dark">
    <div class="card-body">
        <a href="<?php echo $post['pre_url']; ?>" class="btn btn-danger">Prev Posts</a>
        <a href="<?php echo $post['next_url']; ?>" class="btn btn-danger float-right">Next Posts</a>
    </div>
</div>
<div class="card card-dark">
    <div class="card-body">
        <div class="row">
            <!-- Loop this -->
            <?php
            //show articles
            foreach ($post['data'] as $a) {
            ?>
                <div class="col-md-4 mt-2">
                    <div class="card" style="width: 20rem;">
                        <img class="card-img-top" style="width: 320px;height: 200px;" src="<?php echo $a->image; ?>" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="text-dark"><?php echo $a->title; ?></h5>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <?php
                                    // $user_id = User::auth() ? User::auth()->id : 0;
                                    // $article_id = $a->id;
                                    ?>
                                    <i id="like" class="fas fa-heart text-warning">
                                    </i>
                                    <small id="like_count" class="text-muted"><?php echo $a->like_count; ?></small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <i class="far fa-comment text-dark"></i>
                                    <small class="text-muted"><?php echo $a->comment_count; ?></small>
                                </div>
                                <div class="col-md-6 text-center">
                                    <a href="<?php echo "detail.php?slug=$a->slug"; ?>" class="badge badge-success p-1">View</a>
                                    <?php
                                    if ($a->user_id == User::auth()->id) {
                                    ?>
                                        <a href="<?php echo "edit.php?slug=$a->slug"; ?>" class="badge badge-warning p-1">Edit</a>
                                        <a href="<?php echo "delete.php?slug=$a->slug"; ?>" class="badge badge-danger p-1">Delete</a>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>


        </div>
    </div>
</div>

<?php
require_once 'inc/footer.php';
?>

<script>
    // like button process
    var like = document.querySelector('#like');
    var like_count = document.querySelector('#like_count');
    like.addEventListener("click", function() {
        // var user_id = like.getAttribute('user_id');
        // var article_id = like.getAttribute('article_id');

        alert("hi");

        // if user not login
        // if (user_id == 0) {
        //     location.href = "login.php";
        // }

        // axios.get(`api.php?like&article_id=${article_id}`)
        //     .then(function(res) {
        //         if (res.data == 'already_like') {
        //             toastr.warning("Already like");
        //         }
        //         if (Number.isInteger(res.data)) {
        //             like_count.innerHTML = res.data;
        //             toastr.success("Liked success");
        //         }
        //     })
    })
</script>