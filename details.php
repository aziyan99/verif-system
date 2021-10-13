<!DOCTYPE html>
<html lang="en">
<?php
require_once('dbconfig.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './vendors/phpmailer/src/PHPMailer.php';
require './vendors/phpmailer/src/Exception.php';
require './vendors/phpmailer/src/SMTP.php';
session_start();
$message = null;
$verificationId = "";
if (!$_SESSION['userId'] || !$_SESSION['role'] || !$_SESSION['email']) {
    header('location: login.php');
} else {
    if ($_SESSION['role'] != "1") {
        header('location: index.php');
    }

    if (isset($_GET['user_id'])) {
        if ($_GET['user_id'] == "") {
            header('location: admin.php');
        }
        $verificationId = $_GET['user_id'];
    } else {
        header('location: admin.php');
    }

    $sql = "SELECT * FROM users WHERE id=?";
    $row = $conn->prepare($sql);
    $row->execute([$_SESSION['userId']]);
    $profile = $row->fetch();

    $sql = "SELECT * FROM users WHERE id=?";
    $row = $conn->prepare($sql);
    $row->execute([$verificationId]);
    $verification = $row->fetch();

    $sql = "SELECT * FROM user_details WHERE user_id=?";
    $row = $conn->prepare($sql);
    $row->execute([$verificationId]);
    $verificationDetails = $row->fetch();

    $sql = "SELECT * FROM documents_verifications WHERE user_id=?";
    $row = $conn->prepare($sql);
    $row->execute([$verificationId]);
    $verificationDocuments = $row->fetch();
}

if (isset($_POST['note']) || isset($_POST['status'])) {
    $sql = "UPDATE documents_verifications SET status=?, note=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $res = $stmt->execute([$_POST['status'], $_POST['note'], $verificationId]);

    $sql = "SELECT * FROM users WHERE id=?";
    $row = $conn->prepare($sql);
    $row->execute([$verificationId]);
    $verification = $row->fetch();

    $sql = "SELECT * FROM user_details WHERE user_id=?";
    $row = $conn->prepare($sql);
    $row->execute([$verificationId]);
    $verificationDetails = $row->fetch();

    $sql = "SELECT * FROM documents_verifications WHERE user_id=?";
    $row = $conn->prepare($sql);
    $row->execute([$verificationId]);
    $verificationDocuments = $row->fetch();

    $mail = new PHPMailer;

    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.mailtrap.io';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = '3068eb93fa0fb6';                 // SMTP username
    $mail->Password = 'b9fc6035b69f71';                           // SMTP password
    $mail->Port = 465;

    $mail->From = 'system@verification.test';
    $mail->FromName = 'Mailer';
    $mail->addAddress($verification['email'], $verification['email']);     // Add a recipient
    $mail->addCC('cc@verification.test');
    $mail->addBCC('bcc@verification.test');

    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'Status Updated!';
    $mail->Body    = 'Your status and note has been updated. Please check it!';
    $mail->AltBody = 'Your status and note has been updated. Please check it!';

    if (!$mail->send()) {
        $message = "Error during regsitration please try again!";
        // echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        $message = "Check your email for verification!";
    }


    $message = "Data updated!";
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
                        <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
                <hr>
                <?php if ($message != null) : ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <h4>Details</h4>
                <table class="table table-sm table-bordered table-hover">
                    <tr>
                        <th>First name</th>
                        <td><?= $verificationDetails['first_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Sur name</th>
                        <td><?= $verificationDetails['sur_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $verification['email'] ?></td>
                    </tr>
                    <tr>
                        <th>Phone number</th>
                        <td><?= $verificationDetails['phone_number'] ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?= $verificationDetails['address'] ?></td>
                    </tr>
                    <tr>
                        <th>Passport</th>
                        <td><?= $verificationDetails['passport'] ?></td>
                    </tr>
                    <tr>
                        <th>NIC</th>
                        <td><?= $verificationDetails['NIC'] ?></td>
                    </tr>
                    <tr>
                        <th>Company</th>
                        <td><?= $verificationDetails['company'] ?></td>
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td><?= $verificationDetails['country'] ?></td>
                    </tr>
                    <tr>
                        <th>Address document</th>
                        <td>
                            <a target="_blank" href="uploads/<?= $verificationDetails['address_document'] ?>">
                                <img src="uploads/<?= $verificationDetails['address_document'] ?>" class="img-fluid" width="150" alt="address_document">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>IDCard document</th>
                        <td>
                            <a target="_blank" href="uploads/<?= $verificationDetails['idcard_document'] ?>">
                                <img src="uploads/<?= $verificationDetails['idcard_document'] ?>" class="img-fluid" width="150" alt="idcard_document">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Passport document</th>
                        <td>
                            <a target="_blank" href="uploads/<?= $verificationDetails['passport_document'] ?>">
                                <img src="uploads/<?= $verificationDetails['passport_document'] ?>" class="img-fluid" width="150" alt="passport_document">
                            </a>
                        </td>
                    </tr>
                </table>
                <hr>
                <h4>Verifications</h4>
                <table class="table table-sm table-bordered table-hover">
                    <tr>
                        <th>Address document</th>
                        <td>
                            <a target="_blank" href="uploads/<?= $verificationDocuments['address_document'] ?>">
                                <img src="uploads/<?= $verificationDocuments['address_document'] ?>" class="img-fluid" width="150" alt="address_document">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>IDCard document</th>
                        <td>
                            <a target="_blank" href="uploads/<?= $verificationDocuments['idcard_document'] ?>">
                                <img src="uploads/<?= $verificationDocuments['idcard_document'] ?>" class="img-fluid" width="150" alt="idcard_document">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Passport document</th>
                        <td>
                            <a target="_blank" href="uploads/<?= $verificationDocuments['passport_document'] ?>">
                                <img src="uploads/<?= $verificationDocuments['passport_document'] ?>" class="img-fluid" width="150" alt="passport_document">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php if ($verificationDocuments['status'] == "0") : ?>
                                <span class="badge bg-secondary">Rejected</span>
                            <?php else : ?>
                                <span class="badge bg-secondary">Accepted</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Note</th>
                        <td><?= $verificationDocuments['note'] ?></td>
                    </tr>
                </table>
                <hr>
                <h4>Update status and Note</h4>
                <form action="details.php?user_id=<?= $verificationId ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="">---</option>
                            <option value="0" <?= $verificationDocuments['status'] == "0" ? 'selected' : '' ?>>Rejected</option>
                            <option value="1" <?= $verificationDocuments['status'] == "1" ? 'selected' : '' ?>>Accepted</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" id="note" cols="30" rows="10" class="form-control" required><?= $verificationDocuments['note'] ?></textarea>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <br>
        <footer class="mt-5 mb-5 d-flex justify-content-center">
            <p class="text-muted">&copy;2021</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>