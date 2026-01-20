<?php
$lockFile = __DIR__ . '/installed.lock';

if (file_exists($lockFile)) {
    die('Application already installed.');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Application Installer</title>
</head>
<body>
<h2>Install Application</h2>

<form method="POST" action="process.php">
    <label>Database Host</label><br>
    <input type="text" name="db_host" value="localhost" required><br><br>

    <label>Database Name</label><br>
    <input type="text" name="db_name" required><br><br>

    <label>Database Username</label><br>
    <input type="text" name="db_user" required><br><br>

    <label>Database Password</label><br>
    <input type="password" name="db_pass"><br><br>

    <button type="submit">Install</button>
</form>
</body>
</html>
