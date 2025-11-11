<?php
// reservation.php
session_start();

// ---------------------------
// Configuration (edit if needed)
// ---------------------------
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "royal_cozy";

// Email settings
$ADMIN_EMAIL = "joreyoguin@gmail.com"; // notification email (you provided)
$FROM_NAME = "Royal Cozy Haven Reservations";
$FROM_EMAIL = "no-reply@yourdomain.com"; // change to your domain email for better deliverability

// ---------------------------
// Helper: connect to database
// ---------------------------
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . htmlspecialchars($conn->connect_error));
}

// ---------------------------
// CSRF token generation
// ---------------------------
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

$errors = [];
$successMessage = "";

// ---------------------------
// Handle POST submission
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    $posted_csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $posted_csrf)) {
        $errors[] = "Invalid form submission (CSRF mismatch). Please retry.";
    } else {
        // Read + trim inputs
        $firstname     = trim($_POST['firstname'] ?? '');
        $lastname      = trim($_POST['lastname'] ?? '');
        $address       = trim($_POST['address'] ?? '');
        $city          = trim($_POST['city'] ?? '');
        $province      = trim($_POST['province'] ?? '');
        $postal        = trim($_POST['postal'] ?? '');
        $phone         = trim($_POST['phone'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $arrival       = trim($_POST['arrival'] ?? '');
        $departure     = trim($_POST['departure'] ?? '');
        $adults        = (int)($_POST['adults'] ?? 1);
        $kids          = (int)($_POST['kids'] ?? 0);
        $gcash         = trim($_POST['gcash'] ?? '0');
        $special       = trim($_POST['special'] ?? '');
        $room_type     = trim($_POST['room_type'] ?? 'Cozy Classic');

        // Server-side validation
        if ($firstname === '') $errors[] = "Please enter first name.";
        if ($lastname === '') $errors[] = "Please enter last name.";
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email address.";
        if ($phone === '') $errors[] = "Please enter a contact phone number.";
        if ($arrival === '' || !strtotime($arrival)) $errors[] = "Please enter a valid check-in date.";
        if ($departure === '' || !strtotime($departure)) $errors[] = "Please enter a valid check-out date.";
        if (strtotime($departure) <= strtotime($arrival)) $errors[] = "Check-out must be after check-in.";
        if ($adults < 1) $errors[] = "Number of adults must be at least 1.";
        if (!ctype_digit($postal) && $postal !== '') $errors[] = "Postal code must be numeric (or leave blank).";
        if ($gcash === '') $gcash = 0;
        if (!ctype_digit((string)$gcash)) $errors[] = "GCash payment amount must be numeric (digits only) or 0.";

        // If no errors, insert into DB
        if (empty($errors)) {
            $sql = "INSERT INTO customer_details
                    (Firstname, Lastname, Address, City, Province, Postal, Phonenumber, Email, Arrival, Departure, NumberOfAdults, NumberOfKids, GCASH, SpecialRequest)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $errors[] = "Database error: " . htmlspecialchars($conn->error);
            } else {
                $stmt->bind_param(
                    "ssssssssssiiis",
                    $firstname,
                    $lastname,
                    $address,
                    $city,
                    $province,
                    $postal,
                    $phone,
                    $email,
                    $arrival,
                    $departure,
                    $adults,
                    $kids,
                    $gcash,
                    $special
                );

                if ($stmt->execute()) {
                    // Prepare email content
                    $subject_admin = "New Reservation: {$firstname} {$lastname} - {$arrival} to {$departure}";
                    $message_admin = "
A new reservation has been submitted on Royal Cozy Haven.

Guest:
Name: {$firstname} {$lastname}
Email: {$email}
Phone: {$phone}
Address: {$address}, {$city}, {$province}, {$postal}

Reservation:
Room type: {$room_type}
Arrival: {$arrival}
Departure: {$departure}
Adults: {$adults}
Kids: {$kids}
GCash amount: {$gcash}
Special requests: {$special}

-- End of message
";

                    $headers_admin = "From: {$FROM_NAME} <{$FROM_EMAIL}>\r\n";
                    $headers_admin .= "Reply-To: {$email}\r\n";
                    $headers_admin .= "Content-Type: text/plain; charset=UTF-8\r\n";

                    // Send admin email (may need proper SMTP on your server)
                    $mail_sent_admin = @mail($ADMIN_EMAIL, $subject_admin, $message_admin, $headers_admin);

                    // Send confirmation email to guest
                    $subject_guest = "Reservation received — Royal Cozy Haven";
                    $message_guest = "Dear {$firstname} {$lastname},\n\n";
                    $message_guest .= "Thank you for choosing Royal Cozy Haven. We received your reservation request:\n\n";
                    $message_guest .= "Room: {$room_type}\nArrival: {$arrival}\nDeparture: {$departure}\nAdults: {$adults}\nKids: {$kids}\n\n";
                    $message_guest .= "Special requests: {$special}\n\n";
                    $message_guest .= "We will contact you shortly to confirm availability and finalize details.\n\nWarm regards,\nRoyal Cozy Haven\n";

                    $headers_guest = "From: {$FROM_NAME} <{$FROM_EMAIL}>\r\n";
                    $headers_guest .= "Content-Type: text/plain; charset=UTF-8\r\n";

                    $mail_sent_guest = @mail($email, $subject_guest, $message_guest, $headers_guest);

                    // Build success message
                    $successMessage = "Thank you, {$firstname}! Your reservation request has been received.";
                    if (!$mail_sent_admin) {
                        $successMessage .= " (Note: notification email could not be sent by the server.)";
                    }
                    if (!$mail_sent_guest) {
                        $successMessage .= " (Note: confirmation email could not be sent to the guest email.)";
                    }

                    // Reset CSRF token after success to prevent resubmission
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
                } else {
                    $errors[] = "Failed to save reservation: " . htmlspecialchars($stmt->error);
                }
                $stmt->close();
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Reservation — Royal Cozy Haven</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --accent:#8B5E3C;
      --cream:#FFF8F1;
      --bg:#fffdfa;
      --muted:#6b6b6b;
      --card:#fff;
      --radius:12px;
      --shadow: 0 8px 24px rgba(17,17,17,.06);
    }
    *{box-sizing:border-box}
    body{
      font-family: "Inter", sans-serif;
      background:var(--bg);
      color:#2B1B12;
      margin:0;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }
    .container{
      width:92%;
      max-width:1100px;
      margin:0 auto;
      padding:28px 0;
    }

    /* Header */
    .site-header{
      background:var(--cream);
      position:sticky;
      top:0;
      z-index:1000;
      box-shadow:0 2px 10px rgba(0,0,0,0.05);
    }
    .header-inner{
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:14px 0;
    }
    .brand{display:flex;align-items:center;gap:12px}
    .logo{height:48px}
    .brand-name{
      font-family:"Playfair Display",serif;
      font-size:1.05rem;
      letter-spacing:1px;
      font-weight:700;
      color:#2B1B12;
      white-space:nowrap;
    }
    nav.main-nav ul{display:flex;gap:14px;list-style:none;margin:0;padding:0}
    nav.main-nav a{color:#2B1B12;text-decoration:none;font-weight:600;padding:6px 8px;border-radius:6px}
    nav.main-nav a:hover, nav.main-nav a.active{color:var(--accent);border-bottom:2px solid var(--accent)}

    /* Page header */
    .page-head{
      margin:28px 0;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }
    .page-head h1{
      font-family:"Playfair Display",serif;
      font-size:2rem;
      margin:0;
      color:var(--accent);
    }
    .subtitle{color:var(--muted);margin-top:6px;font-size:0.95rem}

    /* layout */
    .grid{
      display:grid;
      grid-template-columns: 1fr 420px;
      gap:28px;
      align-items:start;
    }

    /* Card */
    .card{
      background:var(--card);
      padding:20px;
      border-radius:12px;
      box-shadow:var(--shadow);
    }

    /* Form */
    form .row{display:flex;gap:12px}
    form .col{flex:1}
    label{display:block;font-weight:600;margin-bottom:6px}
    input[type="text"], input[type="email"], input[type="tel"], input[type="date"], select, textarea{
      width:100%;
      padding:10px 12px;
      border-radius:8px;
      border:1px solid #eee;
      background:#fff;
      font-size:0.95rem;
      color:#222;
    }
    textarea{min-height:110px;resize:vertical}
    .small{font-size:0.9rem;color:var(--muted)}

    .form-actions{display:flex;justify-content:flex-end;margin-top:12px;gap:10px}
    .btn{
      padding:10px 16px;border-radius:8px;border:0;font-weight:700;cursor:pointer;
    }
    .btn-primary{background:var(--accent);color:white}
    .btn-ghost{background:transparent;border:2px solid var(--accent);color:var(--accent)}

    /* Info side */
    .info-list{list-style:none;padding:0;margin:0}
    .info-list li{padding:8px 0;border-bottom:1px dashed #f0e9e4;color:#333}

    /* messages */
    .alert{padding:12px;border-radius:8px;margin-bottom:12px}
    .alert.error{background:#FFECEC;color:#9b2b2b;border:1px solid #f3c2c2}
    .alert.success{background:#ECF9F0;color:#175f2e;border:1px solid #bfeac9}

    /* footer small */
    .foot {text-align:center;color:var(--muted);font-size:0.9rem;margin-top:22px}

    @media (max-width:1100px){
      .grid{grid-template-columns:1fr 1fr}
    }
    @media (max-width:860px){
      .grid{grid-template-columns:1fr}
      nav.main-nav ul{display:none}
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="site-header">
    <div class="container header-inner">
      <a href="index.php" class="brand" aria-label="Royal Cozy Haven Home">
        <img src="assets/logo.svg" alt="logo" class="logo">
        <span class="brand-name">ROYAL COZY HAVEN</span>
      </a>

      <nav class="main-nav" aria-label="Main navigation">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="rooms.php">Rooms</a></li>
          <li><a href="gallery.php">Gallery</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a class="active" href="reservation.php">Reservation</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="container">
    <div class="page-head">
      <div>
        <h1>Make a Reservation</h1>
        <div class="subtitle">Book your stay with ease — fill out the form and we'll confirm availability shortly.</div>
      </div>
      <div class="small">Need help? Call us: <strong>+63 912 345 6789</strong></div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert error container">
        <strong>Please fix the following:</strong>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
      <div class="alert success container">
        <?php echo htmlspecialchars($successMessage); ?>
      </div>
    <?php endif; ?>

    <div class="grid">
      <!-- Left: form -->
      <div class="card">
        <form method="post" action="reservation.php" novalidate>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

          <div class="row" style="margin-bottom:12px">
            <div class="col">
              <label for="firstname">First name</label>
              <input id="firstname" name="firstname" type="text" required value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>">
            </div>
            <div class="col">
              <label for="lastname">Last name</label>
              <input id="lastname" name="lastname" type="text" required value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>">
            </div>
          </div>

          <div style="margin-bottom:12px">
            <label for="address">Address</label>
            <input id="address" name="address" type="text" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
          </div>

          <div class="row" style="margin-bottom:12px">
            <div class="col">
              <label for="city">City</label>
              <input id="city" name="city" type="text" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
            </div>
            <div class="col">
              <label for="province">Province</label>
              <input id="province" name="province" type="text" value="<?php echo htmlspecialchars($_POST['province'] ?? ''); ?>">
            </div>
          </div>

          <div class="row" style="margin-bottom:12px">
            <div class="col">
              <label for="postal">Postal code</label>
              <input id="postal" name="postal" type="text" inputmode="numeric" value="<?php echo htmlspecialchars($_POST['postal'] ?? ''); ?>">
            </div>
            <div class="col">
              <label for="phone">Phone</label>
              <input id="phone" name="phone" type="tel" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
          </div>

          <div style="margin-bottom:12px">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
          </div>

          <div class="row" style="margin-bottom:12px">
            <div class="col">
              <label for="arrival">Check-in</label>
              <input id="arrival" name="arrival" type="date" required value="<?php echo htmlspecialchars($_POST['arrival'] ?? ''); ?>">
            </div>
            <div class="col">
              <label for="departure">Check-out</label>
              <input id="departure" name="departure" type="date" required value="<?php echo htmlspecialchars($_POST['departure'] ?? ''); ?>">
            </div>
          </div>

          <div class="row" style="margin-bottom:12px">
            <div class="col">
              <label for="room_type">Room type</label>
              <select id="room_type" name="room_type">
                <option <?php if(($_POST['room_type'] ?? '') === 'Cozy Classic') echo 'selected'; ?>>Cozy Classic</option>
                <option <?php if(($_POST['room_type'] ?? '') === 'Deluxe Suite') echo 'selected'; ?>>Deluxe Suite</option>
                <option <?php if(($_POST['room_type'] ?? '') === 'Royal Loft') echo 'selected'; ?>>Royal Loft</option>
              </select>
            </div>
            <div class="col">
              <label for="adults">Adults</label>
              <input id="adults" name="adults" type="number" min="1" value="<?php echo htmlspecialchars($_POST['adults'] ?? '1'); ?>">
            </div>
          </div>

          <div class="row" style="margin-bottom:12px">
            <div class="col">
              <label for="kids">Kids</label>
              <input id="kids" name="kids" type="number" min="0" value="<?php echo htmlspecialchars($_POST['kids'] ?? '0'); ?>">
            </div>
            <div class="col">
              <label for="gcash">GCash amount (if deposit)</label>
              <input id="gcash" name="gcash" type="text" inputmode="numeric" value="<?php echo htmlspecialchars($_POST['gcash'] ?? '0'); ?>">
            </div>
          </div>

          <div style="margin-bottom:12px">
            <label for="special">Special requests</label>
            <textarea id="special" name="special"><?php echo htmlspecialchars($_POST['special'] ?? ''); ?></textarea>
          </div>

          <div class="form-actions">
            <button type="reset" class="btn btn-ghost">Reset</button>
            <button type="submit" class="btn btn-primary">Send Reservation</button>
          </div>
        </form>
      </div>

      <!-- Right: info / summary -->
      <aside class="card">
        <h3>Reservation summary</h3>
        <p class="small">Fill the form and we will contact you shortly to confirm your reservation and payment details.</p>

        <ul class="info-list" style="margin-top:12px">
          <li><strong>Check-in:</strong> <?php echo htmlspecialchars($_POST['arrival'] ?? '—'); ?></li>
          <li><strong>Check-out:</strong> <?php echo htmlspecialchars($_POST['departure'] ?? '—'); ?></li>
          <li><strong>Room:</strong> <?php echo htmlspecialchars($_POST['room_type'] ?? '—'); ?></li>
          <li><strong>Adults:</strong> <?php echo htmlspecialchars($_POST['adults'] ?? '—'); ?></li>
          <li><strong>Kids:</strong> <?php echo htmlspecialchars($_POST['kids'] ?? '—'); ?></li>
          <li><strong>GCash deposit:</strong> PHP <?php echo htmlspecialchars($_POST['gcash'] ?? '0'); ?></li>
        </ul>

        <h4 style="margin-top:16px">Location & contact</h4>
        <p class="small">123 Royal Avenue, City<br/>Phone: +63 912 345 6789<br/>Email: hello@royalcozyhaven.com</p>

        <div style="margin-top:14px">
          <a href="contact.php" class="btn btn-ghost" style="display:inline-block">Contact us</a>
        </div>
      </aside>
    </div>

    <div class="foot">© <?php echo date('Y'); ?> Royal Cozy Haven. All rights reserved.</div>
  </main>

</body>
</html>
