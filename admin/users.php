<?php
require '../classes/account.class.php';
require '../classes/database.class.php';
require '../tools/functions.php';
session_start();

// Redirect if the user is not an admin
Account::redirect_if_not_logged_in('admin');

global $pdo;

// Initialize variables
$username = $password = $role = "";
$username_err = $password_err = $role_err = "";

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate username
    if (empty(trim($_POST['username']))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST['username']);

        // Check for duplicate username
        $sql = "SELECT id FROM users WHERE username = :username";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $username_err = "This username is already taken.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter a password.";
    } else {
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    }

    // Validate role
    if (empty(trim($_POST['role']))) {
        $role_err = "Please select a role.";
    } else {
        $role = trim($_POST['role']);
    }

    // Check input errors before inserting or updating in the database
    if (empty($username_err) && empty($password_err) && empty($role_err)) {
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            // Update existing user
            $sql = "UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":password", $password);
                $stmt->bindParam(":role", $role);
                $stmt->bindParam(":id", $_POST['update_id']);
                if ($stmt->execute()) {
                    header("Location: users.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        } else {
            // Insert new user
            $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":password", $password);
                $stmt->bindParam(":role", $role);
                if ($stmt->execute()) {
                    header("Location: users.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        }
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $sql = "DELETE FROM users WHERE id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $_GET['delete_id']);
        if ($stmt->execute()) {
            header("Location: users.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
}

// Fetch all users
$sql = "SELECT * FROM users ORDER BY username ASC";
$users = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<body>
<?php include '../includes/_topnav.php'; ?>

<div class="container mt-4">
    <h1>Manage Users</h1>

    <!-- Form for Create and Update -->
    <form method="POST" class="mb-4">
        <input type="hidden" id="update_id" name="update_id">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control <?= !empty($username_err) ? 'is-invalid' : '' ?>" id="username" name="username">
            <span class="invalid-feedback"><?= $username_err ?></span>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control <?= !empty($password_err) ? 'is-invalid' : '' ?>" id="password" name="password">
            <span class="invalid-feedback"><?= $password_err ?></span>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control <?= !empty($role_err) ? 'is-invalid' : '' ?>" id="role" name="role">
                <option value="">Select role...</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="officer" <?= $role === 'officer' ? 'selected' : '' ?>>Officer</option>
            </select>
            <span class="invalid-feedback"><?= $role_err ?></span>
        </div>
        <button type="submit" class="btn btn-primary" id="form-submit-btn">Add User</button>
    </form>

    <h2>User List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-btn" 
                                data-id="<?= $user['id'] ?>" 
                                data-username="<?= htmlspecialchars($user['username']) ?>" 
                                data-role="<?= htmlspecialchars($user['role']) ?>">
                            Edit
                        </button>
                        <a href="users.php?delete_id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Populate the form for editing
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('update_id').value = this.dataset.id;
            document.getElementById('username').value = this.dataset.username;
            document.getElementById('password').value = '';  // Password reset required
            document.getElementById('role').value = this.dataset.role;
            document.getElementById('form-submit-btn').textContent = 'Update User';
        });
    });
</script>

</body>
</html>
