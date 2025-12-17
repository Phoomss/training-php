<?php 
session_start();
require_once '../../configs/connect.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../../index.php');
    exit();
}

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE auth_id = :auth_id");
$stmt->execute([':auth_id' => $_SESSION['auth_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get categories
$stmt = $conn->prepare('SELECT * FROM categories ORDER BY name');
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get locations
$stmt = $conn->prepare('SELECT * FROM location ORDER BY name');
$stmt->execute();
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
    <head>
        <title>แจ้งซ่อม - ระบบแจ้งซ่อม</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>

    <body class="bg-danger-subtle">
        <header>
            <nav class="navbar navbar-expand-lg bg-danger">
                <div class="container-fluid">
                    <a class="navbar-brand text-white" href="index.php">ระบบแจ้งซ่อม</a>
                    <div class="navbar-nav ms-auto">
                        <span class="navbar-text text-white me-3">
                            สวัสดี, <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
                        </span>
                        <a class="btn btn-outline-light" href="../../backend/auth_action.php?logout=1">ออกจากระบบ</a>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container mt-4">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-5">
                    <div class="mb-4">
                        <h1>กรอกข้อมูลแจ้งซ่อม</h1>
                        <p class="text-muted">กรุณากรอกข้อมูลให้ครบถ้วน</p>
                    </div>
                    
                    <form action="../../backend/repair_action.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="add_repair" value="1">
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">เลือกหมวดหมู่ *</label>
                            <select class="form-select" required name="category_id" id="category_id" onchange="loadEquipment()">
                                <option value="">-- เลือกหมวดหมู่ --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="equipment_id" class="form-label">เลือกอุปกรณ์ *</label>
                            <select class="form-select" required name="equipment_id" id="equipment_id">
                                <option value="">-- เลือกหมวดหมู่ก่อน --</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location_id" class="form-label">เลือกตึก *</label>
                            <select class="form-select" required name="location_id" id="location_id" onchange="loadLocationDetails()">
                                <option value="">-- เลือกตึก --</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?= $location['id'] ?>"><?= htmlspecialchars($location['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="locationD_id" class="form-label">เลือกห้อง *</label>
                            <select class="form-select" required name="locationD_id" id="locationD_id">
                                <option value="">-- เลือกตึกก่อน --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="details" class="form-label">รายละเอียดปัญหา *</label>
                            <textarea class="form-control" name="details" id="details" rows="3" required 
                                      placeholder="อธิบายปัญหาที่พบ เช่น เปิดไม่ติด, เสียงดัง, หน้าจอเสีย"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">เลือกรูปภาพ *</label>
                            <input type="file" class="form-control" name="image" id="image" required 
                                   accept=".png,.jpg,.jpeg" onchange="previewImage(this)">
                            <div class="form-text">รองรับไฟล์ .jpg, .jpeg, .png เท่านั้น</div>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary">ส่งรายการแจ้งซ่อม</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <script>
            function loadEquipment() {
                const categoryId = document.getElementById('category_id').value;
                const equipmentSelect = document.getElementById('equipment_id');
                
                equipmentSelect.innerHTML = '<option value="">-- กำลังโหลด... --</option>';
                
                if (categoryId) {
                    fetch(`../../backend/get_equipment.php?category_id=${categoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            equipmentSelect.innerHTML = '<option value="">-- เลือกอุปกรณ์ --</option>';
                            data.forEach(equipment => {
                                equipmentSelect.innerHTML += `<option value="${equipment.id}">${equipment.name}</option>`;
                            });
                        })
                        .catch(error => {
                            equipmentSelect.innerHTML = '<option value="">-- เกิดข้อผิดพลาด --</option>';
                        });
                } else {
                    equipmentSelect.innerHTML = '<option value="">-- เลือกหมวดหมู่ก่อน --</option>';
                }
            }

            function loadLocationDetails() {
                const locationId = document.getElementById('location_id').value;
                const locationDetailSelect = document.getElementById('locationD_id');
                
                locationDetailSelect.innerHTML = '<option value="">-- กำลังโหลด... --</option>';
                
                if (locationId) {
                    fetch(`../../backend/get_location_details.php?location_id=${locationId}`)
                        .then(response => response.json())
                        .then(data => {
                            locationDetailSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
                            data.forEach(detail => {
                                locationDetailSelect.innerHTML += `<option value="${detail.id}">ชั้น ${detail.floor} ห้อง ${detail.room}</option>`;
                            });
                        })
                        .catch(error => {
                            locationDetailSelect.innerHTML = '<option value="">-- เกิดข้อผิดพลาด --</option>';
                        });
                } else {
                    locationDetailSelect.innerHTML = '<option value="">-- เลือกตึกก่อน --</option>';
                }
            }

            function previewImage(input) {
                const preview = document.getElementById('imagePreview');
                
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">`;
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                } else {
                    preview.innerHTML = '';
                }
            }
        </script>
                    
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
