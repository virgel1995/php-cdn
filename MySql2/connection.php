<?php
class FileDatabase
{
    private $connection;
    private $db_name = "cdn";
    // Constructor to establish the database connection and create the table
    public function __construct()
    {
        // $directory = "./database";
        // $db_name = $directory . "/database.sqlite3";
        $db_host = "localhost"; // Change to your database host if necessary
        $db_user = "root";
        $db_port = '3306';
        $db_pass = "";
        $db_name = $this->db_name;
        $this->connection = new mysqli($db_host, $db_user, $db_pass);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
        if ($this->connection->query($sql) !== TRUE) {
            echo "Error creating database: " . $this->connection->error;
        }
        $this->connection = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        $sql = "CREATE TABLE IF NOT EXISTS `$db_name`.`files` 
        (
        `id` INT NOT NULL AUTO_INCREMENT ,
        `name` VARCHAR(255) NULL , 
        `uuid` VARCHAR(255) NULL , 
        `original_name` VARCHAR(255) NULL , 
        `path` VARCHAR(255) NOT NULL ,
        `size` VARCHAR(255) NOT NULL , 
        `type` VARCHAR(255) NOT NULL ,
        `mime_type` VARCHAR(255) NOT NULL ,
        PRIMARY KEY (`id`)
        )
        ENGINE = InnoDB;";
        $result = $this->connection->query($sql);
        if (!$result) {
            die("Table creation failed: " . $this->connection->connect_error);
        }
    }
    // Inserts a new file into the `files` table
    public function createNewFile($genreated_name, $uuid, $original_name, $uniqueFileName, $file_size, $folderType, $file_type)
    {
        if (!$this->connection) {
            $this->handleError("error.createNewFile", "Database connection not available.");
        }
        $sql = "INSERT INTO `files`
        (
            `name`,
            `uuid`,
            `original_name`,
            `path`,
            `size`,
            `type`,
            `mime_type`
            ) 
        VALUES (
            '$genreated_name',       #name
            '$uuid',       #uuid
            '$original_name',       #name
            '$uniqueFileName',       #path
            '$file_size',            #size
            '$folderType',           #type
            '$file_type'             #mime_type
            );";
        $last_sql = "SELECT * FROM `files` WHERE id = LAST_INSERT_ID()";
        $insirting = $this->connection->query($sql);
        $result = $this->connection->query($last_sql);
        $rows = $result->fetch_assoc();
        return $rows;
    }

    public function findFileById($uuid)
    {
        if (!$this->connection) {
            $this->handleError("error.findFileById", "Database connection not available.");
        }
        $sql = "SELECT * FROM `files` WHERE uuid=$uuid;";
        $result = $this->connection->query($sql);
        if (!$result) {
            $this->handleError("error.findFileById", "Query failed: " . $this->connection->connect_error);
        }
        $row = $result->fetch_assoc();
        return $row;
    }

    // Deletes a file by its UUID
    public function deleteFileById($uuid, $filepath)
    {
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        if (!$this->connection) {
            $this->handleError("error.deleteFileById", "Database connection not available.");
        }
        $sql = "DELETE FROM `files` WHERE uuid = $uuid";
        $result = $this->connection->query($sql);
        if (!$result) {
            $this->handleError("error.deleteFileById", "Query failed: " . $this->connection->connect_error);
        }
        return $result;
    }

    // Helper function to handle errors
    private function handleError($status, $message)
    {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(
            array(
                "status" => $status,
                "message" => $message
            )
        );
        var_dump(
            array(
                "status" => $status,
                "message" => $message
            )
        );
        exit;
    }
}

$fileDatabase = new FileDatabase();
