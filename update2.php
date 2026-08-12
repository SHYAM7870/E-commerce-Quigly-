<?php
include_once('admin/includes/db.php');
// global $conn;

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("location: index.php");
    exit;
}

$id = $_GET['id'];

$sql = "SELECT * FROM quigly_table WHERE id='$id'";
$run = mysqli_query($conn,$sql);
$user = mysqli_fetch_assoc($run);

if(!$user){
    echo "User not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            font-family: 'Poppins', sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-custom {
            border-radius: 10px;
            background: #667eea;
            color: #fff;
        }
        .btn-custom:hover {
            background: #5a67d8;
        }
    </style>
</head>

<body>

<div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="col-md-6">

        <div class="card shadow-lg p-4">
            <h3 class="text-center mb-4 fw-bold">Update User</h3>

            <form method="POST" action="update_action2.php">

                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control"
                           value="<?php echo $user['name']?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo $user['email']?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="number" class="form-control"
                           value="<?php echo $user['number']?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Leave blank to keep old password">
                </div>

                <div class="d-flex justify-content-between">

                    <a href="index.php" class="btn btn-secondary px-4">Cancel</a>

                    <button type="submit" class="btn btn-custom px-4">
                        Update
                    </button>

                </div>

            </form>
        </div>

    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>