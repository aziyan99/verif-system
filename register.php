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
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <?php
    $message = null;
    if (isset($_POST['email']) || isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM users WHERE email=:email";
        $row = $conn->prepare($sql);
        $row->execute(['email' => $email]);
        $res = $row->fetchAll();
        if (count($res) > 0) {
            $message = "Email already regsitered!";
        } else {
            $sql = "INSERT INTO users (email, password, role, is_active) VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $insertedUser = $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), 2, 0]);
            $userId = $conn->lastInsertId();
            if ($insertedUser) {
                $sql = "INSERT INTO user_details (user_id, first_name, sur_name, phone_number, address, passport, NIC, company, country, address_document, idcard_document, passport_document) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                $insertedUserDetails = $stmt->execute([$userId, "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-"]);

                $sql = "INSERT INTO documents_verifications (user_id, address_document, idcard_document, passport_document, status, note) VALUES (?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                $insertedDocumentStatus = $stmt->execute([$userId, "-", "-", "-", 0, "-"]);

                $init = rand();
                $token = md5($init);

                $sql = "INSERT INTO verification_tokens (user_id, token) VALUES (?,?)";
                $stmt = $conn->prepare($sql);
                $insertedToken = $stmt->execute([$userId, $token]);
                if ($insertedToken) {
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
                    $mail->addAddress($email, $email);     // Add a recipient
                    $mail->addCC('cc@verification.test');
                    $mail->addBCC('bcc@verification.test');

                    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
                    $mail->isHTML(true);                                  // Set email format to HTML

                    $mail->Subject = 'Email verification';
                    $mail->Body    = '<a href="http://verif-system.test/login.php?token=' . $token . '">verification link</a>';
                    $mail->AltBody = '"http://verif-system.test/login.php?token=' . $token . '"';

                    if (!$mail->send()) {
                        $message = "Error during regsitration please try again!";
                        // echo 'Mailer Error: ' . $mail->ErrorInfo;
                    } else {
                        $message = "Check your email for verification!";
                    }
                } else {
                    $message = "Failed to send email!";
                }
            } else {
                $message = "Error during regsitration please try again!";
            }
        }
    }

    ?>

    <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-4">
                <h3>Register</h3>
                <hr>
                <?php if ($message != null) : ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form action="/register.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
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
                        <button class="btn btn-primary" id="registerButton" disabled>Submit</button>
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