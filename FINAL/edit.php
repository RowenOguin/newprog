<?php
include "db/db_connect.php"; // include database connection

// Initialize empty array to avoid undefined variable notices
$customer = [];

// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $Id = intval($_GET['id']);

    // Fetch customer data by ID
    $sql = "SELECT * FROM customer_details WHERE Id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $Id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $customer = $result->fetch_assoc();
        } else {
            echo "<div class='alert alert-danger text-center'>Customer not found!</div>";
            exit;
        }
    } else {
        die("Statement failed: " . $conn->error);
    }
}

// Handle form update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $Id             = intval($_POST['Id']);
    $Firstname      = $conn->real_escape_string($_POST['Firstname']);
    $Lastname       = $conn->real_escape_string($_POST['Lastname']);
    $Address        = $conn->real_escape_string($_POST['Address']);
    $Province       = $conn->real_escape_string($_POST['Province']);
    $Postal         = $conn->real_escape_string($_POST['Postal']);
    $Phonenumber    = $conn->real_escape_string($_POST['Phonenumber']);
    $Email          = $conn->real_escape_string($_POST['Email']);
    $Departure      = $conn->real_escape_string($_POST['Departure']);
    $NumberOfAdults = intval($_POST['NumberOfAdults']);
    $NumberOfKids   = intval($_POST['NumberOfKids']);
    $GCASH          = $conn->real_escape_string($_POST['GCASH']);
    $SpecialRequest = $conn->real_escape_string($_POST['SpecialRequest']);
    $RoomType       = $conn->real_escape_string($_POST['RoomType']);

    // Update query
    $sql = "UPDATE customer_details 
            SET Firstname=?, Lastname=?, Address=?, Province=?, Postal=?, 
                Phonenumber=?, Email=?, Departure=?, NumberOfAdults=?, 
                NumberOfKids=?, GCASH=?, SpecialRequest=?, RoomType=?
            WHERE Id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
            "sssssssiisissi",
            $Firstname, $Lastname, $Address, $Province, $Postal,
            $Phonenumber, $Email, $Departure, $NumberOfAdults,
            $NumberOfKids, $GCASH, $SpecialRequest, $RoomType, $Id
        );
        if ($stmt->execute()) {
            echo "<div class='alert alert-success text-center'>Customer details updated successfully!</div>";
            // optionally: header("Location: index.php"); exit;
        } else {
            echo "<div class='alert alert-danger text-center'>Error: " . $stmt->error . "</div>";
        }
    } else {
        die("Statement failed: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device‑width, initial‑scale=1.0">
  <title>Edit Customer Details</title>
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
    .btn‑primary {
      background-color: #8d6e63;
      border-color: #8d6e63;
    }
    .btn‑primary:hover, .btn‑primary:focus {
      background-color: #7b5e57;
      border-color: #7b5e57;
    }
    .btn‑secondary {
      background-color: #a1887f;
      border-color: #a1887f;
      color: #fff;
    }
    .form‑label {
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
  

    <div class="card shadow">
      <div class="card-header text-center">
        <h4 class="mb‑0">Edit Customer Details</h4>
      </div>
      <div class="card-body">
        <form method="POST" novalidate>
          <div class="mb‑3">
            <label class="form-label">Id</label>
            <input type="text" name="Id" class="form-control" value="<?= htmlspecialchars($customer['Id'] ?? '') ?>" readonly>
          </div>

          <div class="mb‑3">
            <label class="form-label">Firstname</label>
            <input type="text" name="Firstname" class="form-control" value="<?= htmlspecialchars($customer['Firstname'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Lastname</label>
            <input type="text" name="Lastname" class="form-control" value="<?= htmlspecialchars($customer['Lastname'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Address</label>
            <input type="text" name="Address" class="form-control" value="<?= htmlspecialchars($customer['Address'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Province</label>
            <input type="text" name="Province" class="form-control" value="<?= htmlspecialchars($customer['Province'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Postal</label>
            <input type="text" name="Postal" class="form-control" value="<?= htmlspecialchars($customer['Postal'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Phonenumber</label>
            <input type="text" name="Phonenumber" class="form-control" value="<?= htmlspecialchars($customer['Phonenumber'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Email</label>
            <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($customer['Email'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Departure</label>
            <input type="text" name="Departure" class="form-control" value="<?= htmlspecialchars($customer['Departure'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Number Of Adults</label>
            <input type="number" name="NumberOfAdults" class="form-control" value="<?= htmlspecialchars($customer['NumberOfAdults'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Number Of Kids</label>
            <input type="number" name="NumberOfKids" class="form-control" value="<?= htmlspecialchars($customer['NumberOfKids'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">GCASH</label>
            <input type="text" name="GCASH" class="form-control" value="<?= htmlspecialchars($customer['GCASH'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Special Request</label>
            <input type="text" name="SpecialRequest" class="form-control" value="<?= htmlspecialchars($customer['SpecialRequest'] ?? '') ?>" required>
          </div>

          <div class="mb‑3">
            <label class="form-label">Room Type</label>
            <input type="text" name="RoomType" class="form-control" value="<?= htmlspecialchars($customer['RoomType'] ?? '') ?>" required>
          </div>

          <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Update Customer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
