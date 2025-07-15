<!DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href='css/style.css'>
    <title>Login to CCSystem</title>
</head>
<body>
    <div class="login-container">
        <div class='login-box'>
            <h2><img src="images/logo.png" alt="Logo" style="height: 200px; width: 200px;"></h2>
            <h2>Login</h2>
            <form method="POST" action="login.php">
                <label>Username</label><br>
                <input type="text" name="username" required><br>

                <label>Password</label><br>
                <input type="password" name="password" required><br>

                <button type="submit">Login</button>
            </form>
        </div>
    </div>

</body>
</html>
