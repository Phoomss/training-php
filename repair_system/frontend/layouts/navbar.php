<?php
session_start();
$role = strtolower($_SESSION['role'] ?? '');
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bold"
            href="<?php echo $role === 'admin' ? 'frontend/admin/index.php' : 'frontend/student/index.php'; ?>">
            <?php echo $role === 'admin' ? 'Admin Dashboard' : 'Student Dashboard'; ?>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'admin/index.php') !== false) ? 'active' : ''; ?>"
                            href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'admin/activities.php') !== false) ? 'active' : ''; ?>"
                            href="activities.php">Activities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'admin/form_activity.php') !== false) ? 'active' : ''; ?>"
                            href="form_activity.php">Add Activity</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'student/form_student.php') !== false) ? 'active' : ''; ?>"
                            href="../student/form_student.php">Manage Students</a>
                    </li>
                <?php elseif ($role === 'student'): ?>
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

            <!-- User Info & Logout -->
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item d-flex align-items-center me-3">
                    <i class="bi bi-person-circle me-1"></i> <?php echo $username; ?>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger" href="../../backend/loginout_action.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>