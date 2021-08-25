<?php

class User
{
    //register
    public static function register($request)
    {
        $error = [];

        if (isset($request)) {
            if (empty($request['name'])) {
                $error[] = "Name field is required";
            }
            if (empty($request['email'])) {
                $error[] = "Email field is required";
            }
            if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                $error[] = "Invalid Email Format";
            }
            if (empty($request['password'])) {
                $error[] = "Password field is required";
            }
            // if (empty($request['image'])) {
            //     $error[] = "Image field is required";
            // }

            //check email already exits
            $user = DB::table('users')->where('email', $request['email'])->getOne();
            if ($user) {
                $error[] = "Email already exits";
            }

            if (count($error)) {
                return $error;
            } else {
                //image upload 
                $image = $_FILES['image'];
                $image_name = $image['name'];
                $path = "assets/user/$image_name";
                $tmp_name = $image['tmp_name'];
                if (move_uploaded_file($tmp_name, $path)) {

                    //insert data into db
                    $name = $request['name'];
                    $email = $request['email'];
                    $password = $request['password'];

                    $user = DB::create('users', [
                        'name' => Helper::filter($name),
                        'slug' => Helper::slug($request['name']),
                        'email' => Helper::filter($email),
                        'password' => password_hash($password, PASSWORD_BCRYPT),
                        'image' => $path,
                    ]);

                    //session user_id
                    $_SESSION['user_id'] = $user->id;

                    return 'success';
                } else {
                    $error[] = "Failed to upload image!";
                }
            }
        }
    }

    //auth 
    public static function auth()
    {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            return DB::table('users')->where('id', $user_id)->getOne();
        }
        return false;
    }

    //login process
    public function login($request)
    {
        $email = Helper::filter($request['email']);
        $password = $request['password'];
        $error = [];

        //check email
        $user = DB::table('users')->where('email', $email)->getOne();
        if ($user) {
            //verify password
            $db_password = $user->password;
            if (password_verify($password, $db_password)) {
                $_SESSION['user_id'] = $user->id;
                return 'success';
            } else {
                //password not found
                $error[] = "Wrong Password";
            }
        } else {
            //email not found
            $error[] = "Wrong Email";
        }
        return $error;
    }

    //edit process
    public static function update($request)
    {
        $user = DB::table('users')->where('slug', $request['slug'])->getOne();
        if ($request['password']) {
            //new password
            $password = password_hash($request['password'], PASSWORD_BCRYPT);
        } else {
            //old password
            $password = $user->password;
        }

        if (isset($_FILES['image'])) {
            //new image
            $image = $_FILES['image'];
            $image_name = $image['name'];
            $path = "assets/user/$image_name";
            $tmp_name = $image['tmp_name'];
            move_uploaded_file($tmp_name, $path);
        } else {
            //old image
            $path = $user->image;
        }
        DB::update('users', [
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $password,
            'image' => $path,
        ], $user->id);

        return 'success';
    }
}
