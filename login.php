<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Login | Echoes Today</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        body {
            background: url("loginBg5.png") no-repeat center center fixed;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;

            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        form {
            width: 100%;
            max-width: 400px;
        }

        .form-control {
            background-color: rgb(3, 18, 32);
            color: #ffff;
            border: none;
            border-radius: 0;
            margin-bottom: 18px;
            padding: 20px 18px;
            font-size: 1.1rem;
        }

        .form-control::placeholder {
            color: #ccc;
        }

        .form-control:focus {
            background-color: #0a2540;
            color:rgb(131, 131, 131);
            box-shadow: none;
        }

        .btn-login {
            background-color: rgb(3, 18, 32);
            color: #fff;
            border: none;
            width: 100%;
            padding: 20px;
            border-radius: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-login:hover {
            background-color: #081c33;
        }

        .alert {
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <form action="loginHandler.php" method="POST">
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <input type="text" name="identifier" class="form-control" placeholder="Username" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" class="btn btn-login">Log in</button>
    </form>

</body>

</html>
