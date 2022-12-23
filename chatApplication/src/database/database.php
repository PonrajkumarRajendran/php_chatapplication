<?php
class DB{
    private $pdo;

    public function __construct(){
        
        //Calling the createTables method upon initiation.
        $this->createTables();

    }

    public function createTables(){
        
        $tablePDO = $this->connect();

        //Creating the user table upon initiation, if doesn't already exist.
        /*
        The user table consists of only user_id and username. We can add columns to add password and password hash to the table and use the same table for authentication and authorization purposes.        
        */
        $tableCommand1 = "CREATE TABLE IF NOT EXISTS user
        (user_id INTEGER PRIMARY KEY AUTOINCREMENT,
         username TEXT NOT NULL)";
        
        $tableStmt1 = $tablePDO->query($tableCommand1);
        $tableStmt1->execute();

        //Creating message table upon initiation, if doesn't already exist.
        /*

        The messages table consists of message_id(primary message id), message_receiver(user_id of the receiving user), message_sender(user_id of the sending user), message_sender_name(username of the sender), message_time(date and time stored as string which can be converted to datetime variable in the frontend upon receiving, and which has to be converted in the front end before sending).

        */
        $tableCommand2 = "CREATE TABLE IF NOT EXISTS messages(message_id INTEGER PRIMARY KEY AUTOINCREMENT,
         message_content TEXT NOT NULL,
         message_receiver INTEGER NOT NULL,
         message_sender INTEGER NOT NULL,
         message_sender_name TEXT NOT NULL,
         message_time TEXT NOT NULL)";
        $tableStmt2 = $tablePDO->query($tableCommand2);
        $tableStmt2->execute();
    }

    public function connect(){
        if($this->pdo == null){
            //creating the database file in the root folder to avoid any read and write issues that may arise due to directory permissions.
            $this->pdo = new \PDO('sqlite:random.db');
        }
        return $this->pdo;
    }
}
?>