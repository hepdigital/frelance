<?php
// app/models/Brand.php

require_once __DIR__ . '/../database.php';

class Brand {
    private $conn;
    private $table_name = "brands";

    public $id;
    public $name;
    public $contact_person;
    public $email;
    public $phone;
    public $address;
    public $created_at;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    // Tüm markaları getir
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // ID'ye göre marka getir
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->contact_person = $row['contact_person'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Yeni marka oluştur
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, contact_person=:contact_person, email=:email, phone=:phone, address=:address";
        $stmt = $this->conn->prepare($query);

        // HTML karakterlerini temizle
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->contact_person = htmlspecialchars(strip_tags($this->contact_person));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));

        // Parametreleri bağla
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":contact_person", $this->contact_person);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Marka güncelle
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, contact_person=:contact_person, email=:email, phone=:phone, address=:address WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->contact_person = htmlspecialchars(strip_tags($this->contact_person));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':contact_person', $this->contact_person);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Marka sil
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