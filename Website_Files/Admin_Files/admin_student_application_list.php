<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";

/* ========================= */
/* VERIFY ADMIN SESSION      */
/* ========================= */
$user_id = $_SESSION['user_id'];

// Prepare statement
$stmt = mysqli_prepare($connection, "
    SELECT * FROM users 
    WHERE user_id = ? 
    AND role_id = 2
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

/* ========================= */
/* FILTER PARAMETERS         */
/* ========================= */
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';

/* ✅ GET STUDENT APPLICATIONS WITH FILTERS - JOINING MULTIPLE TABLES */
// First try with JOINs, if fails fall back to simple query
try {
    $sql = "SELECT 
        sa.application_id, sa.lrn, sa.first_name, sa.last_name, sa.middle_name, sa.extension_name,
        sa.date_of_birth, sa.sex, sa.place_of_birth, sa.religion, sa.mother_tongue,
        sa.enrollment_type, sa.application_status, sa.email, sa.contact_number, sa.facebook_profile,
        sa.profile_image, sa.date_submitted, sa.remarks,
        COALESCE(doc.psa_birth_certificate, '') as psa_birth_certificate, 
        COALESCE(doc.form_138, '') as form_138, 
        COALESCE(doc.student_id_copy, '') as student_id_copy,
        COALESCE(addr.house_number, '') as house_number, 
        COALESCE(addr.street, '') as street, 
        COALESCE(addr.barangay, '') as barangay, 
        COALESCE(addr.city_municipality, '') as city_municipality, 
        COALESCE(addr.province, '') as province,
        COALESCE(fam.father_last_name, '') as father_last_name, 
        COALESCE(fam.father_first_name, '') as father_first_name, 
        COALESCE(fam.father_middle_name, '') as father_middle_name,
        COALESCE(fam.mother_last_name, '') as mother_last_name, 
        COALESCE(fam.mother_first_name, '') as mother_first_name, 
        COALESCE(fam.mother_middle_name, '') as mother_middle_name,
        COALESCE(soc.indigenous_community, 'No') as indigenous_community, 
        COALESCE(soc.four_ps_beneficiary, 'No') as four_ps_beneficiary
    FROM student_applications sa
    LEFT JOIN student_documents doc ON sa.application_id = doc.application_id
    LEFT JOIN student_addresses addr ON sa.application_id = addr.application_id
    LEFT JOIN student_family fam ON sa.application_id = fam.application_id
    LEFT JOIN student_social_info soc ON sa.application_id = soc.application_id
    WHERE sa.application_status = 'Pending'";
    
    $result = $connection->query($sql);
    
    // If query fails, it means tables don't exist - use fallback
    if (!$result) {
        throw new Exception($connection->error);
    }
} catch (Exception $e) {
    // Fallback to simple query using only student_applications table
    $sql = "SELECT * FROM student_applications WHERE application_status = 'Pending'";
    $result = $connection->query($sql);
}

if (!empty($search_name)) {
    $sql .= " AND (sa.first_name LIKE '%$search_name%' OR sa.last_name LIKE '%$search_name%' OR CONCAT(sa.first_name, ' ', sa.last_name) LIKE '%$search_name%')";
}

$sql .= " ORDER BY sa.application_id DESC";

$result = $connection->query($sql);

if (!$result) {
    die("Query failed: " . $connection->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Applications - CDONHS-SHS Admin</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/profile_dropdown.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <div class="center">
            Admin
        </div>
        <div class="right">
            <button class="profile-btn" type="button">
                <img src="../../Assets/admin_profile.png">
            </button>
            <div class="profile-dropdown">
                <a href="application_page.php">Dashboard</a>
                <a href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <!-- Page Title -->
    <div class="page-title">
        <h1>Student Application List</h1>
    </div>

    <!-- Navigation -->
    <div class="nav-links">
        <a href="home.php"> Back to Dashboard</a>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search_name">Search by Name:</label>
                    <input type="text" id="search_name" name="search_name" 
                           placeholder="Enter student name..." 
                           value="<?= htmlspecialchars($search_name); ?>">
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-filter">🔍 Search</button>
                    <button type="button" class="btn btn-confirm-batch" id="confirmBatchBtn">✓ Confirm Selected</button>
                    <a href="admin_student_application_list.php" class="btn btn-reset">↻ Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllCheckbox"></th>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>LRN</th>
                    <th>Email</th>
                    <th>PSA Birth Cert</th>
                    <th>Form 138</th>
                    <th>Student ID</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $count = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="application-row" data-application-id="<?= $row['application_id']; ?>">
                            <td><input type="checkbox" class="row-checkbox"></td>
                            <td><?= $count++; ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td><?= htmlspecialchars($row['lrn']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>

                            <!-- PSA -->
                            <td>
                                <?php if (!empty($row['psa_birth_certificate'])): ?>
                                    <a href="../../uploads/Documents/student/<?= htmlspecialchars($row['psa_birth_certificate']); ?>"
                                       target="_blank" class="doc-submitted">✓ View</a>
                                <?php else: ?>
                                    <span class="doc-missing">✗ Missing</span>
                                <?php endif; ?>
                            </td>

                            <!-- Form 138 -->
                            <td>
                                <?php if (!empty($row['form_138'])): ?>
                                    <a href="../../uploads/Documents/student/<?= htmlspecialchars($row['form_138']); ?>"
                                       target="_blank" class="doc-submitted">✓ View</a>
                                <?php else: ?>
                                    <span class="doc-missing">✗ Missing</span>
                                <?php endif; ?>
                            </td>

                            <!-- Student ID -->
                            <td>
                                <?php if (!empty($row['student_id_copy'])): ?>
                                    <a href="../../uploads/Documents/student/<?= htmlspecialchars($row['student_id_copy']); ?>"
                                       target="_blank" class="doc-submitted">✓ View</a>
                                <?php else: ?>
                                    <span class="doc-missing">✗ Missing</span>
                                <?php endif; ?>
                            </td>

                            <!-- Current Status -->
                            <td>
                                <?php 
                                $statusClass = 'status-pending';
                                if ($row['application_status'] == 'Approved') $statusClass = 'status-approved';
                                if ($row['application_status'] == 'Rejected') $statusClass = 'status-rejected';
                                ?>
                                <span class="status-badge <?= $statusClass; ?>">
                                    <?= htmlspecialchars($row['application_status']); ?>
                                </span>
                            </td>

                            <!-- FORM: REMARKS + STATUS -->
                            <td>
                                <div class="batch-update-fields" style="display: none;">
                                    <input type="hidden" class="application-id" value="<?= $row['application_id']; ?>">
                                    <textarea name="remarks" rows="2" class="remarks-small batch-remarks" placeholder="Enter remarks..."></textarea>
                                    <select name="application_status" class="batch-status">
                                        <option value="Pending">Pending</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="original-form-fields">
                                    <span style="color: #666; font-size: 0.85em;">Use checkbox + Confirm to batch update</span>
                                </div>
                            </td>
                            
                            <!-- Action: View Sensitive Information -->
                            <td>
                                <a href="sensitive_information.php?search_name=<?= urlencode($row['first_name'] . ' ' . $row['last_name']); ?>" 
                                   class="btn-view-sensitive" 
                                   title="View Sensitive Information"
                                   target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 30px;">
                            No student applications found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        © 2026 Cagayan De Oro National High School - Senior High School  
        <br>
        School Management System
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="loading-modal">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>Processing... Please wait.</p>
            <span class="loading-subtext">Sending notifications and updating records.</span>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="success-modal">
        <div class="success-content">
            <div class="success-icon">✓</div>
            <p id="successMessage">Operation completed successfully!</p>
            <button type="button" class="btn btn-success" onclick="closeSuccessModal()">OK</button>
        </div>
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/profile_dropdown_function.js"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/application_list_function.js"></script>
</body>
</html>
