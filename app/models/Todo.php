<?php

class Todo
{
    private $conn;
    private $table = "todos";
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //create Todo
    public function create($user_id, $title, $description, $status = 'pending', $priority = 'medium', $due_date = null)
    {
        $sql = "
        INSERT INTO $this->table (
            user_id,
            title,
            description,
            status,
            priority,
            due_date
        )
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
        ";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssss", $user_id, $title, $description, $status, $priority, $due_date);
        return mysqli_stmt_execute($stmt);
    }

    //update todo
    public function update(
        $id,
        $title,
        $description,
        $status,
        $priority,
        $due_date = null
    ) {
        $sql = "
        UPDATE $this->table
        SET
            title = ?,
            description = ?,
            status = ?,
            priority = ?,
            due_date = ?
        WHERE id = ?
        ";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "sssssi",
            $title,
            $description,
            $status,
            $priority,
            $due_date,
            $id
        );
        return mysqli_stmt_execute($stmt);
    }

    //delete todo
    public function delete($id)
    {
        $sql = "
        DELETE FROM $this->table
        WHERE id = ?
        ";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $id
        );
        return mysqli_stmt_execute($stmt);
    }

    /*
|--------------------------------------------------------------------------
| Get All Todos By User
|--------------------------------------------------------------------------
*/

    public function getAllByUser($user_id)
    {
        $sql = "
        SELECT *
        FROM $this->table
        WHERE user_id = ?
        ORDER BY created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $user_id);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
|--------------------------------------------------------------------------
| Find Todo By ID
|--------------------------------------------------------------------------
*/

    public function findById($id)
    {
        $sql = "
        SELECT *
        FROM $this->table
        WHERE id = ?
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /*
|--------------------------------------------------------------------------
| Check Todo Ownership
|--------------------------------------------------------------------------
*/

    public function belongsToUser($todo_id, $user_id)
    {
        $sql = "
        SELECT id
        FROM $this->table
        WHERE id = ?
        AND user_id = ?
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "ii",
            $todo_id,
            $user_id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
}
