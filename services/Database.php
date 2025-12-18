<?php
class Database
{
    protected $connect;  // Change to protected so subclasses can access it
    private $host = "localhost";
    // private $user = "bsd";
    // private $db = "alivetechnology";
    // private $pass = "Bsd@2025";
     private $user = "root";
    private $db = "newisp";
    private $pass = "";

    public function __construct()
    {
        try {
            $this->connect = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=utf8mb4", // charset added
                $this->user,
                $this->pass
            );

            // Error mode set to exception
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }


    // Method to get the connection
    public function getConnection()
    {
        return $this->connect;
    }
}
