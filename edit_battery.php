<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// --- Database Connection ---
$servername = "localhost";
$username = "root";
$password = "";
$database = "battery_stock_system";
$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check for battery id in the URL
if (!isset($_GET['id'])) {
    header("Location: manage_batteries.php");
    exit;
}
$id = intval($_GET['id']);

// Fetch battery record
$query = "SELECT * FROM batteries WHERE battery_id = $id";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    die("Battery not found.");
}
$battery = mysqli_fetch_assoc($result);

// Fetch supplier list for dropdown
$suppliers = mysqli_query($conn, "SELECT supplier_id, company_name AS supplier_name FROM suppliers");

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_battery'])) {
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $model = trim(mysqli_real_escape_string($conn, $_POST['model']));
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $supplier_id = intval($_POST['supplier_id']);
    
    $updateQuery = "UPDATE batteries SET supplier_id = $supplier_id, name = '$name', model = '$model', price = $price, quantity = $quantity WHERE battery_id = $id";
    if (mysqli_query($conn, $updateQuery)) {
        $message = "Battery updated successfully.";
		header("location:manage_batteries.php");
        // Optionally, you could redirect after success.
    } else {
        $message = "Error updating battery: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Battery - Battery Stock System</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Fixed Header */
    header {
      background: url('mb1.jpg') no-repeat center center;
      background-size: cover;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 50;
      transition: transform 0.3s ease;
    }
    header:hover {
      transform: scale(1.03);
    }
    header .header-overlay {
      background-color: rgba(0,0,0,0.6);
      padding: 20px;
      text-align: center;
    }
    /* Fixed Footer */
    footer {
      background: url('mb2.jpg') no-repeat center center;
      background-size: cover;
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 50;
      transition: transform 0.3s ease;
    }
    footer:hover {
      transform: scale(1.03);
    }
    footer .footer-overlay {
      background-color: rgba(0,0,0,0.6);
      padding: 10px;
      text-align: center;
      font-size: 0.875rem;
    }
    /* Main Content Container */
    .main-content {
      margin-top: 120px;
      margin-bottom: 80px;
      padding: 20px;
    }
    /* Glassmorphism Effect */
    .glass {
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 1rem;
      padding: 20px;
    }
  </style>
</head>
<body class="bg-cover" style="background-image: url('cc.jpg');">
  <!-- Header -->
  <header>
    <div class="header-overlay text-white">
      <h1 class="text-4xl font-bold">Battery Stock Information System</h1>
      <p class="mt-2 text-xl">Edit Battery</p>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container mx-auto main-content">
    <?php if ($message != ""): ?>
      <div class="mb-4 p-4 bg-green-500 text-white rounded"><?php echo $message; ?></div>
    <?php endif; ?>
    <div class="glass shadow-lg">
      <h2 class="text-2xl font-bold mb-4 text-white">Edit Battery Details</h2>
      <form action="edit_battery.php?id=<?php echo $id; ?>" method="POST" class="space-y-4">
        <div>
          <label class="block text-white">Battery Name:</label>
          <input type="text" name="name" value="<?php echo htmlspecialchars($battery['name']); ?>" class="w-full p-2 rounded" required>
        </div>
        <div>
          <label class="block text-white">Model:</label>
          <input type="text" name="model" value="<?php echo htmlspecialchars($battery['model']); ?>" class="w-full p-2 rounded">
        </div>
        <div>
          <label class="block text-white">Price:</label>
          <input type="number" step="0.01" name="price" value="<?php echo $battery['price']; ?>" class="w-full p-2 rounded" required>
        </div>
        <div>
          <label class="block text-white">Quantity:</label>
          <input type="number" name="quantity" value="<?php echo $battery['quantity']; ?>" class="w-full p-2 rounded" required>
        </div>
        <div>
          <label class="block text-white">Select Supplier:</label>
          <select name="supplier_id" class="w-full p-2 rounded" required>
            <option value="">-- Select Supplier --</option>
            <?php while($supplier = mysqli_fetch_assoc($suppliers)): ?>
              <option value="<?php echo $supplier['supplier_id']; ?>" <?php if($supplier['supplier_id'] == $battery['supplier_id']) echo "selected"; ?>>
                <?php echo $supplier['supplier_name']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div>
          <button type="submit" name="update_battery" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">Update Battery</button>
        </div>
      </form>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="footer-overlay text-white">
      &copy; 2025 Battery Stock Information System. All Rights Reserved.
    </div>
  </footer>
</body>
</html>
