<?php
session_start();

// Include TCPDF library
require_once('../TCPDF-main/tcpdf.php');

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    echo "User not logged in.";
    exit();
}

// Get cv_id from GET
if (isset($_GET['id'])) {
    $cv_id = intval($_GET['id']);

    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "phplogin"; // Update with your actual database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch CV data
    $stmt = $conn->prepare("SELECT * FROM resume WHERE ID = ? AND name = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $name = $_SESSION['name'];
    $stmt->bind_param('is', $cv_id, $name);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $cv_data = $result->fetch_assoc();

        // Extend TCPDF class to create custom header and footer
        class MYPDF extends TCPDF {
            // Page header
            public function Header() {
                // Set font
                $this->SetFont('helvetica', 'B', 20);
                // Title (empty since we handle it in HTML)
                $this->Cell(0, 15, '', 0, 1, 'C', 0, '', 0);
            }

            // Page footer
            public function Footer() {
                // Position at 15 mm from bottom
                $this->SetY(-15);
                // Set font
                $this->SetFont('helvetica', 'I', 8);
                // Page number
                $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
            }
        }

        // Create new PDF instance
        $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Resume Builder');
        $pdf->SetTitle('Resume');
        $pdf->SetSubject('Resume');
        $pdf->SetKeywords('Resume, PDF, PHP');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(20, 20, 20);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Retrieve data from $cv_data
        $full_name = $cv_data["full_name"] ?? "";
        $email = $cv_data["email"] ?? "";
        $mobile = $cv_data["mobile"] ?? "";
        $address = $cv_data["address"] ?? "";
        $professional_summary = $cv_data["professional_summary"] ?? "";
        $job_sector_preference = $cv_data["job_sector_preference"] ?? "";
        $skills = json_decode($cv_data["skills"], true) ?? [];
        $experiences = json_decode($cv_data["experiences"], true) ?? [];
        $educations = json_decode($cv_data["educations"], true) ?? [];

        // Sanitize file name
        $file_name = preg_replace('/[^A-Za-z0-9_\- ]/', '', $cv_data['file_name']);
        $file_name = trim($file_name);
        $file_name = str_replace(' ', '_', $file_name); // Replace spaces with underscores
        $file_name .= '.pdf';

        // Start building the PDF content
        $html = '<h1 style="text-align:center;">' . htmlspecialchars($full_name) . '</h1>';
        $html .= '<p style="text-align:center;">' . htmlspecialchars($email) . ' | ' . htmlspecialchars($mobile) . '</p>';
        $html .= '<p style="text-align:center;">' . htmlspecialchars($address) . '</p>';
        $html .= '<hr>';

        // Professional Summary
        if (!empty($professional_summary)) {
            $html .= '<h2 style="color: #007BFF;">Professional Summary</h2>';
            $html .= '<p>' . nl2br(htmlspecialchars($professional_summary)) . '</p>';
        }

        // Experience
        if (!empty($experiences)) {
            $html .= '<h2 style="color: #007BFF;">Experience</h2>';
            foreach ($experiences as $experience) {
                if (!empty($experience['job_title'])) {
                    $html .= '<div style="margin-bottom: 20px;">';

                    $html .= '<div style="overflow: hidden;">';
                    $html .= '<span style="font-weight: bold; font-size: 14px;">' . htmlspecialchars($experience['job_title']) . '</span>';

                    if (!empty($experience['years'])) {
                        $html .= '<span style="float: right; font-size: 12px;">' . htmlspecialchars($experience['years']) . ' years</span>';
                    }
                    $html .= '</div>';

                    if (!empty($experience['company_name'])) {
                        $html .= '<div><i>' . htmlspecialchars($experience['company_name']) . '</i></div>';
                    }

                    if (!empty($experience['description'])) {
                        $html .= '<p>' . nl2br(htmlspecialchars($experience['description'])) . '</p>';
                    }

                    $html .= '</div>';
                }
            }
        }

        // Education
        if (!empty($educations)) {
            $html .= '<h2 style="color: #007BFF;">Education</h2>';
            foreach ($educations as $education) {
                if (!empty($education['degree'])) {
                    $html .= '<div style="margin-bottom: 20px;">';

                    $html .= '<div style="overflow: hidden;">';
                    $html .= '<span style="font-weight: bold; font-size: 14px;">' . htmlspecialchars($education['degree']) . '</span>';

                    if (!empty($education['year'])) {
                        $html .= '<span style="float: right; font-size: 12px;">' . htmlspecialchars($education['year']) . '</span>';
                    }
                    $html .= '</div>';

                    if (!empty($education['institution_name'])) {
                        $html .= '<div><i>' . htmlspecialchars($education['institution_name']) . '</i></div>';
                    }

                    if (!empty($education['description'])) {
                        $html .= '<p>' . nl2br(htmlspecialchars($education['description'])) . '</p>';
                    }

                    $html .= '</div>';
                }
            }
        }

        // Skills
        if (!empty($skills)) {
            $html .= '<h2 style="color: #007BFF;">Skills</h2>';
            $html .= '<ul>';
            foreach ($skills as $skill) {
                if (!empty($skill)) {
                    $html .= '<li>' . htmlspecialchars($skill) . '</li>';
                }
            }
            $html .= '</ul>';
        }

        // Additional Information
        if (!empty($job_sector_preference)) {
            $html .= '<h2 style="color: #007BFF;">Additional Information</h2>';
            $html .= '<p><strong>Job Sector Preference:</strong> ' . htmlspecialchars($job_sector_preference) . '</p>';
        }

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output the PDF as a download
        $pdf->Output($file_name, 'D'); // 'D' forces download

        exit();
    } else {
        echo "CV not found.";
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
    exit();
}
?>
