<?php include "C:/xampp1\htdocs\TFT\Backend\LoginUser.php"; ?>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
    <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
        <label> Email
            <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
        </label>
        <span class="help-block"><?php echo $email_err; ?></span>
    </div>
    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label> Password
            <input type="password" name="password" class="form-control">
        </label>
        <span class="help-block"><?php echo $password_err; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Login">
    </div>
    <p>Don't have an account? <a href="TestRegister.php">Sign up now</a>.</p>
    </form>
</div>
</body>

</html>

