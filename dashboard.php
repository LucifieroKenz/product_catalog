<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$productData = "products.json";
$products = file_exists($productData) ? json_decode(file_get_contents($productData), true) : [];

//Initialize
$editing = null;
$errorMessages = [];
$success = "";

//Handle Add or Edit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? null;
    $name = trim($_POST["name"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if (empty($name)) $errorMessages[] = "Product name is required.";
    if (empty($price) || !is_numeric($price) || (float)$price <= 0) $errorMessages[] = "Valid price is required.";
    if (empty($description)) $errorMessages[] = "Description is required.";

    if (empty($errorMessages)) {
        if ($id) {
            foreach ($products as &$product) {
                if ($product["id"] === $id) {
                    $product["name"] = $name;
                    $product["price"] = (float)$price;
                    $product["description"] = $description;
                    break;
                }
            }
            $success = "Product updated!";
        } else {
            $products[] = [
                "id" => uniqid(),
                "name" => $name,
                "price" => (float)$price,
                "description" => $description
            ];
            $success = "Product added!";
        }
        file_put_contents($productData, json_encode($products, JSON_PRETTY_PRINT));
        header("Location: dashboard.php");
        exit();
    } else {
        $editing = ["id" => $id, "name" => $name, "price" => $price, "description" => $description];
    }
}

//Delete
if (isset($_GET["delete_id"])) {
    $id = $_GET["delete_id"];
    $products = array_filter($products, fn($item) => $item["id"] !== $id);
    file_put_contents($productData, json_encode(array_values($products), JSON_PRETTY_PRINT));
    header("Location: dashboard.php");
    exit();
}

//Edit
if (isset($_GET["edit_id"])) {
    foreach ($products as $product) {
        if ($product["id"] === $_GET["edit_id"]) {
            $editing = $product;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <!--Head row-->
        <div class="header-row">
            <h1>Welcome, <?= htmlspecialchars($_SESSION["username"]) ?>!</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <!--Success or Error Message-->
        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessages)): ?>
            <div class="alert error">
                <?php foreach ($errorMessages as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!--Product Input-->
        <form method="POST" action="dashboard.php" class="product-form-row">
            <input type="hidden" name="id" value="<?= htmlspecialchars($editing["id"] ?? "") ?>">

            <div class="form-field">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" 
                       value="<?= htmlspecialchars($editing["name"] ?? "") ?>" required>
            </div>

            <div class="form-field">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" 
                       value="<?= htmlspecialchars($editing["description"] ?? "") ?>" required>
            </div>

            <div class="form-field">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" min="0.01"
                       value="<?= htmlspecialchars($editing["price"] ?? "") ?>" required>
            </div>

            <button type="submit" class="btn add-btn">
                <?= $editing ? "Update" : "Add" ?>
            </button>
        </form>

        <!--Product List/Table-->
        <?php if (!empty($products)): ?>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars(substr($item["id"], 0, 8)) ?></td>
                        <td><?= htmlspecialchars($item["name"]) ?></td>
                        <td><?= htmlspecialchars($item["description"]) ?></td>
                        <td>$<?= number_format($item["price"], 2) ?></td>
                        <td class="actions">
                            <a href="?edit_id=<?= urlencode($item["id"]) ?>" class="edit-btn">Edit</a>
                            <a href="?delete_id=<?= urlencode($item["id"]) ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Delete this product?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-products">No products added yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>