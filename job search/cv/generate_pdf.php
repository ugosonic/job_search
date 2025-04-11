<?php
require_once('../TCPDF-main/tcpdf.php');

// Extend TCPDF class to create custom header and footer
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Logo (uncomment and set path if needed)
        // $this->Image('path/to/logo.png', 10, 10, 30);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
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

// Fetch and display form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve personal information
    $full_name = isset($_POST["full_name"]) ? $_POST["full_name"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $mobile = isset($_POST["mobile"]) ? $_POST["mobile"] : "";
    $address = isset($_POST["address"]) ? $_POST["address"] : "";
    $professional_summary = isset($_POST["professional_summary"]) ? $_POST["professional_summary"] : "";
    $job_sector_preference = isset($_POST["job_sector_preference"]) ? $_POST["job_sector_preference"] : "";
    $skills = isset($_POST["skills"]) ? $_POST["skills"] : [];

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
    if (isset($_POST['experience']) && is_array($_POST['experience']) && !empty($_POST['experience'][0]['job_title'])) {
        $html .= '<h2 style="color: #007BFF;">Experience</h2>';
        foreach ($_POST['experience'] as $experience) {
            // Check if job title is not empty
            if (!empty($experience['job_title'])) {
                $html .= '<div style="margin-bottom: 20px;">';

                // Job title in bold
                $html .= '<div style="overflow: hidden;">';
                $html .= '<span style="font-weight: bold; font-size: 14px;">' . htmlspecialchars($experience['job_title']) . '</span>';

                // Years of experience aligned to the right
                if (!empty($experience['years'])) {
                    $html .= '<span style="float: right; font-size: 12px;">' . htmlspecialchars($experience['years']) . ' years</span>';
                }
                $html .= '</div>';

                // Company name in italics under job title
                if (!empty($experience['company_name'])) {
                    $html .= '<div><i>' . htmlspecialchars($experience['company_name']) . '</i></div>';
                }

                // Description
                if (!empty($experience['description'])) {
                    $html .= '<p>' . nl2br(htmlspecialchars($experience['description'])) . '</p>';
                }

                $html .= '</div>';
            }
        }
    }

    // Education
    if (isset($_POST['education']) && is_array($_POST['education']) && !empty($_POST['education'][0]['degree'])) {
        $html .= '<h2 style="color: #007BFF;">Education</h2>';
        foreach ($_POST['education'] as $education) {
            // Check if degree is not empty
            if (!empty($education['degree'])) {
                $html .= '<div style="margin-bottom: 20px;">';

                // Degree in bold
                $html .= '<div style="overflow: hidden;">';
                $html .= '<span style="font-weight: bold; font-size: 14px;">' . htmlspecialchars($education['degree']) . '</span>';

                // Year of graduation aligned to the right
                if (!empty($education['year'])) {
                    $html .= '<span style="float: right; font-size: 12px;">' . htmlspecialchars($education['year']) . '</span>';
                }
                $html .= '</div>';

                // Institution name in italics under degree
                if (!empty($education['institution_name'])) {
                    $html .= '<div><i>' . htmlspecialchars($education['institution_name']) . '</i></div>';
                }

                // Description
                if (!empty($education['description'])) {
                    $html .= '<p>' . nl2br(htmlspecialchars($education['description'])) . '</p>';
                }

                $html .= '</div>';
            }
        }
    }

    // Skills
    if (!empty($skills) && !empty($skills[0])) {
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

    // Close and output PDF
    $pdf->Output('resume.pdf', 'D');
} else {
    // Redirect back to form if no POST data
    header('Location: resume_builder.php');
    exit;
}
?>
