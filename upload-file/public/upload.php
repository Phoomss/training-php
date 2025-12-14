<?php
// Check if we're updating an existing file
$fileData = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    require_once '../configs/connect.php';
    $stmt = $conn->prepare("SELECT * FROM files WHERE id = ?");
    $stmt->execute([$id]);
    $fileData = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?php echo $fileData ? 'Edit File' : 'Upload File'; ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?php echo $fileData ? 'Edit File' : 'Upload New File'; ?></h4>
                </div>
                <div class="card-body">
                    <form action="../backend/process_upload.php" method="post" enctype="multipart/form-data">
                        <?php if ($fileData): ?>
                            <input type="hidden" name="id" value="<?php echo $fileData['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">Current File:</label><br>
                                <?php if (str_starts_with($fileData['filetype'], 'image')): ?>
                                    <img src="<?php echo htmlspecialchars($fileData['filepath']); ?>" class="img-thumbnail" style="max-width:200px;">
                                <?php else: ?>
                                    <span class="badge bg-secondary">Document: <?php echo htmlspecialchars($fileData['filename']); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label"><?php echo $fileData ? 'Select new file (optional)' : 'Select file to upload'; ?></label>
                            <input type="file" name="file" class="form-control" <?php echo $fileData ? '' : 'required'; ?>>
                            <?php if ($fileData): ?>
                                <div class="form-text">Leave blank to keep the current file</div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="../index.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="<?php echo $fileData ? 'update' : 'upload'; ?>" class="btn btn-primary">
                                <?php echo $fileData ? 'Update File' : 'Upload File'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
