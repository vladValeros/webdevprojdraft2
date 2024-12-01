<?php
// Ensure the session is started to access the user's session data.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set a default username if the session is not set (for testing purposes).
$username = isset($_SESSION['user']['username']) ? htmlspecialchars($_SESSION['user']['username']) : 'Guest';
?>


<nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <div class="container-fluid">
        <a class="navbar-brand" href="../admin/dashboard.php">Admin Dashboard</a>
        <!-- Hamburger Menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Collapsible Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../admin/events.php">Event Manager</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../admin/attendance.php">Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../admin/students.php">Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../admin/users.php">Users</a>
                </li>
            </ul>
            <!-- User Profile Section -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../assets/user-silhouette.png" alt="User Icon" class="rounded-circle me-2" width="40" height="40">
                    <span><?= $username; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userProfileDropdown">
                    <li><a class="dropdown-item" href="../admin/users.php">View Users</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../account/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Link to Prebuilt Bootstrap JS -->
<script src="../js/bootstrap.bundle.min.js"></script>
