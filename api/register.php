<?php

require '../conn.php';
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');

$res;
$error = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $join_date = filter_var($_POST['join_date'], FILTER_SANITIZE_STRING);
    $country = filter_var($_POST['country'], FILTER_SANITIZE_NUMBER_INT);
    $birthdate = filter_var($_POST['birthdate'], FILTER_SANITIZE_STRING);

    if($name === null || $email === null || $password === null || $join_date === null || $birthdate === null || $country == null){
        $error = true;
    }

    

    if(!$error){
        if(mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM users WHERE email='$email'")) === 0){
            $query = mysqli_query($db_conn, "INSERT INTO users(
                `name`, `email`, `password`, `country`, `birthdate`, `join_date`, `description`, `image`
            ) VALUES(
                '$name', '$email', '$password', '". intval($country) ."', '$birthdate', '$join_date', '', ''
            )");

            if($query){
                $res = array(
                    'error' => null,
                    'data' => array(
                        'user_id' => mysqli_insert_id($db_conn)
                    )
                );
            }else{
                $res = array(
                    'error' => array(
                        'message' => 'error while registering 1'
                    ),
                    'data' => []
                );
            } 
        }else{
            $res = array(
                'error' => array(
                    'message' => 'Email is already registered'
                ),
                'data' => []
            );
        }
        
    }else{
        $res = array(
            'error' => array(
                'message' => 'error while registering 2'
            ),
            'data' => []
        );
    }                           
}   

echo json_encode($res);