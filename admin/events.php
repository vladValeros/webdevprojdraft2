<?php
require '../classes/account.class.php';
require '../classes/database.class.php';
require '../classes/events.class.php';
require '../tools/functions.php';
session_start();

// Redirect if the user is not an admin
Account::redirect_if_not_logged_in('admin');

global $pdo;
$eventsClass = new Events($pdo);

// Initialize variables
$title = $description = $organizers = $starttime = $endtime = $venue = $event_date = "";
$title_err = $description_err = $organizers_err = $starttime_err = $endtime_err = $venue_err = $event_date_err = "";

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs (validation logic remains the same as the original)
    if (empty(trim($_POST['title']))) {
        $title_err = "Please enter the event title.";
    } else {
        $title = trim($_POST['title']);
    }

    if (empty(trim($_POST['description']))) {
        $description_err = "Please enter the event description.";
    } else {
        $description = trim($_POST['description']);
    }

    if (empty(trim($_POST['organizers']))) {
        $organizers_err = "Please enter the organizers.";
    } else {
        $organizers = trim($_POST['organizers']);
    }

    if (empty(trim($_POST['starttime']))) {
        $starttime_err = "Please enter the start time.";
    } else {
        $starttime = trim($_POST['starttime']);
    }

    if (empty(trim($_POST['endtime']))) {
        $endtime_err = "Please enter the end time.";
    } elseif ($starttime >= trim($_POST['endtime'])) {
        $endtime_err = "End time cannot be earlier than or equal to the start time.";
    } else {
        $endtime = trim($_POST['endtime']);
    }

    if (empty(trim($_POST['venue']))) {
        $venue_err = "Please enter the venue.";
    } else {
        $venue = trim($_POST['venue']);
    }

    if (empty(trim($_POST['event_date']))) {
        $event_date_err = "Please enter the event date.";
    } else {
        $event_date = trim($_POST['event_date']);
    }

    // Check input errors before inserting or updating
    if (empty($title_err) && empty($description_err) && empty($organizers_err) &&
        empty($starttime_err) && empty($endtime_err) && empty($venue_err) && empty($event_date_err)) {

        if (!empty($_POST['update_id'])) {
            // Update existing event
            $eventsClass->updateEvent($_POST['update_id'], $title, $description, $organizers, $starttime, $endtime, $venue, $event_date);
        } else {
            // Add new event
            $eventsClass->addEvent($title, $description, $organizers, $starttime, $endtime, $venue, $event_date);
        }
        header("Location: events.php");
        exit();
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $eventsClass->deleteEvent($_GET['delete_id']);
    header("Location: events.php");
    exit();
}

// Fetch all events
$events = $eventsClass->getAllEvents();
?>


<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<body>
<?php include '../includes/_topnav.php'; ?>

<div class="container mt-4">
    <h1>Manage Events</h1>

    <!-- Form for Create and Update -->
    <form method="POST" class="mb-4">
        <input type="hidden" id="update_id" name="update_id">
        <div class="form-group">
            <label for="title">Event Title</label>
            <input type="text" class="form-control <?= !empty($title_err) ? 'is-invalid' : '' ?>" id="title" name="title" value="<?= htmlspecialchars($title) ?>">
            <span class="invalid-feedback"><?= $title_err ?></span>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control <?= !empty($description_err) ? 'is-invalid' : '' ?>" id="description" name="description"><?= htmlspecialchars($description) ?></textarea>
            <span class="invalid-feedback"><?= $description_err ?></span>
        </div>
        <div class="form-group">
            <label for="organizers">Organizers</label>
            <input type="text" class="form-control <?= !empty($organizers_err) ? 'is-invalid' : '' ?>" id="organizers" name="organizers" value="<?= htmlspecialchars($organizers) ?>">
            <span class="invalid-feedback"><?= $organizers_err ?></span>
        </div>
        <div class="form-group">
            <label for="starttime">Start Time</label>
            <input type="time" class="form-control <?= !empty($starttime_err) ? 'is-invalid' : '' ?>" id="starttime" name="starttime" value="<?= htmlspecialchars($starttime) ?>">
            <span class="invalid-feedback"><?= $starttime_err ?></span>
        </div>
        <div class="form-group">
            <label for="endtime">End Time</label>
            <input type="time" class="form-control <?= !empty($endtime_err) ? 'is-invalid' : '' ?>" id="endtime" name="endtime" value="<?= htmlspecialchars($endtime) ?>">
            <span class="invalid-feedback"><?= $endtime_err ?></span>
        </div>
        <div class="form-group">
            <label for="venue">Venue</label>
            <input type="text" class="form-control <?= !empty($venue_err) ? 'is-invalid' : '' ?>" id="venue" name="venue" value="<?= htmlspecialchars($venue) ?>">
            <span class="invalid-feedback"><?= $venue_err ?></span>
        </div>
        <div class="form-group">
            <label for="event_date">Event Date</label>
            <input type="date" class="form-control <?= !empty($event_date_err) ? 'is-invalid' : '' ?>" id="event_date" name="event_date" value="<?= htmlspecialchars($event_date) ?>">
            <span class="invalid-feedback"><?= $event_date_err ?></span>
        </div>
        <button type="submit" class="btn btn-primary" id="form-submit-btn">Add Event</button>
    </form>

    <h2>Event List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Organizers</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Venue</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['title']) ?></td>
                    <td><?= htmlspecialchars($event['description']) ?></td>
                    <td><?= htmlspecialchars($event['organizers']) ?></td>
                    <td><?= htmlspecialchars($event['starttime']) ?></td>
                    <td><?= htmlspecialchars($event['endtime']) ?></td>
                    <td><?= htmlspecialchars($event['venue']) ?></td>
                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-btn" 
                                data-id="<?= $event['id'] ?>" 
                                data-title="<?= htmlspecialchars($event['title']) ?>" 
                                data-description="<?= htmlspecialchars($event['description']) ?>" 
                                data-organizers="<?= htmlspecialchars($event['organizers']) ?>"
                                data-starttime="<?= htmlspecialchars($event['starttime']) ?>"
                                data-endtime="<?= htmlspecialchars($event['endtime']) ?>"
                                data-venue="<?= htmlspecialchars($event['venue']) ?>"
                                data-event_date="<?= htmlspecialchars($event['event_date']) ?>">
                            Edit
                        </button>
                        <a href="events.php?delete_id=<?= $event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/_footer.php'; ?>

<script src="../js/jquery.min.js"></script>
<script>
    // Populate the form for editing
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('update_id').value = this.dataset.id;
            document.getElementById('title').value = this.dataset.title;
            document.getElementById('description').value = this.dataset.description;
            document.getElementById('organizers').value = this.dataset.organizers;
            document.getElementById('starttime').value = this.dataset.starttime;
            document.getElementById('endtime').value = this.dataset.endtime;
            document.getElementById('venue').value = this.dataset.venue;
            document.getElementById('event_date').value = this.dataset.event_date;
            document.getElementById('form-submit-btn').textContent = 'Update Event';
        });
    });
</script>
</body>
</html>
