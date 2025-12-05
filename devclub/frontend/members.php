<?php
require_once "../configs/admin_only.php";
require_once "../configs/connect.php";

// SEARCH
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$search_sql = "";
$params = [];

if ($search !== "") {
    $search_sql = " AND (
        m.firstname LIKE :kw 
        OR m.lastname LIKE :kw
        OR m.email LIKE :kw
        OR mj.name LIKE :kw
    )";
    $params[':kw'] = "%$search%";
}

//    PAGINATION
$limit = 10; // จำนวนรายการต่อหน้า
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // กันค่า 0 หรือ -1

$offset = ($page - 1) * $limit;

/* --- หาจำนวนข้อมูลทั้งหมด --- */
$count_sql = "SELECT COUNT(*) 
              FROM members m
              LEFT JOIN majors mj ON m.major = mj.id
              WHERE 1 $search_sql";

$stmt_count = $conn->prepare($count_sql);
$stmt_count->execute($params);
$total_rows = $stmt_count->fetchColumn();

$total_pages = ceil($total_rows / $limit);

/* --- Query ข้อมูลจริง --- */
$sql = "SELECT m.*, mj.name AS major_name 
        FROM members m 
        LEFT JOIN majors mj ON m.major = mj.id
        WHERE 1 $search_sql
        ORDER BY m.id ASC
        LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการสมาชิก</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ปุ่มแก้ไข / ลบบนหน้าจอมือถือให้เรียงลง */
        @media (max-width: 576px) {
            .btn-group-mobile {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }
        }
    </style>
</head>

<body class="bg-light">

    <?php require_once "../layouts/navbar.php"; ?>

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>รายการสมาชิก</h2>
            <a href="form_member.php" class="btn btn-primary">+ เพิ่มสมาชิกใหม่</a>
        </div>

        <!-- Alerts -->
        <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text"
                            class="form-control"
                            name="search"
                            placeholder="ค้นหาชื่อ / อีเมล / สาขา"
                            value="<?= htmlspecialchars($search) ?>">

                        <button class="btn btn-outline-primary" type="submit">ค้นหา</button>
                        <a href="members.php" class="btn btn-outline-secondary">ล้าง</a>
                    </div>
                </form>

                <!-- ตารางแบบ responsive -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>ชื่อ - นามสกุล</th>
                                <th>Email</th>
                                <th>สาขา</th>
                                <th>ชั้นปี</th>
                                <th width="140">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $counter = 1;
                            foreach ($members as $m): ?>
                                <tr>
                                    <td><?= $counter ?></td>

                                    <td><?= htmlspecialchars($m['title'] . " " . $m['firstname'] . " " . $m['lastname']) ?></td>

                                    <td><?= htmlspecialchars($m['email']) ?></td>

                                    <td><?= htmlspecialchars($m['major_name'] ?? "-") ?></td>

                                    <td><?= htmlspecialchars($m['year']) ?></td>

                                    <td>
                                        <div class="btn-group-mobile">
                                            <a href="form_member.php?id=<?= $m['id'] ?>"
                                                class="btn btn-warning btn-sm w-100">
                                                แก้ไข
                                            </a>

                                            <a href="../backend/member_api.php?delete=<?= $m['id'] ?>"
                                                class="btn btn-danger btn-sm w-100"
                                                onclick="return confirm('ยืนยันการลบสมาชิกนี้?');">
                                                ลบ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                $counter++;
                            endforeach; ?>

                            <?php if (count($members) === 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        — ไม่มีข้อมูลสมาชิก —
                                    </td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mt-3">

                            <!-- Prev -->
                            <li class="page-item <?= ($page <= 1 ? 'disabled' : '') ?>">
                                <a class="page-link"
                                    href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                                    ก่อนหน้า
                                </a>
                            </li>

                            <!-- Numbers -->
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i ? 'active' : '') ?>">
                                    <a class="page-link"
                                        href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next -->
                            <li class="page-item <?= ($page >= $total_pages ? 'disabled' : '') ?>">
                                <a class="page-link"
                                    href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
                                    ถัดไป
                                </a>
                            </li>

                        </ul>
                    </nav>

                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>