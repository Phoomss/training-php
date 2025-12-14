<!doctype html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>File Manager</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php
    require_once 'configs/connect.php';
    $stmt = $conn->query("SELECT * FROM files ORDER BY uploaded_at DESC");
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container py-5">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">📁 File Manager</h3>
            <a href="public/upload.php" class="btn btn-primary">
                ➕ Add File
            </a>
        </div>

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 140px;">Preview</th>
                            <th>File</th>
                            <th style="width: 300px;">Update</th>
                            <th style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (count($files) === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    ไม่มีไฟล์
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($files as $file): ?>
                            <tr>

                                <!-- Preview -->
                                <td>
                                    <?php if (str_starts_with($file['filetype'], 'image')): ?>
                                        <img src="<?= htmlspecialchars($file['filepath']) ?>"
                                            class="img-thumbnail"
                                            style="max-width:120px;">
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Document</span>
                                    <?php endif; ?>
                                </td>

                                <!-- File name -->
                                <td>
                                    <a href="<?= htmlspecialchars($file['filepath']) ?>"
                                        target="_blank"
                                        class="fw-semibold text-decoration-none">
                                        <?= htmlspecialchars($file['filename']) ?>
                                    </a>
                                    <div class="text-muted small">
                                        <?= number_format($file['filesize'] / 1024, 2) ?> KB
                                    </div>
                                </td>

                                <!-- Update -->
                                <td>
                                    <a href="public/upload.php?id=<?= $file['id'] ?>"
                                       class="btn btn-warning btn-sm">
                                        Update
                                    </a>
                                </td>

                                <!-- Delete -->
                                <td>
                                    <form action="backend/process_upload.php"
                                        method="post"
                                        onsubmit="return confirm('ยืนยันการลบไฟล์?');">
                                        <input type="hidden" name="id" value="<?= $file['id'] ?>">
                                        <button type="submit"
                                            name="delete"
                                            class="btn btn-danger btn-sm w-100">
                                            ลบ
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>