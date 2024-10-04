<?php
require 'tcpdf/tcpdf.php';

// Create new PDF document
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('User Report');
$pdf->SetSubject('User Data');
$pdf->SetKeywords('TCPDF, PDF, report, user data');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 12);

// Header
$html = '<h1>User Report</h1>';
$html .= '<table border="1" cellspacing="3" cellpadding="4">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>IP</th>
                <th>Location</th>
                <th>Status</th>
                <th>Registration Date</th>
                <th>Version</th>
            </tr>';

// Fetch the user data from the API
$apiUrl = 'https://auth-web-api.onrender.com/api/users';
$jsonData = @file_get_contents($apiUrl);
$data = json_decode($jsonData, true);

// Get the filter status from the POST request
$statusFilter = isset($_POST['statusFilter']) ? $_POST['statusFilter'] : '';

// Check if data is successfully fetched
if ($data) {
    // Filter users based on the selected status
    $filteredUsers = array_filter($data, function($user) use ($statusFilter) {
        return $statusFilter === '' || (isset($user['status']) && $user['status'] === $statusFilter);
    });

    foreach ($filteredUsers as $serialNumber => $user) {
        $html .= '<tr>
                    <td>' . ($serialNumber + 1) . '</td>
                    <td>' . htmlspecialchars($user['name']) . '</td>
                    <td>' . htmlspecialchars($user['email']) . '</td>
                    <td>' . htmlspecialchars($user['ip']) . '</td>
                    <td>' . htmlspecialchars($user['location']['country']) . '</td>
                    <td>' . htmlspecialchars($user['status']) . '</td>
                    <td>' . htmlspecialchars(date('Y-m-d', strtotime($user['signupDate']))) . '</td>
                    <td>' . htmlspecialchars($user['__v']) . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="8">Failed to retrieve data.</td></tr>';
}

$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('user_report.pdf', 'D'); // D for download
?>
