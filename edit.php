<?php
require_once 'inc/header.php';

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $article = DB::table('articles')->where('slug', $slug)->getOne();

    if (!$article) {
        Helper::redirect('404.php');
    } else {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $updateArticle = Post::update($_POST);
        }
    }
} else {
    Helper::redirect('404.php');
}

?>

<div class="card card-dark">
    <div class="card-header bg-warning">
        <h3>Edit Articles</h3>
    </div>
    <div class="card-body">
        <?php
        if (isset($updateArticle) and $updateArticle == 'success') {
        ?>
            <div class="alert alert-success">Success to edit article!</div>
        <?php
        }
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="slug" value="<?php echo $article->slug; ?>">
            <div class="form-group">
                <label for="" class="text-white">Edit Title</label>
                <input type="name" name="title" class="form-control" placeholder="edit title" value="<?php echo $article->title; ?>">
            </div>
            <div class="form-group">
                <label for="">Edit Image</label>
                <input type="file" name="image" class="form-control">
                <img src="<?php echo $article->image; ?>" style="width: 200px; border-radius:20px;" alt="">
            </div>
            <div class="form-group">
                <label for="" class="text-white">Edit Description</label>
                <textarea name="description" class="form-control" id="" cols="30" rows="10"><?php echo $article->description; ?></textarea>
            </div>
            <input type="submit" value="Update" class="btn  btn-outline-warning">
        </form>
    </div>
</div> 

<?php
require_once 'inc/footer.php';
?>