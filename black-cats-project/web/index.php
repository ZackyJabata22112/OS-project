<?php
$host = 'db';
$user = 'root';
$pass = 'root';
$dbname = 'cats_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

$upload_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fact'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    
    if (isset($_FILES['cat_image']) && $_FILES['cat_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cat_image']['tmp_name'];
        $fileName = $_FILES['cat_image']['name'];
        $fileSize = $_FILES['cat_image']['size'];
        $fileType = $_FILES['cat_image']['type'];
        
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = uniqid('cat_', true) . '.' . $fileExtension;
            
            $uploadFileDir = 'images/uploads/';
            
            // АВТОМАТИЧНО СЪЗДАВАНЕ НА ПАПКАТА:
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0775, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                if (!empty($title) && !empty($description)) {
                    $sql = "INSERT INTO cat_myths (title, description, image_path) VALUES ('$title', '$description', '$dest_path')";
                    $conn->query($sql);
                    header("Location: index.php#myths");
                    exit;
                }
            } else {
                $upload_error = "There was an error moving the uploaded file to the server directory.";
            }
        } else {
            $upload_error = "Upload failed. Allowed file types: JPG, JPEG, PNG, GIF.";
        }
    } else {
        $upload_error = "Please select and upload a valid image file.";
    }
}

$result = $conn->query("SELECT * FROM cat_myths");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Black Cats - Myths & Facts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-black-custom sticky-top">
        <div class="container">
            <a class="navbar-brand text-gold" href="index.php">BlackCat.com</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#myths">Myths & Facts</a></li>
                    <li class="nav-item"><a class="nav-link" href="#add-new">Add New Fact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header id="home" class="bg-black-custom text-white py-5 text-center text-lg-start">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">Why are black cats <span class="text-gold">special</span>?</h1>
                    <p class="lead mb-3">They don't bring bad luck – they bring elegance, mystery, and endless love.</p>
                    <a href="#myths" class="btn btn-outline-warning btn-lg mt-3">Learn More</a>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <img src="images/hero-cat.jpg" alt="Black cat hero" class="img-fluid rounded-3 shadow-lg">
                </div>
            </div>
        </div>
    </header>

    <main class="container py-5" id="myths">
        <h2 class="text-center mb-5 fw-bold">Interesting Facts and Myths</h2>
        
        <div class="row g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top" alt="Cat Image">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center"><p class="text-muted">No facts found.</p></div>
            <?php endif; ?>
        </div>

        <hr class="my-5">

        <section id="add-new" class="row justify-content-center">
            <div class="col-lg-6">
                <div class="form-container">
                    <h3 class="fw-bold mb-4 text-center"><i class="bi bi-cloud-arrow-up-fill me-2 text-warning"></i>Upload New Cat Fact</h3>
                    
                    <?php if(!empty($upload_error)): ?>
                        <div class="alert alert-danger" role="alert"><?php echo $upload_error; ?></div>
                    <?php endif; ?>

                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="cat_image" class="form-label fw-semibold">Upload Image File</label>
                            <input class="form-control" type="file" id="cat_image" name="cat_image" accept="image/*" required>
                            <div class="form-text">Supported formats: JPG, JPEG, PNG, GIF.</div>
                        </div>
                        <button type="submit" name="add_fact" class="btn btn-warning w-100 fw-bold py-2 mt-2">Publish and Upload</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-black-custom text-white py-4 text-center">
        <div class="container"><p class="mb-0">&copy; 2026 All rights reserved.</p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>