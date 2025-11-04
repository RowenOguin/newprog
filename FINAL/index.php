<?php
// Include the database connection file
include "db/db_connect.php";

// List of valid filter columns (for safety)
$validFilters = [
    'Firstname','Lastname','Address','City','Province',
    'Postal','Phonenumber','Email','Arrival','Departure',
    'NumberOfAdults','NumberOfKids','GCASH','SpecialRequest','RoomType'
];

$search = "";
$filter = "";
$sql    = "SELECT * FROM customer_details";
$params = [];

if (isset($_GET['search'], $_GET['filter'])) {
    $search = trim($_GET['search']);
    $filter = $_GET['filter'];
    if ($search !== '' && in_array($filter, $validFilters, true)) {
        $sql      = "SELECT * FROM customer_details WHERE `$filter` LIKE ?";
        $params[] = "%{$search}%";
    }
}

if ($stmt = $conn->prepare($sql)) {
    if (!empty($params)) {
        // all parameters here are strings
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer List</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Brown theme palette + enlarged layout */
    body {
      background-color: #f9f6f3;
      color: #4e342e;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      font-size: 1.1rem;
      line-height: 1.6;
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
      font-size: 1rem;
      padding: .75rem 1.25rem;
    }
    .btn-success:hover, .btn-success:focus {
      background-color: #7b5e57;
      border-color: #7b5e57;
    }
    .btn-warning {
      background-color: #bcaaa4;
      border-color: #bcaaa4;
      color: #4e342e;
      font-size: 1rem;
      padding: .65rem 1rem;
    }
    .btn-danger {
      background-color: #a1887f;
      border-color: #a1887f;
      font-size: 1rem;
      padding: .65rem 1rem;
    }
    .form-control, .form-select {
      border: 1px solid #d7ccc8;
      background-color: #fff;
      padding: .75rem;
      font-size: 1rem;
    }
    .table thead {
      background-color: #6d4c41;
      color: #fff;
    }
    .table thead th {
      font-size: 1.05rem;
      padding: .9rem .75rem;
    }
    .table tbody td {
      font-size: .95rem;
      padding: .65rem .75rem;
    }
    .table tbody tr:hover {
      background-color: #e6dfda;
    }
    .table-responsive {
      overflow-x: auto;
    }
    @media (max-width: 768px) {
      .table thead th, .table tbody td {
        font-size: .90rem;
        white-space: nowrap;
      }
    }
  </style>
</head>
<body>
  <div class="container‑xxl mt‑5">
    <div class="card shadow">
      <div class="card-header text-center">
        <h3>Customer List</h3>
      </div>
      <div class="card-body">
        <form method="GET" class="row g-3 mb-4 align-items-end">
          <div class="col-md-5">
            <label for="searchInput" class="form-label">Search</label>
            <input type="text" id="searchInput" name="search" value="<?= htmlspecialchars($search) ?>"
                   class="form-control" placeholder="Enter keyword…">
          </div>
          <div class="col-md-4">
            <label for="filterSelect" class="form-label">Filter By</label>
            <select id="filterSelect" name="filter" class="form-select">
              <option value="">-- Select Filter --</option>
              <?php foreach ($validFilters as $f): ?>
                <option value="<?= htmlspecialchars($f) ?>" <?= ($filter === $f ? "selected" : "") ?>>
                  <?= htmlspecialchars($f) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Search</button>
          </div>
          <div class="col-md-1">
            <a href="index.php" class="btn btn-secondary w-100">Reset</a>
          </div>
        </form>

        <div class="mb-3 text-end">
          <a href="create.php" class="btn btn-success">+ Add New Customer</a>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle text-center">
            <thead>
              <tr>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Address</th>
                <th>City</th>
                <th>Province</th>
                <th>Phonenumber</th>
                <th>Email</th>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Adults</th>
                <th>Kids</th>
                <th>GCASH</th>
                <th>Special Request</th>
                <th>Room Type</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row["Firstname"]) ?></td>
                    <td><?= htmlspecialchars($row["Lastname"]) ?></td>
                    <td><?= htmlspecialchars($row["Address"]) ?></td>
                    <td><?= htmlspecialchars($row["City"]) ?></td>
                    <td><?= htmlspecialchars($row["Province"]) ?></td>
                    <td><?= htmlspecialchars($row["Phonenumber"]) ?></td>
                    <td><?= htmlspecialchars($row["Email"]) ?></td>
                    <td><?= htmlspecialchars($row["Arrival"]) ?></td>
                    <td><?= htmlspecialchars($row["Departure"]) ?></td>
                    <td><?= htmlspecialchars($row["NumberOfAdults"]) ?></td>
                    <td><?= htmlspecialchars($row["NumberOfKids"]) ?></td>
                    <td><?= htmlspecialchars($row["GCASH"]) ?></td>
                    <td><?= htmlspecialchars($row["SpecialRequest"]) ?></td>
                    <td><?= htmlspecialchars($row["RoomType"]) ?></td>
                    <td>
                      <a href="edit.php?id=<?= urlencode($row["Id"]) ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                      <a href="delete.php?id=<?= urlencode($row["Id"]) ?>"
                         class="btn btn-sm btn-danger"
                         onclick="return confirm('Are you sure you want to delete customer details?');">
                        Delete
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="15" class="text-muted">No records found</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
