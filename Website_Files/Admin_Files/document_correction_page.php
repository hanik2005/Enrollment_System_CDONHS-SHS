<?php
include "../../Back_End_Files/PHP_Files/document_correction_backend.php";
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Correction Manager - CDONHS-SHS</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
    <link rel="stylesheet" href="../../Design/admin/document_correction_design.css">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
</head>
<body <?php echo renderThemeBodyAttributes(); ?>>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Document Correction'); ?>
        <div class="right">
            <button class="home-menu-toggle" type="button" data-profile-src="../../Assets/admin_profile.png" data-profile-alt="Admin profile">
                <span class="menu-icon" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="menu-label">Menu</span>
            </button>
            <div class="legacy-nav-links">
                <?php foreach ($navLinks as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['href']); ?>"<?php echo isset($link['class']) ? ' class="' . htmlspecialchars($link['class']) . '"' : ''; ?>>
                        <?php echo htmlspecialchars($link['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="page-title">
        <h1>Document Correction Requests</h1>
    </div>

    <div class="nav-links">
        <a href="home.php">Back to Dashboard</a>
    </div>

    <?php if (!empty($flashMessage)): ?>
        <div class="doc-alert <?php echo $flashMessage['type'] === 'success' ? 'doc-alert-success' : 'doc-alert-error'; ?>">
            <?php echo htmlspecialchars($flashMessage['message']); ?>
        </div>
    <?php endif; ?>

    <div class="filter-section">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search_name">Search Student:</label>
                    <input type="text" id="search_name" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>" placeholder="Enter student name...">
                </div>

                <div class="filter-group">
                    <label for="filter_status">Application Status:</label>
                    <select id="filter_status" name="filter_status">
                        <option value="">All</option>
                        <option value="Pending" <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo $filter_status === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Conditionally Approved" <?php echo $filter_status === 'Conditionally Approved' ? 'selected' : ''; ?>>Conditionally Approved</option>
                        <option value="Rejected" <?php echo $filter_status === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn btn-filter">Search</button>
                    <a href="document_correction_page.php" class="btn btn-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Student</th>
                    <th>Application Status</th>
                    <th>PSA Birth Certificate</th>
                    <th>Form 138</th>
                    <th>Student ID Copy</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($documentRows)): ?>
                    <?php $index = 1; ?>
                    <?php foreach ($documentRows as $row): ?>
                        <?php
                        $fullName = trim($row['last_name'] . ', ' . $row['first_name']);
                        if (!empty($row['middle_name'])) {
                            $fullName .= ' ' . substr($row['middle_name'], 0, 1) . '.';
                        }
                        if (!empty($row['extension_name'])) {
                            $fullName .= ' ' . $row['extension_name'];
                        }
                        ?>
                        <tr>
                            <td><?php echo $index++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($fullName); ?></strong><br>
                                <span class="student-email"><?php echo htmlspecialchars($row['email'] ?: 'No email'); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($row['application_status']); ?></td>
                            <td><?php echo renderDocumentActionCell($row, 'psa_birth_certificate', 'PSA Birth Certificate'); ?></td>
                            <td><?php echo renderDocumentActionCell($row, 'form_138', 'Form 138'); ?></td>
                            <td><?php echo renderDocumentActionCell($row, 'student_id_copy', 'Student ID Copy'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No student documents found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="emailModal" class="doc-modal">
        <div class="doc-modal-content">
            <h3>Send Document Correction Email</h3>
            <p id="emailModalTarget"></p>
            <form method="POST">
                <input type="hidden" name="action" value="send_correction_email">
                <input type="hidden" name="application_id" id="email_application_id">
                <input type="hidden" name="document_field" id="email_document_field">
                <div class="doc-form-group">
                    <label for="reason">Reason for correction:</label>
                    <textarea name="reason" id="reason" rows="4" required placeholder="Explain what is wrong with the submitted document..."></textarea>
                </div>
                <div class="doc-modal-actions">
                    <button type="submit" class="btn btn-filter">Send Email</button>
                    <button type="button" class="btn btn-reset" onclick="closeEmailModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="doc-modal">
        <div class="doc-modal-content">
            <h3>Delete Submitted Document</h3>
            <p id="deleteModalTarget"></p>
            <form method="POST">
                <input type="hidden" name="action" value="delete_document">
                <input type="hidden" name="application_id" id="delete_application_id">
                <input type="hidden" name="document_field" id="delete_document_field">
                <div class="doc-modal-actions">
                    <button type="submit" class="btn btn-delete">Delete Document</button>
                    <button type="button" class="btn btn-reset" onclick="closeDeleteModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/document_correction_function.js"></script>
</body>
</html>

<?php
function renderDocumentActionCell(array $row, string $field, string $label): string
{
    $applicationId = (int) $row['application_id'];
    $fileName = trim($row[$field] ?? '');
    $studentName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));

    if ($fileName === '') {
        return '<span class="doc-missing-badge">Missing</span>';
    }

    $safeStudent = htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8');
    $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
    $safeField = htmlspecialchars($field, ENT_QUOTES, 'UTF-8');
    $viewLink = '../../uploads/Documents/student/' . rawurlencode($fileName);

    return '
        <div class="doc-cell-actions">
            <a class="doc-link-inline" href="' . $viewLink . '" target="_blank">View</a>
            <button type="button" class="btn btn-filter btn-mini"
                onclick="openEmailModal(' . $applicationId . ', \'' . $safeField . '\', \'' . $safeLabel . '\', \'' . $safeStudent . '\')">
                Email Correction
            </button>
            <button type="button" class="btn btn-delete btn-mini"
                onclick="openDeleteModal(' . $applicationId . ', \'' . $safeField . '\', \'' . $safeLabel . '\', \'' . $safeStudent . '\')">
                Delete
            </button>
        </div>
    ';
}
?>
