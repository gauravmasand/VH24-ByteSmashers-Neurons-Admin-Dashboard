<?php
require 'tcpdf/tcpdf.php';

// Suppress warnings that cause premature output
error_reporting(0); 
// or configure to only log errors without displaying them
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);

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

// Set font for header
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Write(0, 'User Report', '', 0, 'C', true, 0, false, false, 0);

// Set font for table content
$pdf->SetFont('helvetica', '', 12);

// Start HTML for table
$html = '<table border="1" cellspacing="3" cellpadding="4">
            <thead>
                <tr style="background-color:#f2f2f2;">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>IP</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Registration Date</th>
                    <th>Version</th>
                </tr>
            </thead>
            <tbody>';

// Fetch user data from API with error handling
$apiUrl = 'https://auth-web-api.onrender.com/api/users';
$jsonData = file_get_contents($apiUrl);

if ($jsonData === false) {
    // Add a row to indicate failure in data retrieval
    $html .= '<tr><td colspan="8">Failed to retrieve data.</td></tr>';
    $totalUsers = count($data);
} else {
    $data = json_decode($jsonData, true);

    // Get filter status from POST request
    $statusFilter = isset($_POST['statusFilter']) ? filter_var($_POST['statusFilter'], FILTER_SANITIZE_STRING) : '';

    // Filter users based on the selected status
    $filteredUsers = array_filter($data, function ($user) use ($statusFilter) {
        // Check if status key exists to prevent undefined array key warning
        if (!isset($user['status'])) {
            return false; // Skip users without a status key
        }
        if ($statusFilter === '') {
            return true; // No filter, return all users
        }
        return $user['status'] === $statusFilter;
    });

    // Display the filtered or all users
    if (!empty($filteredUsers)) {
        foreach ($filteredUsers as $serialNumber => $user) {
            // Check and assign default values to avoid undefined array key issues
            $userName = isset($user['name']) ? htmlspecialchars($user['name']) : 'N/A';
            $userEmail = isset($user['email']) ? htmlspecialchars($user['email']) : 'N/A';
            $userIp = isset($user['ip']) ? htmlspecialchars($user['ip']) : 'N/A';
            $userLocation = isset($user['location']['country']) ? htmlspecialchars($user['location']['country']) : 'N/A';
            $userStatus = isset($user['status']) ? htmlspecialchars($user['status']) : 'N/A';
            $userSignupDate = isset($user['signupDate']) ? htmlspecialchars(date('Y-m-d', strtotime($user['signupDate']))) : 'N/A';
            $userVersion = isset($user['__v']) ? htmlspecialchars($user['__v']) : 'N/A';

            $html .= '<tr>
                        <td>' . ($serialNumber + 1) . '</td>
                        <td>' . $userName . '</td>
                        <td>' . $userEmail . '</td>
                        <td>' . $userIp . '</td>
                        <td>' . $userLocation . '</td>
                        <td>' . $userStatus . '</td>
                        <td>' . $userSignupDate . '</td>
                        <td>' . $userVersion . '</td>
                      </tr>';
        }
    } else {
        $html .= '<tr><td colspan="8">No data found for the selected filter.</td></tr>';
    }
}

$html .= '</tbody></table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document for download
$pdf->Output('user_report.pdf', 'D');
?>
