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
    <title>Reset password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <?php
    $message = null;
    $token = null;
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        $sql = "SELECT * FROM verification_tokens WHERE token=:token";
        $row = $conn->prepare($sql);
        $row->execute(['token' => $token]);
        $token = $row->fetch();
        if (!$token) {
            header('location: login.php');
        }

        if (isset($_POST['password'])) {
            $password = $_POST['password'];
            $sql = "UPDATE users SET password=? WHERE id=?";
            $row = $conn->prepare($sql);
            $row->execute([password_hash($password, PASSWORD_DEFAULT), $token['user_id']]);

            //delete token
            $sql = "DELETE FROM verification_tokens WHERE id=?";
            $row = $conn->prepare($sql);
            $row->execute([$token['id']]);

            header('location: login.php');
        }
    } else {
        header('location: login.php');
    }

    ?>

    <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-4">
                <h3>Reset Password</h3>
                <hr>
                <?php if ($message != null) : ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form action="resetPassword.php?token=<?= $token['token'] ?>" method="POST">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="passwordConfirmation" class="form-label">Password confirmation</label>
                        <input type="password" name="passwordConfirmation" id="passwordConfirmation" class="form-control" required>
                        <small id="passwordMessage" class="text-danger"></small>
                    </div>
                    <div class="mb-3 d-grid">
                        <button class="btn btn-primary" type="submit" id="registerButton" disabled>Submit</button>
                        <p class="mt-2">Already have account? Login <a href="/login.php">here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
        var password = document.getElementById("password");
        var passwordConfirmation = document.getElementById("passwordConfirmation");
        var registerButton = document.getElementById("registerButton");
        var passwordMessage = document.getElementById("passwordMessage");
        var loadingContainer = document.getElementById("loading");
        passwordConfirmation.addEventListener('input', function(e) {
            if (e.target.value === password.value) {
                registerButton.removeAttribute("disabled");
                passwordMessage.textContent = "";
            } else {
                passwordMessage.textContent = "Password not match";
            }
        });
        registerButton.addEventListener("click", function(e) {
            registerButton.textContent = "Loading...";
        });
    </script>
</body>

</html>