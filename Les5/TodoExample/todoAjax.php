<?php
include "TodoDb.php";

function validate($str) {
    return trim(htmlspecialchars($str));
}

function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}


header('Content-type: application/json');

$todoDb = new TodoDb();

if(is_ajax())
{
    if($_SERVER['REQUEST_METHOD'] === "GET")
    {
        if(isset($_GET['todoId']) && filter_var($_GET['todoId'], FILTER_VALIDATE_INT) !== false)
        {
            $todoId = (int)$_GET['todoId'];
            $result = $todoDb->getTodo($todoId);
            $todoDb->closeConnection();

            echo json_encode($result);
        } else {
            $result = $todoDb->getTodos();
            $todoDb->closeConnection();

            echo json_encode($result);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] === "POST")
    {
        $input = json_decode($_POST["myData"]);
        $action = $input->ACTION;

        if ($action === "DeleteTodo")
        {
            $todoId = validate($input->todoId);

            if(isset($todoId) &&
                filter_var($todoId, FILTER_VALIDATE_INT) !== false )
            {
                $todoId = (int)$todoId;
                $result = $todoDb->deleteTodo($todoId);
                $todoDb->closeConnection();

                echo json_encode($result);
            }
        }
        else if ($action === "AddTodo")
        {
            $description = $input->description;
            $done = validate($input->done ?? false);

            if (isset($description) && !empty($description))
            {
                $result = $todoDb->addTodo($description, $done);
                $todoDb->closeConnection();

                echo json_encode($result);
            }
        }
        else if ($action === "UpdateTodo")
        {
            $todoId = validate($input->todoId);
            $description = validate($input->description);

            if (isset($todoId, $description) &&
                filter_var($todoId, FILTER_VALIDATE_INT) !== false &&
                !empty($description))
            {
                $todo = new Todo();
                $todo->todoId = (int)$todoId;
                $todo->description = $description;
                $todo->done = isset($input->done);

                $result = $todoDb->updateTodo($todo);
                $todoDb->closeConnection();

                echo json_encode($result);
            }
        }
        else if ($action === "EditTodo")
        {
            $todoId = validate($input->todoId);

            if (isset($todoId) &&
                filter_var($todoId, FILTER_VALIDATE_INT) !== false)
            {
                $todoId = (int)$todoId;
                $rowToEdit = $todoDb->getTodo($todoId);
                $todoDb->closeConnection();

                echo  json_encode($rowToEdit);
            }
        }
    }
}


