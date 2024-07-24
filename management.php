<?php
include 'config.php';
include 'session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $title = $_POST['title'];
    $image = $_FILES['image']['name'];
    $target_dir = "images/";
    $target_file = $target_dir . basename($image);

    // Check if the directory exists, if not create it
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO logos (title, image_path) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $title, $target_file);
        $stmt->execute();
        $stmt->close();
        echo "New logo added successfully.";
    } else {
        echo "Error uploading file.";
    }
}

// Handle file deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_logo_id'])) {
    $logo_id = $_POST['delete_logo_id'];
    // Fetch the image path to delete the file from the server
    $sql = "SELECT image_path FROM logos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $logo_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    // Delete the file from the server
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    // Delete the logo from the database
    $sql = "DELETE FROM logos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $logo_id);
    $stmt->execute();
    $stmt->close();
    echo "Logo deleted successfully.";
}

// Fetch logos
$sql = "SELECT id, title, image_path FROM logos";
$result = $conn->query($sql);

$logos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $logos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .form-container {
            margin-top: 20px;
        }
        .logo-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .logo-item img {
            max-width: 100px;
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <h1>Management Page</h1>
    <p>Welcome, you can add new manufacturer logos here.</p>
    <div class="form-container">
        <form action="management.php" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <br>
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>
            <br>
            <button type="submit">Add Logo</button>
        </form>
    </div>
    <hr>
    <h2>Current Logos</h2>
    <div class="logo-list">
        <?php foreach ($logos as $logo): ?>
            <div class="logo-item">
                <img src="<?php echo $logo['image_path']; ?>" alt="<?php echo $logo['title']; ?>">
                <form action="management.php" method="POST" style="display:inline;">
                    <input type="hidden" name="delete_logo_id" value="<?php echo $logo['id']; ?>">
                    <button type="submit">Delete</button>
                </form>
                <span><?php echo $logo['title']; ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="logout.php">Logout</a>
</body>
</html>
