<?php

class Todo
{
    private $conn;
    private $table = "todos";
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //create todo
    public function create($user_id, $title, $description)
    {
        $sql = "
        INSERT INTO $this->table (
            user_id,
            title,
            description
        )
        VALUES (
            ?,
            ?,
            ?
        )
        ";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $title, $description);
        return mysqli_stmt_execute($stmt);
    }

    //update todo
    public function update(
        $id,
        $title,
        $description,
        $status,
        $priority
    ) {
        $sql = "
        UPDATE $this->table
        SET
            title = ?,
            description = ?,
            status = ?,
            priority = ?
        WHERE id = ?
        ";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $title,
            $description,
            $status,
            $priority,
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
}
