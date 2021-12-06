<?php

require '../conn.php';
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');

$res = array();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['id'])){
        $counrty_id = intval($_GET['id']);

        $counrty_db = mysqli_query($db_conn, "SELECT * FROM countries WHERE id='$counrty_id'");
        $fetch_counrty = mysqli_fetch_array($counrty_db);

        $counrty = array(
            'id' => $fetch_counrty['id'],
            'name' => $fetch_counrty['name'],
        );

            $res = array(
                'errors' => null,
                'data' => $counrty
            );
        
    }else{
        $countries_db = mysqli_query($db_conn, "SELECT * FROM countries");
        $fetch_countries = mysqli_fetch_array($countries_db);

        $countries = array();
        foreach($countries_db as $counrty){

            $countries[] = array(
                'id' => intval($counrty['id']),
                'name' => $counrty['name'],
                'flag' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/ask" . '/uploads/flags/' . $counrty['flag']
            );

            $res = array(
                'errors' => null,
                'data' => $countries
            );
        }
       
    }
}else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $error = false;

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $flag_name = '';

    if(is_uploaded_file($_FILES['flag']['tmp_name'])){
        $tmp_file = $_FILES['flag']['tmp_name'];

        $image_name = sha1(time()) . '.' . explode('.', $_FILES['flag']['name'])[1];
        $upload_dir = '../uploads/flags/' . $image_name;

        move_uploaded_file($tmp_file, $upload_dir);

        $flag_name = $image_name;
    }else{
        $error = true;
    }

    if($name === null){
        $error = true;
    }

    if(!$error){
        $query = mysqli_query($db_conn, "INSERT INTO countries(
                                                        `name`, `flag`
                                                    ) VALUES(
                                                        '$name', '$flag_name'
                                                    )");

        if($query){
            $res = array(
                'error' => null,
                'data' => array(
                    'counrty_id' => mysqli_insert_id($db_conn)
                )
            );
        }else{
            $res = array(
                'error' => array(
                    'message' => 'error while adding counrty'
                ),
                'data' => []
            );
        } 
    }else{
        $res = array(
            'error' => array(
                'message' => 'error while adding counrty'
            ),
            'data' => []
        );
    }       
}

echo json_encode($res);