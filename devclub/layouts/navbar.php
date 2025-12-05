<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold" href="#">DevClub</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" aria-controls="navbarNav" 
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="./members.php">Members</a>
                </li>

                <!-- <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "ADMIN"): ?>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="./dashboard.php">Admin Panel</a>
                </li>
                <?php endif; ?> -->
            </ul>

            <!-- Right menu -->
            <ul class="navbar-nav ms-auto">

                <?php if (!isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="./login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm" href="./register.php">Register</a>
                    </li>
                <?php else: ?>
                
                    <li class="nav-item">
                        <span class="nav-link fw-bold">
                            👤 <?= htmlspecialchars($_SESSION['username']); ?>
                            (<?= $_SESSION['role']; ?>)
                        </span>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-outline-danger btn-sm" href="./logout.php">
                            Logout
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

        </div>
    </div>
</nav>
