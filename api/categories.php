<?php

require '../conn.php';
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');

$res = array();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['id'])){
        $category_id = intval($_GET['id']);

        $category_db = mysqli_query($db_conn, "SELECT * FROM categories WHERE id='$category_id'");
        $fetch_category = mysqli_fetch_array($category_db);

        $category = array(
            'id' => $fetch_category['id'],
            'name' => $fetch_category['name'],
        );

            $res = array(
                'errors' => null,
                'data' => $category
            );
        
    }else{
        $categories_db = mysqli_query($db_conn, "SELECT * FROM categories");
        $fetch_categories = mysqli_fetch_array($categories_db);

        $categories = array();
        foreach($categories_db as $category){

            $categories[] = array(
                'id' => intval($category['id']),
                'name' => $category['name'],
            );

            $res = array(
                'errors' => null,
                'data' => $categories
            );
        }
       
    }
}else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $error = false;

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

    if($name === null){
        $error = true;
    }

    if(!$error){
        $query = mysqli_query($db_conn, "INSERT INTO categories(
                                                        `name`
                                                    ) VALUES(
                                                        '$name'
                                                    )");

        if($query){
            $res = array(
                'error' => null,
                'data' => array(
                    'category_id' => mysqli_insert_id($db_conn)
                )
            );
        }else{
            $res = array(
                'error' => array(
                    'message' => 'error while adding category'
                ),
                'data' => []
            );
        } 
    }else{
        $res = array(
            'error' => array(
                'message' => 'error while adding category'
            ),
            'data' => []
        );
    }       
}

echo json_encode($res);