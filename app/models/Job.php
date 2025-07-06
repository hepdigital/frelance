<?php
// app/models/Job.php

require_once __DIR__ . '/../database.php';

class Job {
    private $conn;
    private $table_name = "jobs";

    public $id;
    public $project_id;
    public $title;
    public $description;
    public $price;
    public $is_monthly_retainer;
    public $monthly_retainer_amount;
    public $completed_at;
    public $status;
    public $created_at;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    // Tüm işleri getir (isteğe bağlı olarak project_id'ye göre filtrele)
    public function getAll($project_id = null) {
        $query = "SELECT j.*, p.name as project_name, b.name as brand_name 
                  FROM " . $this->table_name . " j 
                  JOIN projects p ON j.project_id = p.id
                  JOIN brands b ON p.brand_id = b.id ";
        if ($project_id) {
            $query .= " WHERE j.project_id = :project_id";
        }
        $query .= " ORDER BY j.completed_at DESC, j.created_at DESC";
        $stmt = $this->conn->prepare($query);

        if ($project_id) {
            $stmt->bindParam(":project_id", $project_id);
        }
        $stmt->execute();
        return $stmt;
    }

    // Belirli bir marka ve ay için işleri getir (fatura oluşturmak için)
    public function getJobsForInvoice($brand_id, $month, $year) {
        $query = "SELECT j.*, p.name as project_name
                  FROM " . $this->table_name . " j
                  JOIN projects p ON j.project_id = p.id
                  WHERE p.brand_id = :brand_id
                  AND MONTH(j.completed_at) = :month
                  AND YEAR(j.completed_at) = :year
                  ORDER BY j.completed_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":brand_id", $brand_id);
        $stmt->bindParam(":month", $month);
        $stmt->bindParam(":year", $year);
        $stmt->execute();
        return $stmt;
    }

    // ID'ye göre iş getir
    public function getById($id) {
        $query = "SELECT j.*, p.name as project_name, b.name as brand_name
                  FROM " . $this->table_name . " j
                  JOIN projects p ON j.project_id = p.id
                  JOIN brands b ON p.brand_id = b.id
                  WHERE j.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->project_id = $row['project_id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->is_monthly_retainer = $row['is_monthly_retainer'];
            $this->monthly_retainer_amount = $row['monthly_retainer_amount'];
            $this->completed_at = $row['completed_at'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Yeni iş oluştur
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET project_id=:project_id, title=:title, description=:description, price=:price, is_monthly_retainer=:is_monthly_retainer, monthly_retainer_amount=:monthly_retainer_amount, completed_at=:completed_at, status=:status";
        $stmt = $this->conn->prepare($query);

        $this->project_id = htmlspecialchars(strip_tags($this->project_id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->is_monthly_retainer = htmlspecialchars(strip_tags($this->is_monthly_retainer));
        $this->monthly_retainer_amount = htmlspecialchars(strip_tags($this->monthly_retainer_amount));
        $this->completed_at = htmlspecialchars(strip_tags($this->completed_at));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":project_id", $this->project_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":is_monthly_retainer", $this->is_monthly_retainer);
        $stmt->bindParam(":monthly_retainer_amount", $this->monthly_retainer_amount);
        $stmt->bindParam(":completed_at", $this->completed_at);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // İş güncelle
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET project_id=:project_id, title=:title, description=:description, price=:price, is_monthly_retainer=:is_monthly_retainer, monthly_retainer_amount=:monthly_retainer_amount, completed_at=:completed_at, status=:status WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->project_id = htmlspecialchars(strip_tags($this->project_id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->is_monthly_retainer = htmlspecialchars(strip_tags($this->is_monthly_retainer));
        $this->monthly_retainer_amount = htmlspecialchars(strip_tags($this->monthly_retainer_amount));
        $this->completed_at = htmlspecialchars(strip_tags($this->completed_at));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':is_monthly_retainer', $this->is_monthly_retainer);
        $stmt->bindParam(':monthly_retainer_amount', $this->monthly_retainer_amount);
        $stmt->bindParam(':completed_at', $this->completed_at);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // İş sil
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