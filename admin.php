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


    $sql = "SELECT * FROM users WHERE role=?";
    $row = $conn->prepare($sql);
    $row->execute([2]);
    $verifications = $row->fetchAll();
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification system</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.11.3/datatables.min.css" />

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.11.3/datatables.min.js"></script>

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
                        <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
                    </ol>
                </nav>
                <hr>
                <h4>Verification data</h4>
                <?php if ($message != null) : ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="verifications" class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                                <th>Is activated?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($verifications as $data) : ?>
                                <tr>
                                    <td>
                                        <a href="details.php?user_id=<?= $data['id'] ?>" class="btn btn-info btn-sm">Details</a>
                                    </td>
                                    <td><?= $data['email'] ?></td>
                                    <td>
                                        <?php if ($data['is_active'] == "0") : ?>
                                            <span class="badge bg-secondary">no</span>
                                        <?php else : ?>
                                            <span class="badge bg-secondary">yes</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <br>
        <footer class="mt-5 mb-5 d-flex justify-content-center">
            <p class="text-muted">&copy;2021</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#verifications').DataTable();
        });
    </script>
</body>

</html>