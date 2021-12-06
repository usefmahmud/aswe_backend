<?php

require '../conn.php';
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');

$res;
$error = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    if($email === null || $password === null){
        $error = true;
    }

    

    if(!$error){
        $check_email = mysqli_query($db_conn, "SELECT * FROM users WHERE email='$email'");
       
        if(mysqli_num_rows($check_email) > 0){
            $fetch_user = mysqli_fetch_array($check_email);
            if($fetch_user['password'] === $password){
                $res = array(
                    'error' => array(
                        'message' => 'Logged in!',
                        'code' => 200
                    ),
                    'data' => array(
                        'id' => intval($fetch_user['id']),
                        'email' => $fetch_user['email'],
                        'password' => $fetch_user['password']
                    )
                );
            }else{
                $res = array(
                    'error' => array(
                        'message' => 'Invalid password',
                        'code' => 100
                    ),
                    'data' => []
                );
            }
        }else{
            $res = array(
                'error' => array(
                    'message' => 'Email is not found',
                    'code' => 100
                ),
                'data' => []
            );
        }
    }else{
        $res = array(
            'error' => array(
                'message' => 'error while registering',
                'code' => 100
            ),
            'data' => []
        );
    }                           
}   

echo json_encode($res);