<?php
class Post
{
    //fetch articles from db
    public static function all()
    {
        $articles = DB::table('articles')->orderBy('id', 'DESC')->paginate(6);
        foreach ($articles['data'] as $k => $d) {
            $articles['data'][$k]->like_count = DB::table('article_likes')->where('article_id', $d->id)->count();
            $articles['data'][$k]->comment_count = DB::table('article_comments')->where('article_id', $d->id)->count();
        }
        return $articles;
    }

    //fetch articles detail from db
    public static function detail($slug)
    {
        $articles = DB::table('articles')->where('slug', $slug)->getOne();

        //try to get lke count
        $articles->like_count = DB::table("article_likes")->where('article_id', $articles->id)->count();

        //try to get comment count
        $articles->comment_count = DB::table("article_comments")->where('article_id', $articles->id)->count();


        //try to get comments
        $articles->comments = DB::table("article_comments")->where("article_id", $articles->id)->get();

        //try to get category
        $articles->category = DB::table("category")->where('id', $articles->category_id)->getOne();

        //try to get languages
        $articles->languages = DB::raw("select languages.id,languages.slug,languages.name from article_language 
        left join languages 
        on languages.id = article_language.language_id
        where article_id = {$articles->id};")->get();

        return $articles;
    }

    //article by category
    public static function articleByCategory($slug)
    {
        $category_id = DB::table('category')->where('slug', $slug)->getOne()->id;
        $articles = DB::table('articles')->where('category_id', $category_id)->orderBy('id', 'DESC')->paginate(6, "&category=$slug");

        foreach ($articles['data'] as $k => $d) {
            $articles['data'][$k]->like_count = DB::table('article_likes')->where('article_id', $d->id)->count();
            $articles['data'][$k]->comment_count = DB::table('article_comments')->where('article_id', $d->id)->count();
        }
        return $articles;
    }

    //article by Language
    public static function articleByLanguage($slug)
    {
        $language_id = DB::table('languages')->where('slug', $slug)->getOne()->id;
        $articles = DB::raw("
            select * from article_language inner join articles 
            on articles.id = article_language.article_id 
            where article_language.language_id = {$language_id}
        ")->orderBy('articles.id', "DESC")->paginate(6, "&language=$slug");

        foreach ($articles['data'] as $k => $d) {
            $articles['data'][$k]->like_count = DB::table('article_likes')->where('article_id', $d->id)->count();
            $articles['data'][$k]->comment_count = DB::table('article_comments')->where('article_id', $d->id)->count();
        }
        return $articles;
    }

    //create article
    public static function create($request)
    {
        // return Helper::slug($request['title']);

        //image upload 
        $image = $_FILES['image'];
        $image_name = $image['name'];
        $path = "assets/article/$image_name";
        $tmp_name = $image['tmp_name'];

        if (move_uploaded_file($tmp_name, $path)) {
            //insert into article table

            $article = DB::create('articles', [
                'user_id' => User::auth()->id,
                'category_id' =>  $request['category_id'],
                'slug' => Helper::slug($request['title']),
                'title' => $request['title'],
                'image' => $path,
                'description' => $request['description']
            ]);

            //insert many to many
            if ($article) {
                foreach ($request['language_id'] as $id) {
                    DB::create('article_language', [
                        'article_id' => $article->id,
                        'language_id' => $id,
                    ]);
                    return 'success';
                }
            } else {
                return "article_error";
            }
        } else {
            return "image_error";
        }
    }

    //search process
    public static function search($search)
    {
        $articles = DB::table('articles')->where("title", "like", "%$search%")->orderBy("id", "DESC")->paginate(6, "&search=$search");
        foreach ($articles['data'] as $k => $d) {
            $articles['data'][$k]->like_count = DB::table('article_likes')->where('article_id', $d->id)->count();
            $articles['data'][$k]->comment_count = DB::table('article_comments')->where('article_id', $d->id)->count();
        }
        return $articles;
    }

    //post by id
    public static function postById($id)
    {
        $articles = DB::table('articles')->where('user_id', $id)->orderBy('id', 'DESC')->paginate(6);
        return $articles;
    }

    //edit articles
    public static function update($request)
    {
        $slug = $request['slug'];
        $title = $request['title'];
        $image = $request['image'];
        $description = $request['description'];

        $article = DB::table('articles')->where('slug', $slug)->getOne();

        //image edit

        if (isset($_FILES['image'])) {
            //new image
            $image = $_FILES['image'];
            $image_name = $image['name'];
            $path = "assets/article/$image_name";
            $tmp_name = $image['tmp_name'];
            move_uploaded_file($tmp_name, $path);
        } else {
            //old image
            $path = $article->image;
        }

        DB::update('articles', [
            'title' => $title,
            'image' => $path,
            'description' => $description
        ], $article->id);

        return 'success';
    }

    //delete article
    public static function delete($slug)
    {
        $article = DB::table('articles')->where('slug', $slug)->getOne();
        DB::delete('articles', $article->id);

        return 'successDeleted';
    }
}
