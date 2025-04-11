<?php
// Start the session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'home_logged_users.php';

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    echo "User not logged in.";
    exit(); // Exit to prevent further execution
}



// Fetch only CVs created by the logged-in user
$name = $_SESSION['name']; // Ensure this is the correct session variable

$query = $conn->prepare("SELECT * FROM `resume` WHERE name = ?");

// Check if the prepare statement failed
if ($query === false) {
    die("Prepare failed: " . $conn->error);
}

$query->bind_param('s', $name);

// Execute the query
$query->execute();
$result = $query->get_result();

// Close the prepared statement
$query->close();

// Check if there's a message to display (e.g., after deletion)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CV History</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS (if any) -->
    <style>
        body {
            padding-top: 50px;
        }
        .container {
            max-width: 900px;
            margin-top: 30px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>CV History</h2>

    <!-- Display success or error message -->
    <?php if (isset($message)): ?>
        <div id="message" class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php
    // Check if any CVs are found
    if ($result && $result->num_rows > 0) {
        // Display CVs in a table
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead class='thead-dark'>";
        echo "<tr><th>ID</th><th>CV Title</th><th>Full Name</th><th>Creation Date</th><th>Action</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            $cv_id = $row['ID'];
            echo "<tr>";
            echo "<td>{$cv_id}</td>";
            echo "<td>{$row['file_name']}</td>";
            echo "<td>{$row['full_name']}</td>";
            echo "<td>{$row['creation_date']}</td>";
            echo "<td>";
            echo "<a href='view_resume.php?id={$cv_id}' class='btn btn-info btn-sm'>View</a> ";
            echo "<a href='edit_cv.php?id={$cv_id}' class='btn btn-primary btn-sm'>Edit</a> ";
            echo "<a href='download_cv.php?id={$cv_id}' class='btn btn-success btn-sm'>Download</a> ";
            echo "<button onclick='confirmDelete({$cv_id})' class='btn btn-danger btn-sm'>Delete</button>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>No CVs found.</p>";
    }

    // Close database connection
    $conn->close();
    ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="deleteForm" action="delete_cv.php" method="post">
      <input type="hidden" name="cv_id" id="cv_id_to_delete">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Delete CV</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
       <div class="modal-body">
          Are you sure you want to delete this CV?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete CV</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script>
function confirmDelete(cvId) {
    $('#cv_id_to_delete').val(cvId);
    $('#deleteModal').modal('show');
}

$(document).ready(function() {
    // Hide the message after 5 seconds
    setTimeout(function() {
        $("#message").fadeOut('slow');
    }, 5000); // 5000 milliseconds = 5 seconds
});
</script>

</body>
</html>
