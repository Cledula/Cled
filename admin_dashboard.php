<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit();
}

// Connect to MySQL database
$servername = "localhost";
$username = "root";
$password = "root";
$database = "ems";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch appointment data
$sql = "SELECT * FROM appointment"; // Assuming "appointment" is the name of your table
$result = $conn->query($sql);

// Handle adding appointment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_appointment"])) {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $service_type = $_POST["service_type"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $additional_info = $_POST["additional_info"];

    // Prepare SQL statement to insert new appointment
    $stmt = $conn->prepare("INSERT INTO appointment (first_name, last_name, service_type, email, phone, additional_info) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $service_type, $email, $phone, $additional_info);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "<script>alert('Appointment added successfully!');</script>";
        // Redirect to avoid form resubmission
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Error adding appointment.');</script>";
    }
}

// Handle removing appointment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_appointment"])) {
    $appointment_id = $_POST["appointment_id"];

    // Prepare SQL statement to remove appointment
    $stmt = $conn->prepare("DELETE FROM appointment WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "<script>alert('Appointment removed successfully!');</script>";
        // Redirect to avoid form resubmission
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Error removing appointment.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome to the Admin Dashboard</h2>
    
    <!-- Display scheduled appointments here -->
    <h3>Scheduled Appointments</h3>
    <table border="1">
        <tr>
            <th>Appointment ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Service Type</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Additional Info</th>
        </tr>
        <?php
        // Display appointment data in a table
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row["id"]."</td>";
            echo "<td>".$row["first_name"]."</td>";
            echo "<td>".$row["last_name"]."</td>";
            echo "<td>".$row["service_type"]."</td>";
            echo "<td>".$row["email"]."</td>";
            echo "<td>".$row["phone"]."</td>";
            echo "<td>".$row["additional_info"]."</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <!-- Add Appointment Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h3>Add Appointment</h3>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="text" name="service_type" placeholder="Service Type" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="tel" name="phone" placeholder="Phone" required>
        <input type="text" name="additional_info" placeholder="Additional Info">
        <button type="submit" name="add_appointment">Add Appointment</button>
    </form>

    <!-- Remove Appointment Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h3>Remove Appointment</h3>
        <input type="text" name="appointment_id" placeholder="Appointment ID" required>
        <button type="submit" name="remove_appointment">Remove Appointment</button>
    </form>

    <?php
    // Close database connection
    $conn->close();
    ?>
</body>
</html>
