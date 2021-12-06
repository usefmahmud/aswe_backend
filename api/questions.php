<?php

require '../conn.php';
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');

$res = array();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['id'])){
        $question_id = intval($_GET['id']);

        $question_db = mysqli_query($db_conn, "SELECT * FROM questions WHERE id='$question_id'");
        $fetch_question = mysqli_fetch_array($question_db);

        $answers_db = mysqli_query($db_conn, "SELECT * FROM answers WHERE question='". $question_id ."' ");
        $fetch_answers = mysqli_fetch_array($answers_db);

        $user_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $fetch_question['user_id'] ."'");
        $fetch_user = mysqli_fetch_array($user_db);

        $answers = array();
        foreach($answers_db as $answer){
            $user_answer_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $answer['user_id'] ."'");
            $fetch_user_answer = mysqli_fetch_array($user_answer_db);

            $upvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM upvotes WHERE answer='". $answer['id'] ."'"));
            $downvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM downvotes WHERE answer='". $answer['id'] ."'"));

            $answers[] = array(
                'id' => $answer['id'],
                'answer' => $answer['answer'],
                'date' => $answer['date'],
                'upvotes' => intval($upvotes_count),
                'downvotes' => intval($downvotes_count),
                'user' => array(
                    'id' => $fetch_user_answer['id'],
                    'name' => $fetch_user_answer['name'],
                    'image' => "http://localhost/ask/uploads/profiles/" . $fetch_user_answer['image'],
                )
            );
        }

        $question = array(
            'id' => $fetch_question['id'],
            'question' => $fetch_question['question'],
            'date' => $fetch_question['date'],
            'adults' => boolval($fetch_question['adults']),
            'user' => array(
                'id' => $fetch_user['id'],
                'name' => $fetch_user['name'],
                'image' => "http://localhost/ask/uploads/profiles/" . $fetch_user['image'],
            ),
            'answers' => $answers
        );

            $res = array(
                'errors' => null,
                'data' => $question
            );
        
    }else{
        $questions_db = mysqli_query($db_conn, "SELECT * FROM questions");
        $fetch_questions = mysqli_fetch_array($questions_db);

        $questions = array();
        foreach($questions_db as $question){
            $answers_db = mysqli_query($db_conn, "SELECT * FROM answers WHERE question='". $question['id'] ."' ");
            $fetch_answers = mysqli_fetch_array($answers_db);

            $user_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $question['user_id'] ."'");
            $fetch_user = mysqli_fetch_array($user_db);

            $answers = array();
            foreach($answers_db as $answer){
                $user_answer_db = mysqli_query($db_conn, "SELECT * FROM users WHERE id='". $answer['user_id'] ."'");
                $fetch_user_answer = mysqli_fetch_array($user_answer_db);

                $upvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM upvotes WHERE answer='". $answer['id'] ."'"));
                $downvotes_count = mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM downvotes WHERE answer='". $answer['id'] ."'"));

                $answers[] = array(
                    'id' => intval($answer['id']),
                    'answer' => $answer['answer'],
                    'date' => $answer['date'],
                    'upvotes' => intval($upvotes_count),
                    'downvotes' => intval($downvotes_count),
                    'user' => array(
                        'id' => intval($fetch_user_answer['id']),
                        'name' => $fetch_user_answer['name'],
                        'image' => "http://localhost/ask/uploads/profiles/" . $fetch_user_answer['image'],
                    )
                );
            }

            $questions[] = array(
                'id' => intval($question['id']),
                'question' => $question['question'],
                'date' => $question['date'],
                'adults' => boolval($question['adults']),
                'user' => array(
                    'id' => intval($fetch_user['id']),
                    'name' => $fetch_user['name'],
                    'image' => "http://localhost/ask/uploads/profiles/" . $fetch_user['image'],
                ),
                'answers' => $answers
            );

            $res = array(
                'errors' => null,
                'data' => $questions
            );
        }
       
    }
}else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $error = false;

    $question = filter_var($_POST['question'], FILTER_SANITIZE_STRING);
    $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
    $adults = filter_var($_POST['adults'], FILTER_SANITIZE_NUMBER_INT);

    if($question === null || $adults === null || $date === null || $category == null || $user_id == null){
        $error = true;
    }

    if(!$error){
        $query = mysqli_query($db_conn, "INSERT INTO questions(
                                                        `question`, `date`, `category`, `user_id`, `adults`
                                                    ) VALUES(
                                                        '$question', '$date', '". intval($category) ."', '". intval($user_id) ."', '". intval($adults) ."'
                                                    )");

        if($query){
            $res = array(
                'error' => null,
                'data' => array(
                    'question_id' => mysqli_insert_id($db_conn)
                )
            );
        }else{
            $res = array(
                'error' => array(
                    'message' => 'error while adding question'
                ),
                'data' => []
            );
        } 
    }else{
        $res = array(
            'error' => array(
                'message' => 'error while adding question'
            ),
            'data' => []
        );
    }       
}

echo json_encode($res);