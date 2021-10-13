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
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <?php
    $message = null;

    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $sql = "SELECT * FROM users WHERE email=?";
        $row = $conn->prepare($sql);
        $row->execute([$email]);
        $res = $row->fetch();
        if ($res) {
            $init = rand();
            $token = md5($init);

            $sql = "INSERT INTO verification_tokens (user_id, token) VALUES (?,?)";
            $stmt = $conn->prepare($sql);
            $insertedToken = $stmt->execute([$res['id'], $token]);
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

                $mail->Subject = 'Password Reset';
                $mail->Body    = '<a href="https://calm-dusk-87412.herokuapp.com/resetPassword.php?token=' . $token . '">Reset password link</a>';
                $mail->AltBody = '"https://calm-dusk-87412.herokuapp.com/resetPassword.php?token=' . $token . '"';

                if (!$mail->send()) {
                    $message = "Check your email!";
                    // echo 'Mailer Error: ' . $mail->ErrorInfo;
                } else {
                    $message = "Check your email!";
                }
            } else {
                $message = "Check your email!";
            }
        } else {
            $message = "Check your email!";
        }
    }
    ?>

    <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-4">
                <h3>Forgot password</h3>
                <hr>
                <?php if ($message != null) : ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form action="forgotPassword.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="mb-3 d-grid">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <p class="mt-2">Don't have account? Register <a href="/register.php">here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>