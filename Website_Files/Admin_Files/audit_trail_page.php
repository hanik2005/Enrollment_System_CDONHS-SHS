<?php
include "../../Back_End_Files/PHP_Files/admin_audit_trail_backend.php";
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Audit Trail - CDONHS-SHS</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
    <link rel="stylesheet" href="../../Design/admin/audit_trail_design.css">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
</head>
<body>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Audit Trail'); ?>
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
        <h1>Audit Trail Logs</h1>
    </div>

    <div class="nav-links">
        <a href="home.php">Back to Dashboard</a>
    </div>

    <div class="filter-section">
        <form method="GET" class="filter-form" id="auditFilterForm">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="action_type">Action:</label>
                    <select name="action_type" id="action_type">
                        <option value="">All</option>
                        <?php foreach ($actionTypes as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $filterAction === $type ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="entity_type">Entity:</label>
                    <select name="entity_type" id="entity_type">
                        <option value="">All</option>
                        <?php foreach ($entityTypes as $entity): ?>
                            <option value="<?php echo htmlspecialchars($entity); ?>" <?php echo $filterEntity === $entity ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($entity); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date_from">Date From:</label>
                    <input type="date" name="date_from" id="date_from" value="<?php echo htmlspecialchars($filterDateFrom); ?>">
                </div>

                <div class="filter-group">
                    <label for="date_to">Date To:</label>
                    <input type="date" name="date_to" id="date_to" value="<?php echo htmlspecialchars($filterDateTo); ?>">
                </div>

                <div class="filter-group">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($searchText); ?>" placeholder="Description, entity id, username">
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn btn-filter">Apply</button>
                    <button type="button" class="btn btn-reset" onclick="resetAuditFilters()">Reset</button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Time</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>Description</th>
                    <th>IP</th>
                    <th>Metadata</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($auditRows)): ?>
                    <?php foreach ($auditRows as $row): ?>
                        <tr>
                            <td><?php echo (int) $row['audit_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>#<?php echo (int) $row['user_id']; ?> (<?php echo htmlspecialchars($row['username']); ?>)</td>
                            <td><span class="audit-action"><?php echo htmlspecialchars($row['action_type']); ?></span></td>
                            <td><?php echo htmlspecialchars(($row['entity_type'] ?: 'N/A') . ' #' . ($row['entity_id'] ?: '-')); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['ip_address'] ?: 'N/A'); ?></td>
                            <td>
                                <?php if (!empty($row['metadata'])): ?>
                                    <details>
                                        <summary>View</summary>
                                        <pre><?php echo htmlspecialchars($row['metadata']); ?></pre>
                                    </details>
                                <?php else: ?>
                                    <span class="meta-empty">None</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">No audit logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/audit_trail_function.js"></script>
</body>
</html>
