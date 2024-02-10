<?php

namespace m039\DB;

class DBManager {
    private string $host;
    private string $username;
    private string $password;
    private string $database;

    private \PDO $connection;

    private int $previousTime = 0;
    
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
        
        $this->createTableIfNotExist();
    }

    private function getPDO() : \PDO {
        $time = time();
        if (!$this->previousTime || $this->previousTime + 60 < $time) {
            try {
                $this->connection = new \PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
            } catch (\PDOException $e) {
                die ("Can't connect to database: " . $e->getMessage());
            }
            $this->previousTime = $time;
        }

        return $this->connection;
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
        $statement = $this->getPDO()->query("SELECT * FROM server_bot");
        return $statement->fetchAll(\PDO::FETCH_CLASS, DBEntry::class);
    }

    public function register(int $userId, int $chatId) {
        $statement = $this->getPDO()->prepare("SELECT _id FROM server_bot WHERE user_id=? AND chat_id=? LIMIT 1");
        $statement->bindParam(1, $userId, \PDO::PARAM_INT);
        $statement->bindParam(2, $chatId, \PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            echo "Registering a new subscriber.\n";
            $statement = $this->getPDO()->prepare("INSERT INTO server_bot (user_id, chat_id) VALUES (?, ?)");
            $statement->bindParam(1, $userId, \PDO::PARAM_INT);
            $statement->bindParam(2, $chatId, \PDO::PARAM_INT);
            $statement->execute();
        } else {
            $statement = $this->getPDO()->prepare("UPDATE server_bot SET subscribed=1 WHERE user_id=? AND chat_id=?");
            $statement->bindParam(1, $userId, \PDO::PARAM_INT);
            $statement->bindParam(2, $chatId, \PDO::PARAM_INT);
            $statement->execute();
        }
    }

    public function unregister(int $userId, int $chatId) {
        $statement = $this->getPDO()->prepare("UPDATE server_bot SET subscribed=0 WHERE user_id=? AND chat_id=?");
        $statement->bindParam(1, $userId, \PDO::PARAM_INT);
        $statement->bindParam(2, $chatId, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function updateEntry(DBEntry $entry) {
        $statement = $this->getPDO()->prepare("UPDATE server_bot SET server_is_online=:server_is_online, subscribed=:subscribed WHERE _id=:_id");
        $statement->execute([
            "server_is_online" => $entry->server_is_online,
            "subscribed" => $entry->subscribed,
            "_id" => $entry->_id
        ]);
    }

    private function createTableIfNotExist() {
        try {
            $this->getPDO()->query("DESCRIBE server_bot");
        } catch (\PDOException $e) {
            echo "Creating the table.\n";
            $this->getPDO()->query(
                "CREATE TABLE server_bot (_id INT NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, chat_id INT NOT NULL, server_is_online BOOLEAN NOT NULL DEFAULT 1, subscribed BOOLEAN NOT NULL DEFAULT 1, PRIMARY KEY (_id))"
            );
        }
    }
}