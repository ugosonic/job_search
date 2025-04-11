<?php
include 'home_logged_users.php';
include 'header.php';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Search Results</title>
    <link href="../css/style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body class="loggedin">
    <div class="content">
        <h2>Search Results</h2>
        <?php
        
// Initialize an array to store conditions for the WHERE clause
$conditions = [];

// Check if each field is provided in the search
if (!empty($_GET['job_sector_preference'])) {
    $conditions[] = "job_sector_preference = '" . $_GET['job_sector_preference'] . "'";
}
if (!empty($_GET['education_level'])) {
    $conditions[] = "education_level_1 >= '" . $_GET['education_level'] . "'";
}
if (!empty($_GET['gcse_passes'])) {
    $conditions[] = "gcse_passes_1 >= '" . $_GET['gcse_passes'] . "'";
}
if (!empty($_GET['educational_qualification'])) {
    $conditions[] = "educational_qualification_1 = '" . $_GET['educational_qualification'] . "'";
}
if (!empty($_GET['professional_qualification'])) {
    $conditions[] = "professional_qualification_1 = '" . $_GET['professional_qualification'] . "'";
}
if (!empty($_GET['skill'])) {
    $conditions[] = "skill = '" . $_GET['skill'] . "'";
}
if (!empty($_GET['experience'])) {
    $conditions[] = "experience_1 LIKE '%" . $_GET['experience'] . "%' OR experience_2 LIKE '%" . $_GET['experience'] . "%' OR experience_3 LIKE '%" . $_GET['experience'] . "%'";
}

// Construct the SQL query
$sql = "SELECT * FROM resume";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Perform the query
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<strong>Name:</strong> " . $row["full_name"] . " ";
        echo '<button class="see-more-btn">See More</button>';
        echo '<div class="details" style="display: none;">';
        // Output all details
        echo "<br><strong>Mobile:</strong> " . $row["mobile"] . "<br><br>";
        echo "<strong>Email:</strong> " . $row["email"] . "<br><br>";
        echo "<strong>Address:</strong> " . $row["address"] . "<br><br>";
        echo "<strong>Professional Summary:</strong> " . $row["professional_summary"] . "<br><br>";
        echo "<strong>Experience 1:</strong> " . $row["name_of_company_1"] . " <br> " . $row["experience_1"] . " (" . $row["experience_1_years"] . " years)<br><br>";
        echo "<strong>Experience 2:</strong> " . $row["name_of_company_2"] . " <br> " . $row["experience_2"] . " (" . $row["experience_2_years"] . " years)<br><br>";
        echo "<strong>Experience 3:</strong> " . $row["name_of_company_3"] . " <br> " . $row["experience_3"] . " (" . $row["experience_3_years"] . " years)<br><br>";
        echo "<strong>Education 1:</strong> " . $row["name_of_institution_1"] . " - " . $row["education_1"] . " (" . $row["education_level_1"] . ")<br><br>";
        echo "<strong>Education 2:</strong> " . $row["name_of_institution_2"] . " - " . $row["education_2"] . " (" . $row["education_level_2"] . ")<br><br>";
        echo "<strong>Education 3:</strong> " . $row["name_of_institution_3"] . " - " . $row["education_3"] . " (" . $row["education_level_3"] . ")<br><br>";
        echo "<strong>Qualifications and Passes:</strong><br>";
        echo "   - " . $row["educational_qualification_1"] . " / " . $row["gcse_passes_1"] . " / " . $row["professional_qualification_1"] . "<br><br>";
        echo "   - " . $row["educational_qualification_2"] . " / " . $row["gcse_passes_2"] . " / " . $row["professional_qualification_2"] . "<br><br>";
        echo "   - " . $row["educational_qualification_3"] . " / " . $row["gcse_passes_3"] . " / " . $row["professional_qualification_3"] . "<br><br>";
        echo "<strong>Job Sector Preference:</strong> " . $row["job_sector_preference"] . "<br><br>";
        echo "<strong>Skill:</strong> " . $row["skill"] . "<br>";
        // Output all other fields as needed
        echo '</div><br><br>';
    }
} else {
    echo "No results found.";
}

// Close connection
$conn->close();
?>

    <script>
    const seeMoreButtons = document.querySelectorAll('.see-more-btn');
    seeMoreButtons.forEach(button => {
        button.addEventListener('click', () => {
            const details = button.nextElementSibling;
            if (details.style.display === 'none' || details.style.display === '') {
                details.style.display = 'block';
                button.textContent = 'See Less';
            } else {
                details.style.display = 'none';
                button.textContent = 'See More';
            }
        });
    });
    </script>
    <script>
    const seeMoreButtons = document.querySelectorAll('.see-more-btn');
    console.log(seeMoreButtons); // Check if buttons are correctly selected
    seeMoreButtons.forEach(button => {
        console.log(button); // Check each button
        button.addEventListener('click', () => {
            console.log('Button clicked'); // Check if event listener is triggered
            const details = button.nextElementSibling;
            console.log(details); // Check if details element is correctly selected
            if (details.style.display === 'none' || details.style.display === '') {
                details.style.display = 'block';
                button.textContent = 'See Less';
            } else {
                details.style.display = 'none';
                button.textContent = 'See More';
            }
        });
    });
</script>

</body>
</html>
