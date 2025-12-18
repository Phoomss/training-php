<?php
session_start();
$role = $_SESSION['role'] ?? null;
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/index.php">Repair System</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarRepair" aria-controls="navbarRepair"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarRepair">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- ทุก role เห็น -->
                <li class="nav-item">
                    <a class="nav-link" href="/index.php">หน้าแรก</a>
                </li>

                <!-- ADMIN -->
                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/frontend/admin/equipment.php">จัดการอุปกรณ์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/frontend/admin/repair.php">สถานะการซ่อม</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/frontend/admin/report.php">รายงานแจ้งซ่อม</a>
                    </li>
                <?php endif; ?>

                <!-- STUDENT -->
                <?php if ($role === 'student'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/frontend/student/create-repair.php">แจ้งซ่อม</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/frontend/student/my-repairs.php">รายงานแจ้งซ่อม</a>
                    </li>
                <?php endif; ?>

                <!-- TECHNICAL -->
                <?php if ($role === 'technical'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/frontend/technical/repairs.php">รายการแจ้งซ่อม</a>
                    </li>
                <?php endif; ?>

            </ul>

            <!-- USER INFO -->
            <ul class="navbar-nav ms-auto">
                <?php if ($role): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown"
                           data-bs-toggle="dropdown">
                            <?= htmlspecialchars($_SESSION['username']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item text-danger" href="/logout.php">
                                    ออกจากระบบ
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login.php">เข้าสู่ระบบ</a>
                    </li>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</nav>
