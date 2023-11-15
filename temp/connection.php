<?php
class FileDatabase
{
    private $connection;
    private $targetDirectory = "./uploads/";
    // Constructor to establish the database connection and create the table
    public function __construct()
    {
        $directory = "./database";
        $db_name = $directory . "/database.sqlite3";
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $this->connection = new SQLite3($db_name);
        if (!$this->connection) {
            die("Connection failed: " . $this->connection->lastErrorMsg());
        }
        $sql = "CREATE TABLE IF NOT EXISTS `files` (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NULL,
            uuid TEXT NULL,
            original_name TEXT NULL,
            path TEXT NOT NULL,
            size TEXT NOT NULL,
            type TEXT NOT NULL,
            mime_type TEXT NOT NULL
        )";
        $result = $this->connection->exec($sql);
        if (!$result) {
            die("Table creation failed: " . $this->connection->lastErrorMsg());
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
                name,
                uuid,
                original_name,
                path,
                size,
                type,
                mime_type
            ) 
            VALUES (
                :genreated_name,
                :uuid,
                :original_name,
                :uniqueFileName,
                :file_size,
                :folderType,
                :file_type
            )";
    
        $stmt = $this->connection->prepare($sql);
    
        // Bind parameters
        $stmt->bindValue(':genreated_name', $genreated_name, SQLITE3_TEXT);
        $stmt->bindValue(':uuid', $uuid, SQLITE3_TEXT);
        $stmt->bindValue(':original_name', $original_name, SQLITE3_TEXT);
        $stmt->bindValue(':uniqueFileName', $uniqueFileName, SQLITE3_TEXT);
        $stmt->bindValue(':file_size', $file_size, SQLITE3_INTEGER);
        $stmt->bindValue(':folderType', $folderType, SQLITE3_TEXT);
        $stmt->bindValue(':file_type', $file_type, SQLITE3_TEXT);
    
        $result = $stmt->execute();
    
        if (!$result) {
            $this->handleError("error.createNewFile", "Query failed: " . $this->connection->lastErrorMsg());
        }
    
        $lastInsertID = $this->connection->lastInsertRowID();
        $last_sql = "SELECT * FROM `files` WHERE id = $lastInsertID";
        $result = $this->connection->query($last_sql);
    
        if (!$result) {
            $this->handleError("error.createNewFile", "Query failed: " . $this->connection->lastErrorMsg());
        }
    
        $rows = $result->fetchArray(SQLITE3_ASSOC);
        return $rows;
    }
    
    // Retrieves a file from the database using its UUID
    public function findFileById($uuid)
    {
        if (!$this->connection) {
            $this->handleError("error.findFileById", "Database connection not available.");
        }

        $sql = "SELECT * FROM `files` WHERE uuid='$uuid'";
        $result = $this->connection->query($sql);

        if (!$result) {
            $this->handleError("error.findFileById", "Query failed: " . $this->connection->lastErrorMsg());
        }

        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row;
    }

    // Deletes a file by its ID
    public function deleteFileById($uuid, $filepath)
    {
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        if (!$this->connection) {
            $this->handleError("error.deleteFileById", "Database connection not available.");
        }
        $sql = "DELETE FROM `files` WHERE uuid = :uuid";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':uuid', $uuid, SQLITE3_TEXT);
        $result = $stmt->execute();
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
