<?php
// session_start();
$role = $_SESSION['role'] ?? null;
?>

<nav class="navbar navbar-expand-sm navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand"
            href="<?php echo $role === 'ADMIN' ? 'frontend/admin/index.php' : 'frontend/student/index.php'; ?>">
            <?php echo $role === 'ADMIN' ? 'Admin Dashboard' : 'Student Dashboard'; ?>
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                <?php if ($role === 'ADMIN'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'admin/index.php') !== false) ? 'active' : ''; ?>"
                            href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'admin/activites.php') !== false) ? 'active' : ''; ?>"
                            href="activites.php">Activities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'admin/form_activity.php') !== false) ? 'active' : ''; ?>"
                            href="form_activity.php">Add Activity</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'student/form_student.php') !== false) ? 'active' : ''; ?>"
                            href="../student/form_student.php">Manage Students</a>
                    </li>
                <?php elseif ($role === 'STUDENT'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'student/index.php') !== false) ? 'active' : ''; ?>"
                            href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'student/profile.php') !== false) ? 'active' : ''; ?>"
                            href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'student/activity_detail.php') !== false) ? 'active' : ''; ?>"
                            href="activity_detail.php">My Activities</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="navbar-text me-3">
                        <i class="bi bi-person-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-danger" href="../../backend/loginout_action.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>