<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

// Fetch data for dropdowns
$usersStmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'student' ORDER BY username ASC");
$usersStmt->execute();
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

$equipmentStmt = $conn->prepare("SELECT id, name FROM equipment ORDER BY name ASC");
$equipmentStmt->execute();
$equipment = $equipmentStmt->fetchAll(PDO::FETCH_ASSOC);

$locationStmt = $conn->prepare("SELECT id, name FROM location ORDER BY name ASC");
$locationStmt->execute();
$locations = $locationStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch location details based on selected location
$location_details = [];
$location_id = filter_input(INPUT_GET, 'location_id', FILTER_VALIDATE_INT);
if ($location_id) {
    $loc_detailStmt = $conn->prepare("SELECT id, name FROM location_detail WHERE location_id = :location_id ORDER BY name ASC");
    $loc_detailStmt->execute([':location_id' => $location_id]);
    $location_details = $loc_detailStmt->fetchAll(PDO::FETCH_ASSOC);
}

$success_message = $_GET['status'] ?? '';
$error_message = $_GET['error'] ?? '';
?>

<!doctype html>
<html lang="en">

<head>
    <title>Create Repair Request</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php require_once '../layouts/navbar.php' ?>

    <main class="container mt-5">
        <div class="card shadow p-4">
            <h3 class="mb-4"><i class="bi bi-file-earmark-plus"></i> เพิ่มรายการแจ้งซ่อม</h3>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form action="../../backend/repair_action.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">ผู้แจ้ง *</label>
                            <select class="form-select" name="user_id" required>
                                <option value="">-- เลือกผู้แจ้ง --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>">
                                        <?= htmlspecialchars($user['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">อุปกรณ์ *</label>
                            <select class="form-select" name="equipment_id" required>
                                <option value="">-- เลือกอุปกรณ์ --</option>
                                <?php foreach ($equipment as $equip): ?>
                                    <option value="<?= $equip['id'] ?>">
                                        <?= htmlspecialchars($equip['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">ตึก *</label>
                            <select class="form-select" name="location_id" id="locationSelect" required>
                                <option value="">-- เลือกตึก --</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?= $location['id'] ?>">
                                        <?= htmlspecialchars($location['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">ห้อง *</label>
                            <select class="form-select" name="locationD_id" id="locationDetailSelect" required>
                                <option value="">-- เลือกห้อง --</option>
                                <?php if (!empty($_GET['location_id'])): ?>
                                    <?php foreach ($location_details as $detail): ?>
                                        <option value="<?= $detail['id'] ?>" 
                                            <?= (isset($_GET['locationD_id']) && $_GET['locationD_id'] == $detail['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($detail['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">รูปภาพอาการเสีย (jpg, jpeg, png) *</label>
                    <input type="file" class="form-control" name="image" accept=".jpg, .jpeg, .png" required>
                    <div class="form-text">เฉพาะไฟล์รูปภาพ: JPG, JPEG, PNG</div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" name="add_repair" class="btn btn-primary">
                        <i class="bi bi-save"></i> บันทึก
                    </button>
                    <a href="repair.php" class="btn btn-secondary">
                        <i class="bi bi-x"></i> ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('locationSelect').addEventListener('change', function() {
            const locationId = this.value;
            if(locationId) {
                // Reload the page with location_id to update location details
                window.location.href = 'form_repair.php?location_id=' + locationId;
            }
        });
    </script>
</body>

</html>