<!DOCTYPE html>
<html lang="en">
<?php
require_once('dbconfig.php');
session_start();
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <?php
    $message = null;
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        $sql = "SELECT * FROM verification_tokens WHERE token=:token";
        $row = $conn->prepare($sql);
        $row->execute(['token' => $token]);
        $res = $row->fetchAll();
        if (count($res) > 0) {
            $tokenId = $res[0]['id'];
            $userId = $res[0]['user_id'];
            //update user status
            $sql = "UPDATE users SET is_active=? WHERE id=?";
            $row = $conn->prepare($sql);
            $row->execute([1, $userId]);
            //delete token
            $sql = "DELETE FROM verification_tokens WHERE id=?";
            $row = $conn->prepare($sql);
            $row->execute([$tokenId]);


            $message =  "Account activated!";
        } else {
            $message =  "Invalid token!";
        }
    }

    if (isset($_POST['email']) || isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM users WHERE email=?";
        $row = $conn->prepare($sql);
        $row->execute([$email]);
        $res = $row->fetch();
        if ($res) {
            $passwordVerify = password_verify($password, $res['password']);
            if ($passwordVerify) {
                if ($res['is_active'] != 0) {
                    $_SESSION['role'] = $res['role'];
                    $_SESSION['userId'] = $res['id'];
                    $_SESSION['email'] = $res['email'];
                    header('location: index.php');
                } else {
                    $message =  "Account need activation!";
                }
            } else {
                $message =  "Invalid credentials!";
            }
        } else {
            $message =  "Invalid credentials!";
        }
    }


    ?>

    <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-4">
                <h3>Login</h3>
                <hr>
                <?php if ($message != null) : ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form action="/login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="mb-3 d-grid">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <p class="mt-2">Don't have account? Register <a href="/register.php">here</a></p>
                        <p class="mt-2">Forgot password? Click <a href="forgotPassword.php">here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
