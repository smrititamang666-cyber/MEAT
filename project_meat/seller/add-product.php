<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('seller');

$error = "";
$success = "";

/* ==============================
   GET SELLER SHOP ID
   ============================== */
$user_id = (int) $_SESSION['user_id'];

$shop = null;
$shop_stmt = $conn->prepare("SELECT * FROM shops WHERE user_id = ? LIMIT 1");
$shop_stmt->bind_param("i", $user_id);
$shop_stmt->execute();
$shop_result = $shop_stmt->get_result();

if ($shop_result && $shop_result->num_rows > 0) {
    $shop = $shop_result->fetch_assoc();
}

if (!$shop) {
    die("You must create a shop before adding products.");
}

$shop_id = (int) $shop['id'];

/* ==============================
   HANDLE PRODUCT SUBMISSION
   ============================== */
if (isset($_POST['add_product'])) {

    $category_id = $_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $size = trim($_POST['size']);
    $quality = trim($_POST['quality']);
    $price = $_POST['price'];

    /* ==============================
       IMAGE UPLOAD
       ============================== */
    $image = "";
    $upload_dir = "../assets/images/products/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $image_name;
        } else {
            $error = "Image upload failed.";
        }
    }

    /* ==============================
       INSERT PRODUCT
       ============================== */
    if (!$error) {
        $stmt = $conn->prepare("
            INSERT INTO products
            (shop_id, category_id, name, description, size, quality, price, image)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iissssds",
            $shop_id,
            $category_id,
            $name,
            $description,
            $size,
            $quality,
            $price,
            $image
        );

        if ($stmt->execute()) {
            $success = "✅ Product added successfully!";
        } else {
            $error = "Database error: " . $stmt->error;
        }
    }
}

/* ==============================
   GET CATEGORIES
   ============================== */
$cat_result = $conn->query("SELECT * FROM categories");
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>Add New Product</h2>
        <p>List a new product in your shop</p>
    </div>

    <?php if ($error): ?>
        <p style="color:red; margin-bottom:15px;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green; margin-bottom:15px;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <div class="styled-form">
        <form method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" placeholder="Product Name" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php while ($cat = $cat_result->fetch_assoc()): ?>
                        <option value="<?php echo (int)$cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Describe your product" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="size">Size</label>
                <input type="text" id="size" name="size" placeholder="e.g. 500g, 1kg">
            </div>

            <div class="form-group">
                <label for="quality">Quality</label>
                <input type="text" id="quality" name="quality" placeholder="e.g. Premium, Grade A">
            </div>

            <div class="form-group">
                <label for="price">Price (Rs)</label>
                <input type="number" id="price" step="0.01" name="price" placeholder="Enter price" min="0" required>
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>

            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
