<!doctype html>
<html lang="en">

<head>
    <title>Products</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
</head>

<?php
require_once 'configs/connect.php';
$products = $conn->query("SELECT * FROM products")->fetchAll();
?>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-3">
            <h3>🛒 Products</h3>
            <a href="public/form_product.php" class="btn btn-success">Add Product</a>
        </div>

        <div class="row g-4">
            <?php foreach ($products as $p): ?>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <img src="<?= $p['image'] ?>" class="card-img-top" height="200">
                        <div class="card-body text-center">
                            <h6><?= $p['name'] ?></h6>
                            <p class="text-success fw-bold"><?= number_format($p['price'], 2) ?> ฿</p>

                            <div class="d-flex justify-content-center gap-2">
                                <a href="public/form_product.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="backend/product_action.php" method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button name="delete" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>