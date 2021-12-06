<?php

require '../conn.php';
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');

$res = array();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['answer_id'])){
        $answer_id = intval($_GET['answer_id']);

        $upvotes_db = mysqli_query($db_conn, "SELECT * FROM upvotes WHERE answer='$answer_id'");
        $fetch_upvotes = mysqli_fetch_array($upvotes_db);

        $upvotes = array();
        foreach($upvotes_db as $upvote){
            $user_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $upvote['user_id'] ."'");
            $fetch_user = mysqli_fetch_array($user_db);

            $answer_db = mysqli_query($db_conn, "SELECT * FROM answers WHERE id='". $upvote['answer'] ."'");
            $fetch_answer = mysqli_fetch_array($answer_db);

            $upvotes[] = array(
                'id' => intval($upvote['id']),
                'answer' => array(
                    'id' => intval($fetch_answer['id']),
                    'answer' => $fetch_answer['answer'],
                    'date' => $fetch_answer['date']
                ),
                'user' => array(
                    'id' => intval($fetch_user['id']),
                    'name' => $fetch_user['name']
                ),
            );

            
        }

        $res = array(
            'errors' => null,
            'data' => $upvotes
        );
        
    }else if(isset($_GET['user_id'])){
        $user_id = intval($_GET['user_id']);

        $upvotes_db = mysqli_query($db_conn, "SELECT * FROM upvotes WHERE user_id='$user_id'");
        $fetch_upvotes = mysqli_fetch_array($upvotes_db);

        $upvotes = array();
        foreach($upvotes_db as $upvote){
            $user_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $upvote['user_id'] ."'");
            $fetch_user = mysqli_fetch_array($user_db);

            $answer_db = mysqli_query($db_conn, "SELECT * FROM answers WHERE id='". $upvote['answer'] ."'");
            $fetch_answer = mysqli_fetch_array($answer_db);

            $upvotes[] = array(
                'id' => intval($upvote['id']),
                'answer' => array(
                    'id' => intval($fetch_answer['id']),
                    'answer' => $fetch_answer['answer'],
                    'date' => $fetch_answer['date']
                ),
                'user' => array(
                    'id' => intval($fetch_user['id']),
                    'name' => $fetch_user['name']
                ),
            );

            
        }

        $res = array(
            'errors' => null,
            'data' => $upvotes
        );
    }else{
        $upvotes_db = mysqli_query($db_conn, "SELECT * FROM upvotes");
        $fetch_upvotes = mysqli_fetch_array($upvotes_db);

        $upvotes = array();
        foreach($upvotes_db as $upvote){
            $user_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $upvote['user_id'] ."'");
            $fetch_user = mysqli_fetch_array($user_db);

            $answer_db = mysqli_query($db_conn, "SELECT * FROM answers WHERE id='". $upvote['answer'] ."'");
            $fetch_answer = mysqli_fetch_array($answer_db);

            $upvotes[] = array(
                'id' => intval($upvote['id']),
                'answer' => array(
                    'id' => intval($fetch_answer['id']),
                    'answer' => $fetch_answer['answer'],
                    'date' => $fetch_answer['date']
                ),
                'user' => array(
                    'id' => intval($fetch_user['id']),
                    'name' => $fetch_user['name']
                ),
            );

            
        }

        $res = array(
            'errors' => null,
            'data' => $upvotes
        );
       
    }
}else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $error = false;

    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
    $answer = filter_var($_POST['answer'], FILTER_SANITIZE_NUMBER_INT);

    if($user_id === null || $answer === null){
        $error = true;
    }

    if(!$error){
        $query = mysqli_query($db_conn, "INSERT INTO upvotes(
                                                        `user_id`, `answer`
                                                    ) VALUES(
                                                        '". intval($user_id) ."', '". intval($answer) ."'
                                                    )");

        if($query){
            $res = array(
                'error' => null,
                'data' => array(
                    'upvote_id' => mysqli_insert_id($db_conn)
                )
            );
        }else{
            $res = array(
                'error' => array(
                    'message' => 'error while adding upvote'
                ),
                'data' => []
            );
        } 
    }else{
        $res = array(
            'error' => array(
                'message' => 'error while adding upvote'
            ),
            'data' => []
        );
    }       
}

echo json_encode($res);