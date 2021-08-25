<?php
class DB
{
    private static $dbh = null;
    private static $sql, $res, $data, $count;
    public function __construct()
    {
        // self::$dbh = new PDO("mysql:host=localhost;dbname=php_project", "root", "");

        $servername = "localhost";
        $dbname = "php_project";
        $username = "root";
        $password = "1234";

        try {
            self::$dbh = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected successfully";
          } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
          }
          
    }

    public function query($params = [])
    {

        self::$res = self::$dbh->prepare(self::$sql);
        self::$res->execute($params);
        return $this;
    }

    public static function table($table)
    {
        $sql = 'select * from ' . $table;
        self::$sql = $sql;
        $db = new DB();
        return $db;
    }

    public function orderBy($col, $order)
    {
        self::$sql .= ' order by ' . $col . ' ' . $order;
        $this->query();
        // echo self::$sql;
        return $this;
    }

    public function where($col, $operator, $value = "")
    {
        if (func_num_args() == 2) {
            self::$sql .= ' where ' . $col . '="' . $operator . '"';
        } else {
            self::$sql .= ' where ' . $col . ' ' . $operator . ' "' . $value . '"';
            echo self::$sql;
        }
        return $this;
    }

    public function andWhere($col, $operator, $value = "")
    {
        if (func_num_args() == 2) {
            self::$sql .= ' and ' . $col . '="' . $operator . '"';

            $this->query();
        } else {
            self::$sql .= ' and ' . $col . ' ' . $operator . ' "' . $value . '"';

            $this->query();
        }
        return $this;
    }

    public function orWhere($col, $operator, $value = "")
    {
        if (func_num_args() == 2) {
            self::$sql .= ' or ' . $col . '="' . $operator . '"';

            $this->query();
        } else {
            self::$sql .= ' or ' . $col . ' ' . $operator . ' "' . $value . '"';

            $this->query();
        }
        return $this;
    }

    public static function create($table, $data)
    {
        $db = new DB();

        $v = '';


        //for keys
        $str_col =  implode(',', array_keys($data));

        //for value
        $values = array_values($data);


        //for ?
        foreach ($data as $d) {
            $v .= '?,';
        }
        $unknown_var = substr($v, 0, -1);

        $sql = 'insert into ' . $table . ' (' . $str_col . ') values (' . $unknown_var . ')';

        self::$sql = $sql;

        $db->query($values);

        $new_id = self::$dbh->lastInsertId();

        return  $db::table($table)->where('id', $new_id)->getOne();
    }

    public static function update($table, $data, $id)
    {
        $db = new DB();

        $sql = 'update ' . $table . ' set ';

        //for value
        $values = array_values($data);


        //key and ?
        $keys_str = '';
        foreach (array_keys($data) as $d) {
            $i = 0;
            $keys_str .= $d . ' = ? , ';
        }
        $keys_str = substr($keys_str, 0, -2);
        $sql .= $keys_str . ' where id = ' . $id;
        self::$sql = $sql;

        $db->query($values);
        return  DB::table('users')->where('id', $id)->getOne();
    }

    public static function delete($table, $id)
    {
        $db = new DB();
        $sql = 'delete from ' . $table . ' where id = ?';
        self::$sql = $sql;
        $db->query([$id]);
        echo 'deleted';
    }


    public function paginate($records_per_page, $append = "")
    {
        if (isset($_GET['page'])) {
            $page_no = $_GET['page'];
        }
        if (!isset($_GET['page'])) {
            $page_no = 1;
        }
        if (isset($_GET['page']) && $_GET['page'] < 1) {
            $page_no = 1;
        }
        //count
        $this->query();
        $count = self::$res->rowCount();


        //previous , next
        $pre_page_no = $page_no - 1;
        $next_page_no = $page_no + 1;
        $pre_page = '?page=' . $pre_page_no;
        $next_page = '?page=' . $next_page_no;

        //records
        $record_start =  ($page_no - 1) * $records_per_page;

        self::$sql .= ' limit ' . $record_start . ',' . $records_per_page;
        $this->query();
        $records = self::$res->fetchAll(PDO::FETCH_OBJ);

        $data = [
            'total' => $count,
            'data' => $records,
            'pre_url' => $pre_page . $append,
            'next_url' => $next_page . $append,
        ];

        return $data;
    }

    public static function raw($sql)
    {
        $db = new DB();
        self::$sql = $sql;
        return $db;
    }

    public function count()
    {
        $this->query();
        self::$count = self::$res->rowCount();
        return self::$count;
    }

    public function get()
    {
        $this->query();
        self::$data = self::$res->fetchAll(PDO::FETCH_OBJ);
        return self::$data;
    }

    public function getOne()
    {
        $this->query();
        self::$data = self::$res->fetch(PDO::FETCH_OBJ);
        return self::$data;
    }
}

// $db = new DB();
// $data = DB::table('users')->where('name','Saw Joseph Wah')->get();
// echo "<pre>";
// print_r($data);

// $user = DB::create('users',[
//     'name'=> 'Sane',
//     'age'=> 26,
//     'location'=>'Bayrn Munich', 
// ]);

// echo '<pre>';
// print_r($user);

// $user = DB::update('users',[
//     'name'=> 'Grizmann',
//     'age'=> 29,
//     'location'=>'Barcelona', 
// ], 10 );

// echo '<pre>';
// print_r($user);

// DB::delete('users', 9 );

// $users = DB::table('users')->paginate(3);
// echo '<pre>';
// print_r($users);