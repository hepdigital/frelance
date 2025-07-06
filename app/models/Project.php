<?php
// app/models/Project.php

require_once __DIR__ . '/../database.php';

class Project {
    private $conn;
    private $table_name = "projects";

    public $id;
    public $brand_id;
    public $name;
    public $description;
    public $status;
    public $start_date;
    public $end_date;
    public $created_at;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    // Tüm projeleri getir (isteğe bağlı olarak brand_id'ye göre filtrele)
    public function getAll($brand_id = null) {
        $query = "SELECT p.*, b.name as brand_name FROM " . $this->table_name . " p JOIN brands b ON p.brand_id = b.id ";
        if ($brand_id) {
            $query .= " WHERE p.brand_id = :brand_id";
        }
        $query .= " ORDER BY p.name ASC";
        $stmt = $this->conn->prepare($query);

        if ($brand_id) {
            $stmt->bindParam(":brand_id", $brand_id);
        }
        $stmt->execute();
        return $stmt;
    }

    // ID'ye göre proje getir
    public function getById($id) {
        $query = "SELECT p.*, b.name as brand_name FROM " . $this->table_name . " p JOIN brands b ON p.brand_id = b.id WHERE p.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->brand_id = $row['brand_id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->status = $row['status'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Yeni proje oluştur
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET brand_id=:brand_id, name=:name, description=:description, status=:status, start_date=:start_date, end_date=:end_date";
        $stmt = $this->conn->prepare($query);

        $this->brand_id = htmlspecialchars(strip_tags($this->brand_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));

        $stmt->bindParam(":brand_id", $this->brand_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Proje güncelle
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET brand_id=:brand_id, name=:name, description=:description, status=:status, start_date=:start_date, end_date=:end_date WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->brand_id = htmlspecialchars(strip_tags($this->brand_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':brand_id', $this->brand_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Proje sil
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>