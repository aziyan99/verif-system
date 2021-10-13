<!DOCTYPE html>
<html lang="en">
<?php
require_once('dbconfig.php');
session_start();
if (!$_SESSION['userId'] || !$_SESSION['role'] || !$_SESSION['email']) {
    header('location: login.php');
} else {
    $sql = "SELECT * FROM users WHERE id=?";
    $row = $conn->prepare($sql);
    $row->execute([$_SESSION['userId']]);
    $profile = $row->fetch();
    $sql = "SELECT * FROM documents_verifications WHERE user_id=?";
    $row = $conn->prepare($sql);
    $row->execute([$_SESSION['userId']]);
    $verificationDocuments = $row->fetch();
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
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Verification</li>
                    </ol>
                </nav>
                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <h4>Documents Verifications</h4>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert" id="successMessage" style="display: none;">
                            Upload success!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert" id="failMessage" style="display: none;">
                            Upload fail!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <div class="mb-3">
                            <label for="documentType" class="form-label">Document type</label>
                            <select name="documentType" id="documentType" class="form-select">
                                <option value="0">---</option>
                                <option value="1">Address document</option>
                                <option value="2">IDCard document</option>
                                <option value="3">Passport document</option>
                            </select>
                        </div>
                        <div class="row" id="videoContainer" style="display: none;">
                            <div class="col-md-12">
                                <video id="video" width="640" height="480" autoplay></video>
                                <canvas id="canvas" width="640" height="480" style="display: none;"></canvas>
                                <div class="mt-2 mb-2">
                                    <button id="startCamera">Start Camera</button>
                                    <button id="takePhoto">Take Photo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover">
                            <tr>
                                <th>Address document</th>
                                <th>IDCard document</th>
                                <th>Passport document</th>
                                <th>Status</th>
                                <th>Note</th>
                            </tr>
                            <tr>
                                <td>
                                    <a target="_blank" href="uploads/<?= $verificationDocuments['address_document'] ?>">
                                        <img src="uploads/<?= $verificationDocuments['address_document'] ?>" class="img-fluid" width="150" alt="address_document">
                                    </a>
                                </td>
                                <td>
                                    <a target="_blank" href="uploads/<?= $verificationDocuments['idcard_document'] ?>">
                                        <img src="uploads/<?= $verificationDocuments['idcard_document'] ?>" class="img-fluid" width="150" alt="idcard_document">
                                    </a>
                                </td>
                                <td>
                                    <a target="_blank" href="uploads/<?= $verificationDocuments['passport_document'] ?>">
                                        <img src="uploads/<?= $verificationDocuments['passport_document'] ?>" class="img-fluid" width="150" alt="passport_document">
                                    </a>
                                </td>
                                <td>
                                    <?php if ($verificationDocuments['status'] == "0") : ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php else : ?>
                                        <span class="badge bg-success">Accepted</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $verificationDocuments['note'] ?></td>
                            </tr>
                        </table>
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
    <script>
        let documentType = document.querySelector("#documentType");
        let videoContainer = document.querySelector("#videoContainer");
        let startCamera = document.querySelector("#startCamera");
        let video = document.querySelector("#video");
        let takePhoto = document.querySelector("#takePhoto");
        let canvas = document.querySelector("#canvas");
        let successMessage = document.querySelector("#successMessage");
        let failMessage = document.querySelector("#failMessage");

        documentType.addEventListener("change", function() {
            console.log(documentType.value);
            if (documentType.value === "0") {
                videoContainer.style.display = "none";
            }
            if (documentType.value === "1" || documentType.value === "2" || documentType.value === "3") {
                videoContainer.style.display = "block";
            }
        });

        startCamera.addEventListener('click', async function() {
            video.style.display = "block";
            canvas.style.display = "none"
            let stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false
            });
            video.srcObject = stream;
        });

        takePhoto.addEventListener('click', async function() {
            video.style.display = "none";
            canvas.style.display = "block"
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            let image_data_url = canvas.toDataURL('image/jpeg');

            let stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false
            });
            video.srcObject = stream;
            stream.getTracks().forEach(function(track) {
                if (track.readyState == 'live') {
                    track.stop();
                }
            });


            fetch('processValidation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        documentType: documentType.value,
                        data: image_data_url
                    })
                })
                .then(res => res.json())
                .then(data => {
                    videoContainer.style.display = "none";
                    successMessage.style.display = "block";
                    stream.getTracks().forEach(function(track) {
                        if (track.readyState == 'live') {
                            track.stop();
                        }
                    });
                    video.style.display = "block";
                    canvas.style.display = "none";
                    document.location.href = "verification.php";
                })
                .catch(error => {
                    failMessage.style.display = "block";
                    videoContainer.style.display = "none";
                    stream.getTracks().forEach(function(track) {
                        if (track.readyState == 'live') {
                            track.stop();
                        }
                    });
                    console.log(error);
                    video.style.display = "block";
                    canvas.style.display = "none";
                    document.location.href = "verification.php";
                });
        });
    </script>
</body>

</html>