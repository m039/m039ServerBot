<?php

namespace m039;
use PDOException;

class DBManager {
    private string $host;
    private string $username;
    private string $password;
    private string $database;

    private $connection;
    
    public function __construct(
        string $host = null, 
        string $username = null, 
        string $password = null,
        string $database = null
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        try {
            $this->connection = new \PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
        } catch (\PDOException $e) {
            die ("Can't connect to database: " . $e->getMessage());
        }

        $this->createTableIfNotExist();
    }

    static public function createInstance() : DBManager {
        return new DBManager(
            host: getenv("DB_HOST"), 
            username: getenv("DB_USERNAME"), 
            password: getenv("DB_PASSWORD"),
            database: getenv("DB_DATABASE"),
        );
    }

    public function getAllEntries() : array {
        $statement = $this->connection->query("SELECT * FROM server_bot");
        return $statement->fetchAll(\PDO::FETCH_CLASS, DBEntry::class);
    }

    public function register(int $userId, int $chatId) {
        $statement = $this->connection->prepare("SELECT _id FROM server_bot WHERE user_id=? AND chat_id=? LIMIT 1");
        $statement->bindParam(1, $userId, \PDO::PARAM_INT);
        $statement->bindParam(2, $chatId, \PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            echo "Registering a new subscriber.\n";
            $statement = $this->connection->prepare("INSERT INTO server_bot (user_id, chat_id) VALUES (?, ?)");
            $statement->bindParam(1, $userId, \PDO::PARAM_INT);
            $statement->bindParam(2, $chatId, \PDO::PARAM_INT);
            $statement->execute();
        } else {
            $statement = $this->connection->prepare("UPDATE server_bot SET subscribed=1 WHERE user_id=? AND chat_id=?");
            $statement->bindParam(1, $userId, \PDO::PARAM_INT);
            $statement->bindParam(2, $chatId, \PDO::PARAM_INT);
            $statement->execute();
        }
    }

    public function unregister(int $userId, int $chatId) {
        $statement = $this->connection->prepare("UPDATE server_bot SET subscribed=0 WHERE user_id=? AND chat_id=?");
        $statement->bindParam(1, $userId, \PDO::PARAM_INT);
        $statement->bindParam(2, $chatId, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function updateEntry(DBEntry $entry) {
        $server_is_online = $entry->server_is_online? 1: 0;
        $subscribed = $entry->subscribed? 1 : 0;

        $statement = $this->connection->prepare("UPDATE server_bot SET server_is_online=?, subscribed=? WHERE _id=?");
        $statement->bindParam(1, $server_is_online, \PDO::PARAM_INT);
        $statement->bindParam(2, $subscribed, \PDO::PARAM_INT);
        $statement->bindParam(3, $entry->_id, \PDO::PARAM_INT);
        $statement->execute();
    }

    private function createTableIfNotExist() {
        try {
            $this->connection->query("DESCRIBE server_bot");
        } catch (PDOException $e) {
            echo "Creating the table.\n";
            $this->connection->query(
                "CREATE TABLE server_bot (_id INT NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, chat_id INT NOT NULL, server_is_online BOOLEAN NOT NULL DEFAULT 1, subscribed BOOLEAN NOT NULL DEFAULT 1, PRIMARY KEY (_id))"
            );
        }
    }
}