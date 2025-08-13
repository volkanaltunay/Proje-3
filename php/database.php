<?php

class Database {
    private $host;
    private $dbname;
    private $user;
    private $password;
    public $conn;

    public function __construct() {
        $this->loadEnv();
        $this->conn = null;
    }

    private function loadEnv() {
        $filePath = __DIR__ . '/.env';
        if (!file_exists($filePath)) {
            die("`.env` dosyası bulunamadı.");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            putenv(sprintf('%s=%s', $name, $value));
        }

        $this->host = getenv('DB_HOST');
        $this->dbname = getenv('DB_NAME');
        $this->user = getenv('DB_USER');
        $this->password = getenv('DB_PASS');
    }

    public function getConnection() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Bağlantı hatası: " . $exception->getMessage();
        }
        return $this->conn;
    }
}