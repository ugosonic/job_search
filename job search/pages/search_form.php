<?php
include 'home_logged_users.php';
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Form</title>
    <link rel="stylesheet" type="text/css" href="../css/search_cv.css">
    
    <script>
        function validateForm() {
            var job_sector_preference = document.getElementById("job_sector_preference").value;
            var education_level = document.getElementById("education_level").value;
            var gcse_passes = document.getElementById("gcse_passes").value;
            var educational_qualification = document.getElementById("educational_qualification").value;
            var professional_qualification = document.getElementById("professional_qualification").value;
            var skill = document.getElementById("skill").value;
            var experience = document.getElementById("experience").value;

            if (job_sector_preference === "" && education_level === "" && gcse_passes === "" &&
                educational_qualification === "" && professional_qualification === "" &&
                skill === "" && experience === "") {
                alert("Please provide at least one search criteria.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <form action="search_results.php" method="GET" onsubmit="return validateForm()">
        <label for="job_sector_preference">Job or Sector Preference:</label>
        <select name="job_sector_preference" id="job_sector_preference">
            <option value="">Select...</option>
            <option value="Technology">Technology</option>
            <option value="Finance">Finance</option>
            <option value="Healthcare">Healthcare</option>
            <!-- Add more options as needed -->
        </select><br><br>

        <label for="education_level">Minimum Education Level:</label>
        <select name="education_level" id="education_level">
            <option value="">Select...</option>
            <option value="Level 1">Level 1</option>
            <option value="Level 2">Level 2</option>
            <option value="Level 3">Level 3</option>
            <option value="Level 4">Level 4</option>
            <option value="Level 5">Level 5</option>
            <!-- Add more options as needed -->
        </select><br><br>

        <label for="gcse_passes">Minimum Number of GCSE Passes:</label>
        <input type="number" name="gcse_passes" id="gcse_passes"><br><br>

        <label for="educational_qualification">Specific Educational Qualification:</label>
        <input type="text" name="educational_qualification" id="educational_qualification"><br><br>

        <label for="professional_qualification">Specific Professional Qualification:</label>
        <input type="text" name="professional_qualification" id="professional_qualification"><br><br>

        <label for="skill">Specific Skill:</label>
        <input type="text" name="skill" id="skill"><br><br>

        <label for="experience">Experience:</label>
        <textarea name="experience" id="experience" rows="4" cols="50"></textarea><br><br>

        <button type="submit">Search</button>
    </form>
</body>
</html>
