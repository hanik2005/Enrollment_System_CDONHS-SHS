<?php
include "../../Back_End_Files/PHP_Files/document_compliance_backend.php";
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Compliance Tracking - CDONHS-SHS</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
    <link rel="stylesheet" href="../../Design/admin/document_compliance_design.css">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
</head>
<body <?php echo renderThemeBodyAttributes(); ?>>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Document Compliance'); ?>
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
        <h1>Document Compliance Tracking</h1>
    </div>

    <div class="nav-links">
        <a href="home.php">Back to Dashboard</a>
    </div>

    <div class="note-box">
        Track PSA Birth Certificate, Form 137, Form 138, and Student ID Copy to detect missing requirements.
    </div>

    <div class="compliance-summary">
        <div class="summary-card">
            <div class="summary-label">Records Shown</div>
            <div class="summary-value"><?php echo (int) $summary['total_records']; ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Compliant</div>
            <div class="summary-value"><?php echo (int) $summary['compliant_records']; ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-label">With Missing Docs</div>
            <div class="summary-value"><?php echo (int) $summary['missing_records']; ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Missing Birth Certificate</div>
            <div class="summary-value"><?php echo (int) $summary['missing_psa_birth_certificate']; ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Missing Form 138</div>
            <div class="summary-value"><?php echo (int) $summary['missing_form_138']; ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Missing Form 137</div>
            <div class="summary-value">
                <?php echo $summary['has_form_137_column'] ? (int) $summary['missing_form_137'] : 0; ?>
            </div>
        </div>
        <div class="summary-card summary-card-centered">
            <div class="summary-label">Missing Student ID Copy</div>
            <div class="summary-value"><?php echo (int) $summary['missing_student_id_copy']; ?></div>
        </div>
    </div>

    <div class="filter-section">
        <form method="GET" class="filter-form" id="documentComplianceFilterForm">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search_name">Search Student:</label>
                    <input type="text" id="search_name" name="search_name" placeholder="Enter student name..." value="<?php echo htmlspecialchars($search_name); ?>">
                </div>

                <div class="filter-group">
                    <label for="application_status">Application Status:</label>
                    <select id="application_status" name="application_status">
                        <option value="">All</option>
                        <option value="Pending" <?php echo $filter_application_status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo $filter_application_status === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Conditionally Approved" <?php echo $filter_application_status === 'Conditionally Approved' ? 'selected' : ''; ?>>Conditionally Approved</option>
                        <option value="Rejected" <?php echo $filter_application_status === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="grade_level">Grade Level:</label>
                    <select id="grade_level" name="grade_level">
                        <option value="">All</option>
                        <option value="11" <?php echo $filter_grade_level === '11' ? 'selected' : ''; ?>>11</option>
                        <option value="12" <?php echo $filter_grade_level === '12' ? 'selected' : ''; ?>>12</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="strand_id">Strand:</label>
                    <select id="strand_id" name="strand_id">
                        <option value="">All</option>
                        <?php foreach ($strands as $strand): ?>
                            <option value="<?php echo (int) $strand['strand_id']; ?>" <?php echo $filter_strand_id == $strand['strand_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($strand['strand_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="compliance_status">Compliance:</label>
                    <select id="compliance_status" name="compliance_status">
                        <option value="">All</option>
                        <option value="Compliant" <?php echo $filter_compliance === 'Compliant' ? 'selected' : ''; ?>>Compliant</option>
                        <option value="Missing" <?php echo $filter_compliance === 'Missing' ? 'selected' : ''; ?>>Missing Documents</option>
                    </select>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn btn-filter">Search</button>
                    <button type="button" class="btn btn-reset" onclick="resetDocumentComplianceFilters()">Reset</button>
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
                    <th>Grade</th>
                    <th>Strand</th>
                    <th>Section</th>
                    <th>Birth Certificate</th>
                    <th>Form 138</th>
                    <th>Form 137</th>
                    <th>Student ID Copy</th>
                    <th>Compliance</th>
                    <th>Missing Alerts</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($complianceRows)): ?>
                    <?php $rowNo = 1; ?>
                    <?php foreach ($complianceRows as $row): ?>
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
                            <td><?php echo $rowNo++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($fullName); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['grade_level'] ? 'Grade ' . $row['grade_level'] : 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['strand_name'] ?? 'Unassigned'); ?></td>
                            <td><?php echo htmlspecialchars($row['section_name'] ?? 'Unassigned'); ?></td>
                            <td>
                                <span class="status-pill <?php echo $row['psa_birth_certificate_status'] === 'Submitted' ? 'status-submitted' : 'status-missing'; ?>">
                                    <?php echo htmlspecialchars($row['psa_birth_certificate_status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-pill <?php echo $row['form_138_status'] === 'Submitted' ? 'status-submitted' : 'status-missing'; ?>">
                                    <?php echo htmlspecialchars($row['form_138_status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $form137Class = 'status-config';
                                if ($row['form_137_status'] === 'Submitted') {
                                    $form137Class = 'status-submitted';
                                } elseif ($row['form_137_status'] === 'Missing') {
                                    $form137Class = 'status-missing';
                                }
                                ?>
                                <span class="status-pill <?php echo $form137Class; ?>">
                                    <?php echo htmlspecialchars($row['form_137_status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-pill <?php echo $row['student_id_copy_status'] === 'Submitted' ? 'status-submitted' : 'status-missing'; ?>">
                                    <?php echo htmlspecialchars($row['student_id_copy_status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-pill <?php echo $row['compliance_status'] === 'Compliant' ? 'compliance-ok' : 'compliance-alert'; ?>">
                                    <?php echo htmlspecialchars($row['compliance_status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['missing_docs_text'] === 'None'): ?>
                                    <span class="info-muted">None</span>
                                <?php else: ?>
                                    <span class="missing-alert-text"><?php echo htmlspecialchars($row['missing_docs_text']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="document_correction_page.php?application_id=<?php echo (int) $row['application_id']; ?>" class="btn btn-filter btn-small" target="_blank">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" style="text-align: center; padding: 20px;">
                            No records found for the selected filters.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/document_compliance_function.js"></script>
</body>
</html>
