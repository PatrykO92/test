<?php
include 'config.php';
include 'session.php';

// Fetch logos
$sql = "SELECT title, image_path FROM logos";
$result = $conn->query($sql); $logos = []; if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) { $logos[] = $row; } } ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CUBE - Landing Page</title>
    <style>
      body {
        font-family: Arial, sans-serif;
      }
      .login-form {
        margin-top: 20px;
      }
      .logo-wall {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
      }
      .logo-item {
        flex: 0 1 calc(25% - 20px);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 10px;
        text-align: center;
      }
    </style>
  </head>
  <body>
    <h1>Welcome to CUBE</h1>
    <p>
      Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse
      scelerisque.
    </p>
    <div class="logo-wall">
      <?php foreach ($logos as $logo): ?>
      <div class="logo-item">
        <img
          src="<?php echo $logo['image_path']; ?>"
          alt="<?php echo $logo['title']; ?>"
          style="max-width: 100%; height: auto"
        />
        <p><?php echo $logo['title']; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="login-form">
      <h2>Login</h2>
      <form action="login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required />
        <br />
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />
        <br />
        <button type="submit">Login</button>
      </form>
    </div>
  </body>
</html>
