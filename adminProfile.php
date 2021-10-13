<!DOCTYPE html>
<html lang="en">
<?php
require_once('dbconfig.php');
session_start();
$message = null;
if (!$_SESSION['userId'] || !$_SESSION['role'] || !$_SESSION['email']) {
    header('location: login.php');
} else {
    if ($_SESSION['role'] != "1") {
        header('location: index.php');
    }
    $sql = "SELECT * FROM users WHERE id=?";
    $row = $conn->prepare($sql);
    $row->execute([$_SESSION['userId']]);
    $profile = $row->fetch();
}

if (isset($_POST['type'])) {
    if ($_POST['type'] == "email") {
        $sql = "UPDATE users SET email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute([$_POST['email'], $_SESSION['userId']]);
        $message = "Email updated!";
        $sql = "SELECT * FROM users WHERE id=?";
        $row = $conn->prepare($sql);
        $row->execute([$_SESSION['userId']]);
        $profile = $row->fetch();
    } else {
        $sql = "UPDATE users SET password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $res = $stmt->execute([$password, $_SESSION['userId']]);
        $message = "Password updated!";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification system</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="admin.php" class="list-group-item list-group-item-action">Home</a>
                    <a href="adminProfile.php" class="list-group-item list-group-item-action">Profile</a>
                    <a href="logout.php" onclick="return confirm('Logout now?')" class="list-group-item list-group-item-action">Logout</a>
                </div>
                <br>
                <small>Logged in as <?= $profile['email'] ?></small>
            </div>
            <div class="col-md-9">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
                <hr>
                <?php if ($message != null) : ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <h4>Update Email</h4>
                <form action="profile.php" method="POST">
                    <input type="hidden" name="type" required value="email">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" require value="<?= $profile['email'] ?>">
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </form>
                <hr>
                <h4>Update Password</h4>
                <form action="profile.php" method="POST">
                    <input type="hidden" name="type" required value="password">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="passwordConfirmation" class="form-label">Password confirmation</label>
                        <input type="password" name="passwordConfirmation" id="passwordConfirmation" class="form-control" required>
                        <small id="passwordMessage" class="text-danger"></small>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </form>
                <hr>
            </div>
        </div>
        <br>
        <footer class="mt-5 mb-5 d-flex justify-content-center">
            <p class="text-muted">&copy;2021</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
        var password = document.getElementById("password");
        var passwordConfirmation = document.getElementById("passwordConfirmation");
        var passwordMessage = document.getElementById("passwordMessage");
        passwordConfirmation.addEventListener('input', function(e) {
            if (e.target.value === password.value) {
                passwordMessage.textContent = "";
            } else {
                passwordMessage.textContent = "Password not match";
            }
        });
    </script>
</body>

</html>