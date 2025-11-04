<?php
include "db/db_connect.php"; // include database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize / validate inputs
    $Id             = intval($_POST['Id']);
    $Firstname      = trim($_POST['Firstname']);
    $Lastname       = trim($_POST['Lastname']);
    $Address        = trim($_POST['Address']);
    $City           = trim($_POST['City']);
    $Province       = trim($_POST['Province']);
    $Postal         = trim($_POST['Postal']);
    $Phonenumber    = trim($_POST['Phonenumber']);
    $Email          = trim($_POST['Email']);
    $Arrival        = trim($_POST['Arrival']);
    $Departure      = trim($_POST['Departure']);
    $NumberOfAdults = intval($_POST['NumberOfAdults']);
    $NumberOfKids   = intval($_POST['NumberOfKids']);
    $GCASH          = trim($_POST['GCASH']);
    $SpecialRequest = trim($_POST['SpecialRequest']);
    $RoomType       = trim($_POST['RoomType']);

    // Check if Id already exists
    $checkSql = "SELECT Id FROM customer_details WHERE Id = ?";
    if ($stmtCheck = $conn->prepare($checkSql)) {
        $stmtCheck->bind_param("i", $Id);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            echo "<div class='alert alert-warning text-center'>ID number <b>{$Id}</b> already exists!</div>";
        } else {
            // Insert new record
            $insertSql = "INSERT INTO customer_details 
                (Id, Firstname, Lastname, Address, City, Province, Postal, Phonenumber, Email, Arrival, Departure, NumberOfAdults, NumberOfKids, GCASH, SpecialRequest, RoomType)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($insertSql)) {
                $stmt->bind_param(
                    "isssssss ssiiiss",
                    $Id, $Firstname, $Lastname, $Address, $City, $Province,
                    $Postal, $Phonenumber, $Email, $Arrival, $Departure,
                    $NumberOfAdults, $NumberOfKids, $GCASH, $SpecialRequest, $RoomType
                );
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success text-center'>New customer added successfully!</div>";
                } else {
                    echo "<div class='alert alert-danger text-center'>Error: " . htmlspecialchars($stmt->error) . "</div>";
                }
                $stmt->close();
            } else {
                echo "<div class='alert alert-danger text-center'>Error preparing statement: " . htmlspecialchars($conn->error) . "</div>";
            }
        }
        $stmtCheck->close();
    } else {
        echo "<div class='alert alert-danger text-center'>Error preparing ID check: " . htmlspecialchars($conn->error) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device‑width, initial-scale=1.0">
    <title>Create Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      /* Brown theme palette + wider layout */
      body {
        background-color: #f9f6f3;
        color: #4e342e;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        font-size: 1.1rem;
      }
      .container‑xxl {
        max-width: 1400px;
      }
      .card-header {
        background-color: #795548;
        color: #fff;
        padding: 1.2rem;
      }
      .card-body {
        padding: 1.5rem;
      }
      .btn-success {
        background-color: #8d6e63;
        border-color: #8d6e63;
      }
      .btn-success:hover, .btn-success:focus {
        background-color: #7b5e57;
        border-color: #7b5e57;
      }
      .btn-secondary {
        background-color: #a1887f;
        border-color: #a1887f;
        color: #fff;
      }
      .form-label {
        font-weight: 500;
      }
      .form-control {
        border: 1px solid #d7ccc8;
        background-color: #fff;
        font-size: 1rem;
        padding: .75rem;
      }
      .mb‑3 {
        margin-bottom: 1.25rem !important;
      }
      @media (max-width: 768px) {
        .form-control {
          font-size: .95rem;
        }
      }
    </style>
</head>
<body>
   <div class="container mt‑5"></div>
    <div class="card shadow">
      <div class="card-header text-center">
        <h4 class="mb‑0">Add New Customer</h4>
      </div>
      <div class="card-body">
        <form method="POST" novalidate>
          <div class="mb‑3">
            <label class="form-label">ID</label>
            <input type="number" name="Id" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Firstname</label>
            <input type="text" name="Firstname" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Lastname</label>
            <input type="text" name="Lastname" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Address</label>
            <input type="text" name="Address" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">City</label>
            <input type="text" name="City" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Province</label>
            <input type="text" name="Province" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Postal</label>
            <input type="text" name="Postal" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="Phonenumber" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Email</label>
            <input type="email" name="Email" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Arrival</label>
            <input type="date" name="Arrival" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Departure</label>
            <input type="date" name="Departure" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Number Of Adults</label>
            <input type="number" name="NumberOfAdults" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Number Of Kids</label>
            <input type="number" name="NumberOfKids" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">GCASH</label>
            <input type="text" name="GCASH" class="form-control" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Special Request</label>
            <input type="text" name="SpecialRequest" class="form-control">
          </div>

          <div class="mb‑3">
            <label class="form-label">Room Type</label>
            <input type="text" name="RoomType" class="form-control" required>
          </div>

          <div class="d‑flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-success">Add Customer</button>
          </div>

        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
