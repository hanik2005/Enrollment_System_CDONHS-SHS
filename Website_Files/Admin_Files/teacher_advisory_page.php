<?php
include "../../Back_End_Files/PHP_Files/teacher_advisory_backend.php";
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Advisory Management</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/admin/teacher_advisory.css">
</head>
<body <?php echo renderThemeBodyAttributes(); ?>>
    <!-- header -->
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONSHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Teacher Advisory'); ?>
        <div class="right">
            <button class="legacy-menu-trigger" type="button">
                <img src="../../Assets/admin_profile.png">
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

    <!-- Back Button -->
    <div class="back-button-container">
        <a href="home.php" class="back-button">← Back to Home</a>
    </div>

    <div class="advisory-container">
        <div class="advisory-box">
            <h2>Teacher Advisory Management</h2>

            <!-- Message Alert -->
            <?php if (!empty($message)): ?>
                <div class="message <?= $message_type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- ================= ASSIGN ADVISER FORM ================= -->
            <div class="form-section">
                <h3>Assign Teacher as Adviser</h3>
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="school_year">School Year:</label>
                            <select name="school_year" id="school_year" required>
                                <option value="2025-2026" selected>2025-2026</option>
                                <option value="2026-2027">2026-2027</option>
                                <option value="2027-2028">2027-2028</option>
                                <option value="2028-2029">2028-2029</option>
                                <option value="2029-2030">2029-2030</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="grade_level">Grade Level:</label>
                            <select name="grade_level" id="grade_level" required onchange="updateSections()">
                                <option value="">Select Grade Level</option>
                                <option value="11">Grade 11</option>
                                <option value="12">Grade 12</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="strand_id">Strand:</label>
                            <select name="strand_id" id="strand_id" required onchange="updateSections()">
                                <option value="">Select Strand</option>
                                <?php foreach ($strands as $s): ?>
                                    <option value="<?= $s['strand_id'] ?>">
                                        <?= htmlspecialchars($s['strand_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="section_id">Section:</label>
                            <select name="section_id" id="section_id" required disabled>
                                <option value="">Select Section</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="teacher_id">Select Teacher (Adviser):</label>
                            <select name="teacher_id" id="teacher_id" required>
                                <option value="">Select Teacher</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= $t['teacher_id'] ?>">
                                        <?= htmlspecialchars($t['last_name'] . ', ' . $t['first_name'] . ' ' . ($t['middle_name'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" name="assign_adviser" class="assign-btn">Assign Adviser</button>
                    </div>
                </form>
            </div>

            <!-- ================= CURRENT ADVISORIES TABLE ================= -->
            <div class="table-section">
                <h3>Current Advisory Assignments</h3>
                <div class="table-container">
                    <?php if (count($advisories) > 0): ?>
                        <table class="advisory-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Grade Level</th>
                                    <th>Strand</th>
                                    <th>Section</th>
                                    <th>Adviser</th>
                                    <th>School Year</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($advisories as $adv): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>Grade <?= htmlspecialchars($adv['grade_level']) ?></td>
                                        <td><?= htmlspecialchars($adv['strand_name']) ?></td>
                                        <td><?= htmlspecialchars($adv['section_name']) ?></td>
                                        <td><?= htmlspecialchars($adv['last_name'] . ', ' . $adv['first_name']) ?></td>
                                        <td><?= htmlspecialchars($adv['school_year']) ?></td>
                                        <td>
                                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this adviser assignment?');">
                                                <input type="hidden" name="advisory_id" value="<?= $adv['advisory_id'] ?>">
                                                <button type="submit" name="delete_advisory" class="delete-btn">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="no-results">No advisory assignments found. Use the form above to assign teachers as advisers.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script>
        // Available sections from PHP (organized by grade_level and strand_id)
        const sections = <?php echo json_encode($sections); ?>;
        
        function updateSections() {
            const gradeLevel = document.getElementById('grade_level').value;
            const strandId = document.getElementById('strand_id').value;
            const sectionSelect = document.getElementById('section_id');
            
            // Clear current options
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            
            if (!gradeLevel || !strandId) {
                sectionSelect.disabled = true;
                return;
            }
            
            // Filter sections based on selected grade level and strand
            const filteredSections = sections.filter(function(sec) {
                return sec.grade_level == gradeLevel && sec.strand_id == strandId;
            });
            
            // Add filtered sections as options
            filteredSections.forEach(function(sec) {
                const option = document.createElement('option');
                option.value = sec.section_id;
                option.textContent = sec.section_name;
                sectionSelect.appendChild(option);
            });
            
            sectionSelect.disabled = false;
        }
    </script>
</body>
</html>
