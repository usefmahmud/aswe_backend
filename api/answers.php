<?php

require '../conn.php';
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');

$res = array();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['id'])){
        $answer_id = intval($_GET['id']);

        $answer_db = mysqli_query($db_conn, "SELECT * FROM answers WHERE id='$answer_id'");
        $fetch_answer = mysqli_fetch_array($answer_db);

        $question_db = mysqli_query($db_conn, "SELECT * FROM questions WHERE id='". $fetch_answer['question'] ."' ");
        $fetch_question = mysqli_fetch_array($question_db);

        $user_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $fetch_answer['user_id'] ."'");
        $fetch_user = mysqli_fetch_array($user_db);

        $user_question_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $fetch_question['user_id'] ."'");
        $fetch_user_question = mysqli_fetch_array($user_question_db);

        $upvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM upvotes WHERE answer='". $fetch_answer['id'] ."'"));
        $downvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM downvotes WHERE answer='". $fetch_answer['id'] ."'"));

        $answer = array(
            'id' => intval($fetch_answer['id']),
            'answer' => $fetch_answer['question'],
            'date' => $fetch_answer['date'],
            'upvotes' => intval($upvotes_count),
            'downvotes' => intval($downvotes_count),
            'user' => array(
                'id' => intval($fetch_user['id']),
                'name' => $fetch_user['name'],
                'image' => "http://localhost/ask/uploads/profiles/" . $fetch_user['image'],
            ),
            'question' => array(
                'id' => $fetch_question['id'],
                'question' => $fetch_question['question'],
                'date' => $fetch_question['date'],
                'adults' => boolval($fetch_question['adults']),
                'user' => array(
                    'id' => $fetch_user_question['id'],
                    'name' => $fetch_user_question['name'],
                    'image' => "http://localhost/ask/uploads/profiles/" . $fetch_user_question['image'],
                ),
            )
        );

        $res = array(
            'errors' => null,
            'data' => $answer
        );
        
    }else{
        $answers_db = mysqli_query($db_conn, "SELECT * FROM answers");
        $fetch_answers = mysqli_fetch_array($answers_db);

        $answers = array();
        foreach($answers_db as $answer){
            $user_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $answer['user_id'] ."'");
            $fetch_user = mysqli_fetch_array($user_db);

            $question_db = mysqli_query($db_conn, "SELECT * FROM questions WHERE id='". $answer['question'] ."'");
            $fetch_question = mysqli_fetch_array($question_db);

            $user_question_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $fetch_question['user_id'] ."'");
            $fetch_user_question = mysqli_fetch_array($user_question_db);

            $upvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM upvotes WHERE answer='". $answer['id'] ."'"));
            $downvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM downvotes WHERE answer='". $answer['id'] ."'"));

            $answers[] = array(
                'id' => intval($answer['id']),
                'answer' => $answer['question'],
                'date' => $answer['date'],
                'upvotes' => intval($upvotes_count),
                'downvotes' => intval($downvotes_count),
                'user' => array(
                    'id' => intval($fetch_user['id']),
                    'name' => $fetch_user['name'],
                    'image' => "http://localhost/ask/uploads/profiles/" . $fetch_user['image'],
                ),
                'question' => array(
                    'id' => $fetch_question['id'],
                    'question' => $fetch_question['question'],
                    'date' => $fetch_question['date'],
                    'adults' => boolval($fetch_question['adults']),
                    'user' => array(
                        'id' => $fetch_user_question['id'],
                        'name' => $fetch_user_question['name'],
                        'image' => "http://localhost/ask/uploads/profiles/" . $fetch_user_question['image'],
                    ),
                    // 'answers' => $question_answers
                )
            );

           
        }

        $res = array(
            'errors' => null,
            'data' => $answers
        );
       
    }
}else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $error = false;

    $answer = filter_var($_POST['answer'], FILTER_SANITIZE_STRING);
    $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
    $question = filter_var($_POST['question'], FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);

    if($answer === null || $question === null || $date === null || $user_id == null){
        $error = true;
    }

    if(!$error){
        $query = mysqli_query($db_conn, "INSERT INTO answers(
                                                        `answer`, `date`, `question`, `user_id`
                                                    ) VALUES(
                                                        '$answer', '$date', '". intval($question) ."', '". intval($user_id) ."'
                                                    )");

        if($query){
            $res = array(
                'error' => null,
                'data' => array(
                    'answer_id' => mysqli_insert_id($db_conn)
                )
            );
        }else{
            $res = array(
                'error' => array(
                    'message' => 'error while adding answer'
                ),
                'data' => []
            );
        } 
    }else{
        $res = array(
            'error' => array(
                'message' => 'error while adding answer'
            ),
            'data' => []
        );
    }       
}

echo json_encode($res);



/*
$question_answers_db = mysqli_query($db_conn, "SELECT * FROM answers WHERE question='". $fetch_question['id'] ."'");
            $fetch_user_question = mysqli_fetch_array($question_answers_db);

            $question_answers = array();
            foreach($question_answers_db as $question_answer){
                $user_answer_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $question_answer['user_id'] ."'");
                $fetch_user_answer = mysqli_fetch_array($user_answer_db);

                $upvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM upvotes WHERE answer='". $question_answer['id'] ."'"));
                $downvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM downvotes WHERE answer='". $question_answer['id'] ."'"));

                $question_answers[] = array(
                    'id' => $question_answer['id'],
                    'answer' => $question_answer['answer'],
                    'date' => $question_answer['date'],
                    'upvotes' => intval($upvotes_count),
                    'downvotes' => intval($downvotes_count),
                    'user' => array(
                        'id' => $fetch_user_answer['id'],
                        'name' => $fetch_user_answer['name'],
                        'image' => "http://localhost/ask/uploads/profiles/" . $fetch_user_answer['image'],
                    )
                );
            }
*/