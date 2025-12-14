<!doctype html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow-sm">
        <?php
        require_once '../configs/connect.php';

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $product = null;

        // If ID is provided, fetch the product for update
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();

            if (!$product) {
                die("Product not found.");
            }
        }
        ?>
        <div class="card-header fw-bold">
            <?php echo $id ? '✏️ Edit Product' : '➕ Add Product'; ?>
        </div>
        <div class="card-body">
            <form action="../backend/product_action.php" method="post" enctype="multipart/form-data">
                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="update" value="1">
                <?php else: ?>
                    <input type="hidden" name="create" value="1">
                <?php endif; ?>

                <div class="mb-3">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control"
                           value="<?php echo $id ? htmlspecialchars($product['name']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control"
                           value="<?php echo $id ? $product['price'] : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control">
                    <?php if ($id && !empty($product['image'])): ?>
                        <div class="mt-2">
                            <p>Current Image:</p>
                            <img src="../<?php echo $product['image']; ?>" alt="Current Image" width="100">
                        </div>
                        <div class="form-text">Leave blank to keep current image</div>
                    <?php else: ?>
                        <div class="form-text">Please select an image</div>
                    <?php endif; ?>
                </div>

                <button class="btn btn-<?php echo $id ? 'success' : 'primary'; ?>" type="submit">
                    <?php echo $id ? 'Update' : 'Save'; ?>
                </button>
                <a href="../index.php" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
