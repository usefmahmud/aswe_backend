<?php

require '../conn.php';
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');

$res = array();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $error = false;

    $type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
    $image_name = '';

    if(is_uploaded_file($_FILES['image']['tmp_name'])){
        $tmp_file = $_FILES['image']['tmp_name'];

        $image_name = sha1(time()) . '.' . explode('.', $_FILES['image']['name'])[1];
        if($type === 'answer_image'){
            $upload_dir = '../uploads/answers/' . $image_name;
        }

        move_uploaded_file($tmp_file, $upload_dir);
    }else{
        $error = true;
    }

    if(!$error){
        $upload_folder = '';
        if($type === 'answer_image'){
            $upload_folder = 'answers';
        }

        $res = array(
            'error' => null,
            'data' => array(
                'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/ask" . '/uploads/'. $upload_folder .'/' . $image_name
            )
        );
    }else{
        $res = array(
            'error' => array(
                'message' => 'error while uploading image'
            ),
            'data' => []
        );
    }       
}

echo json_encode($res);