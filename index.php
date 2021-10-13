<!DOCTYPE html>
<html lang="en">
<?php
require_once('dbconfig.php');
session_start();
$message = null;
if (!$_SESSION['userId'] || !$_SESSION['role'] || !$_SESSION['email']) {
    header('location: login.php');
} else {
    if ($_SESSION['role'] == 1) {
        header('location: admin.php');
    } else {
        $sql = "SELECT * FROM users WHERE id=?";
        $row = $conn->prepare($sql);
        $row->execute([$_SESSION['userId']]);
        $profile = $row->fetch();

        $sql = "SELECT * FROM user_details WHERE user_id=?";
        $row = $conn->prepare($sql);
        $row->execute([$_SESSION['userId']]);
        $userDetails = $row->fetch();

        $sql = "SELECT * FROM documents_verifications WHERE user_id=?";
        $row = $conn->prepare($sql);
        $row->execute([$_SESSION['userId']]);
        $documentsVerifications = $row->fetch();
    }
}
if (isset($_POST['formType'])) {
    if ($_POST['formType'] == "details") {
        $data = [
            'first_name' => $_POST['firstName'],
            'sur_name' => $_POST['surName'],
            'phone_number' => $_POST['phoneNumber'],
            'address' => $_POST['address'],
            'passport' => $_POST['passport'],
            'NIC' => $_POST['nic'],
            'company' => $_POST['company'],
            'country' => $_POST['country'],
            'user_id' => $_SESSION['userId'],
        ];
        $sql = "UPDATE user_details SET first_name=:first_name, sur_name=:sur_name, phone_number=:phone_number, address=:address, passport=:passport, NIC=:NIC, company=:company, country=:country WHERE user_id=:user_id";
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($data);
        if ($res) {
            $sql = "SELECT * FROM user_details WHERE user_id=?";
            $row = $conn->prepare($sql);
            $row->execute([$_SESSION['userId']]);
            $userDetails = $row->fetch();
            $message = "Data updated!";
        } else {
            $message = "Failed updated data!";
        }
    }
    if ($_POST['formType'] == "documents") {
        if ($_POST['documentType'] == "address") {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["addressDocument"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            /* create new name file */
            $filename = uniqid();
            $basename = $filename . "." . $imageFileType;
            $destination = $target_dir . $basename;
            // Check if image file is a actual image or fake image
            if (isset($_POST["submit"])) {
                $check = getimagesize($_FILES["addressDocument"]["tmp_name"]);
                if ($check === false) {
                    $message = "File is not an image.";
                    $uploadOk = 0;
                }
            }

            // Allow certain file formats
            if (
                $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif"
            ) {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $message = "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["addressDocument"]["tmp_name"], $destination)) {
                    $data = [
                        'address_document' => $basename,
                        'user_id' => $_SESSION['userId']
                    ];
                    $sql = "UPDATE user_details SET address_document=:address_document WHERE user_id=:user_id";
                    $stmt = $conn->prepare($sql);
                    $res = $stmt->execute($data);
                    $message = "The file has been uploaded.";
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        }
        if ($_POST['documentType'] == "idcard") {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["idcardDocument"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            /* create new name file */
            $filename = uniqid();
            $basename = $filename . "." . $imageFileType;
            $destination = $target_dir . $basename;
            // Check if image file is a actual image or fake image
            if (isset($_POST["submit"])) {
                $check = getimagesize($_FILES["idcardDocument"]["tmp_name"]);
                if ($check === false) {
                    $message = "File is not an image.";
                    $uploadOk = 0;
                }
            }

            // Allow certain file formats
            if (
                $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif"
            ) {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $message = "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["idcardDocument"]["tmp_name"], $destination)) {
                    $data = [
                        'idcard_document' => $basename,
                        'user_id' => $_SESSION['userId']
                    ];
                    $sql = "UPDATE user_details SET idcard_document=:idcard_document WHERE user_id=:user_id";
                    $stmt = $conn->prepare($sql);
                    $res = $stmt->execute($data);
                    $message = "The file has been uploaded.";
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        }
        if ($_POST['documentType'] == "passport") {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["passportDocument"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            /* create new name file */
            $filename = uniqid();
            $basename = $filename . "." . $imageFileType;
            $destination = $target_dir . $basename;
            // Check if image file is a actual image or fake image
            if (isset($_POST["submit"])) {
                $check = getimagesize($_FILES["passportDocument"]["tmp_name"]);
                if ($check === false) {
                    $message = "File is not an image.";
                    $uploadOk = 0;
                }
            }

            // Allow certain file formats
            if (
                $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif"
            ) {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $message = "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["passportDocument"]["tmp_name"], $destination)) {
                    $data = [
                        'passport_document' => $basename,
                        'user_id' => $_SESSION['userId']
                    ];
                    $sql = "UPDATE user_details SET passport_document=:passport_document WHERE user_id=:user_id";
                    $stmt = $conn->prepare($sql);
                    $res = $stmt->execute($data);
                    $message = "The file has been uploaded.";
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        }
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
                    <a href="index.php" class="list-group-item list-group-item-action">Home</a>
                    <a href="verification.php" class="list-group-item list-group-item-action">Verification</a>
                    <a href="profile.php" class="list-group-item list-group-item-action">Profile</a>
                    <a href="logout.php" onclick="return confirm('Logout now?')" class="list-group-item list-group-item-action">Logout</a>
                </div>
                <br>
                <small>Logged in as <?= $profile['email'] ?></small>
            </div>
            <div class="col-md-9">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Home</li>
                    </ol>
                </nav>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($message != null) : ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <?= $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <h4>Informations</h4>
                        <form action="index.php" method="POST">
                            <input type="hidden" name="formType" value="details" required>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="firstName" class="form-control" value="<?= $userDetails['first_name'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Surr Name</label>
                                        <input type="text" name="surName" class="form-control" value="<?= $userDetails['sur_name'] ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone number</label>
                                <input type="text" name="phoneNumber" class="form-control" value="<?= $userDetails['phone_number'] ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Passport</label>
                                        <input type="text" name="passport" class="form-control" value="<?= $userDetails['passport'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">NIC</label>
                                        <input type="text" name="nic" class="form-control" value="<?= $userDetails['NIC'] ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Company</label>
                                <input type="text" name="company" class="form-control" value="<?= $userDetails['company'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control" value=" <?= $userDetails['country'] ?>" required>
                            </div>
                            <div class=" mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" cols="30" rows="3" required><?= $userDetails['address'] ?></textarea>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <h4>Address documents proof</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <form action="index.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="formType" value="documents" required>
                                    <input type="hidden" name="documentType" value="address" required>
                                    <div class="mb-3">
                                        <label class="form-label">Address document</label>
                                        <input type="file" class="form-control" name="addressDocument" required>
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 d-flex justify-content-center">
                                <?php if ($userDetails['address_document'] != "-") : ?>
                                    <a href="uploads/<?= $userDetails['address_document'] ?>" target="_blank">
                                        <img src="uploads/<?= $userDetails['address_document'] ?>" class="img-fluid" width="200" alt="addressDocument">
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <h4>IDCard documents proof</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <form action="index.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="formType" value="documents" required>
                                    <input type="hidden" name="documentType" value="idcard" required>
                                    <div class="mb-3">
                                        <label class="form-label">IDCard document</label>
                                        <input type="file" class="form-control" name="idcardDocument" required>
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 d-flex justify-content-center">
                                <?php if ($userDetails['idcard_document'] != "-") : ?>
                                    <a href="uploads/<?= $userDetails['idcard_document'] ?>" target="_blank">
                                        <img src="uploads/<?= $userDetails['idcard_document'] ?>" class="img-fluid" width="200" alt="idcard_document">
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <h4>Passport documents proof</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <form action="index.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="formType" value="documents" required>
                                    <input type="hidden" name="documentType" value="passport" required>
                                    <div class="mb-3">
                                        <label class="form-label">Passport document</label>
                                        <input type="file" class="form-control" name="passportDocument" required>
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 d-flex justify-content-center">
                                <?php if ($userDetails['passport_document'] != "-") : ?>
                                    <a href="uploads/<?= $userDetails['passport_document'] ?>" target="_blank">
                                        <img src="uploads/<?= $userDetails['passport_document'] ?>" class="img-fluid" width="200" alt="passport_document">
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
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