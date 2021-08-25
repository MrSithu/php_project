<?php
require_once 'core/autoload.php';

//like process

$request = $_GET;

if (isset($request['like'])) {
    $user_id = $request['user_id'];
    $article_id = $request['article_id'];

    $u = DB::table('article_likes')->where('user_id', $user_id)->andWhere('article_id', $article_id)->getOne();
    if ($u) {
        echo "already_like";
    } else {
        $like = DB::create('article_likes', [
            'user_id' => $user_id,
            'article_id' => $article_id
        ]);

        if ($like) {
            $count = DB::table('article_likes')->where('article_id', $article_id)->count();
            echo $count;
        }
    }
}

//##comment process

if ($_POST['comment']) {
    $user_id = User::auth()->id;
    $article_id = $_POST['article_id'];
    $comment = $_POST['comment'];

    $comment = DB::create('article_comments', [
        'user_id' => $user_id,
        'article_id' => $article_id,
        'comment' => $comment,
    ]);

    if ($comment) {
        $comments = DB::table('article_comments')->where('article_id', $article_id)->orderBy('id', 'DESC')->get();
        // print_r($comments);
        $html = '';
        foreach ($comments as $c) {
            $user = DB::table('users')->where('id', $c->user_id)->getOne();
            $html .= "
            <div class='card-dark mt-1'>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-md-1'>
                            <img src='{$user->image}' style='width:50px;border-radius:50%'>
                        </div>
                        <div class='col-md-4 d-flex align-items-center'>
                            {$user->name}
                        </div>
                    </div>
                    <hr>
                    <p>
                        {$c->comment}
                    </p>
                </div>
            </div>
            ";
        }
        echo $html;
    }
}
