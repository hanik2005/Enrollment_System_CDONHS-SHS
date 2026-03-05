<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";

/* Check if Student Progress Page is enabled */
include_once "../../Back_End_Files/PHP_Files/check_activation.php";
if (!isFeatureEnabled('Student Progress Page')) {
    header("Location: ../access_denied.php?feature=Student Progress Page");
    exit;
}

/* VERIFY TEACHER SESSION */
$stmt = $connection->prepare("
    SELECT u.*, t.first_name, t.last_name, t.middle_name, t.extension_name
    FROM users u
    INNER JOIN teachers t ON t.user_id = u.user_id
    WHERE u.user_id = ? AND u.role_id = 3
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Set profile image path (using default since teachers table doesn't have profile_image)
$profileImagePath = "../../Assets/profile_button.png";

include "../../Back_End_Files/PHP_Files/student_progress_backend.php";
include "../../Back_End_Files/PHP_Files/student_promotion_backend.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/profile_dropdown.css">
    <link rel="stylesheet" href="../../Design/teacher/student_progress_design.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
    <title>Student Progress - CDONHS-SHS</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <style>
        /* Promotion Status Styles */
        .promotion-column {
            min-width: 180px;
        }

        .promotion-select {
            padding: 6px 10px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            background: #ffffff;
            min-width: 140px;
        }

        .promotion-select:focus {
            outline: none;
            border-color: #1e3a8a;
        }

        .promotion-select.promote { background: #d1fae5; border-color: #059669; }
        .promotion-select.graduate { background: #dbeafe; border-color: #2563eb; }
        .promotion-select.retain { background: #fee2e2; border-color: #dc2626; }
        .promotion-select.pending { background: #fef3c7; border-color: #d97706; }

        .remarks-input {
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 12px;
            width: 150px;
        }

        .save-btn {
            padding: 6px 12px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .save-btn:hover {
            background: #2563eb;
        }

        .saved-badge {
            display: inline-block;
            padding: 4px 8px;
            background: #059669;
            color: white;
            border-radius: 4px;
            font-size: 11px;
        }

        /* Bulk action styles */
        .bulk-action-container {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .bulk-action-container label {
            font-weight: bold;
            color: #1e3a8a;
        }

        .bulk-action-container select {
            padding: 8px 12px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .bulk-action-container input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            width: 200px;
        }

        .confirm-btn {
            padding: 10px 20px;
            background: #059669;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .confirm-btn:hover {
            background: #047857;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <div class="center">
            Student Progress | Advisory: <?php echo htmlspecialchars($advisoryText); ?>
        </div>
        <div class="right">
            <button class="profile-btn" type="button">
                <img src="<?php echo $profileImagePath; ?>">
            </button>
            <div class="profile-dropdown">
                <a href="home.php">Home</a>
                <a href="profile_page.php">View Profile</a>
                <a href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="back-button-container">
        <a href="home.php" class="back-button">← Back to Home</a>
    </div>

    <!-- Main Content -->
    <div class="progress-container">
        <!-- Messages -->
        <?php if (!empty($message)): ?>
            <?php if ($message_type === 'success'): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showSuccessModal('<?php echo addslashes($message); ?>');
                });
            </script>
            <?php else: ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Table Header -->
        <div class="table-header">
            <h2>Student Promotion Status</h2>
            <p class="subtitle">Select promotion status for your advisory students</p>
            <?php if (!empty($advisoryText)): ?>
                <p class="advisory-info">Advisory: <?php echo htmlspecialchars($advisoryText); ?></p>
            <?php endif; ?>
        </div>

        <?php if (empty($progressData)): ?>
            <div class="no-results">
                <p>No students found in your advisory section.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <form method="POST" id="promotionForm">
                
                <!-- Bulk Action Bar -->
                <div class="bulk-action-container">
                    <label>Bulk Action:</label>
                    <select name="bulk_status" id="bulkStatus">
                        <option value="Pending">Pending</option>
                        <?php if ($advisoryGradeLevel == 11): ?>
                        <option value="Promote to Grade 12">Promote to Grade 12</option>
                        <?php endif; ?>
                        <?php if ($advisoryGradeLevel == 12): ?>
                        <option value="Graduate">Graduate</option>
                        <?php endif; ?>
                        <option value="Retained">Retained</option>
                    </select>
                    <input type="text" name="bulk_remarks" id="bulkRemarks" placeholder="Add remarks for all...">
                    <button type="submit" name="bulk_update_promotion" class="confirm-btn">✓ Confirm Selected</button>
                </div>

                <table class="progress-table" id="progressTable">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">
                                <input type="checkbox" id="selectAll" title="Select All">
                            </th>
                            <th>No</th>
                            <th>Student Name</th>
                            <th>Grade Level</th>
                            <th>Strand</th>
                            <th>Section</th>
                            <th class="promotion-column">Promotion Status</th>
                            <th>Teacher Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        
                        // Get promotion status for all students
                        $promotion_statuses = [];
                        
                        $currentMonth = date('n');
                        $currentYear = date('Y');
                        if ($currentMonth >= 6) {
                            $school_year = $currentYear . '-' . ($currentYear + 1);
                        } else {
                            $school_year = ($currentYear - 1) . '-' . $currentYear;
                        }
                        
                        // Simple query without complex binding
                        $promoQuery = $connection->query("
                            SELECT student_id, recommended_status, teacher_remarks, is_approved
                            FROM student_promotion_status 
                            WHERE school_year = '$school_year'
                        ");
                        
                        if ($promoQuery) {
                            while ($promo = $promoQuery->fetch_assoc()) {
                                $promotion_statuses[$promo['student_id']] = $promo;
                            }
                        }
                        
                        foreach ($progressData as $student): 
                            $fullName = $student['last_name'] . ', ' . $student['first_name'];
                            if (!empty($student['middle_name'])) {
                                $fullName .= ' ' . substr($student['middle_name'], 0, 1) . '.';
                            }
                            if (!empty($student['extension_name'])) {
                                $fullName .= ' ' . $student['extension_name'];
                            }

                            // Get promotion status
                            $promoStatus = $promotion_statuses[$student['student_id']]['recommended_status'] ?? 'Pending';
                            $promoRemarks = $promotion_statuses[$student['student_id']]['teacher_remarks'] ?? '';
                            $isApproved = $promotion_statuses[$student['student_id']]['is_approved'] ?? 0;
                            
                            $promoClass = strtolower(str_replace(' ', '-', str_replace(' to ', '-', $promoStatus)));
                            
                            // Determine available options based on teacher's advisory grade level
                            $showPromoteOption = ($advisoryGradeLevel == 11);
                            $showGraduateOption = ($advisoryGradeLevel == 12);
                        ?>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="selected_students[]" 
                                           value="<?php echo $student['student_id']; ?>" 
                                           class="student-checkbox">
                                </td>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($fullName); ?></td>
                                <td>Grade <?php echo htmlspecialchars($student['grade_level']); ?></td>
                                <td><?php echo htmlspecialchars($student['strand_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['section_name']); ?></td>
                                <td>
                                    <input type="hidden" name="students[<?php echo $student['student_id']; ?>][student_id]" value="<?php echo $student['student_id']; ?>">
                                    <input type="hidden" name="students[<?php echo $student['student_id']; ?>][current_grade_level]" value="<?php echo $student['grade_level']; ?>">
                                    <select name="students[<?php echo $student['student_id']; ?>][recommended_status]" class="promotion-select <?php echo $promoClass; ?>">
                                        <option value="Pending" <?php echo $promoStatus === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <?php if ($showPromoteOption): ?>
                                        <option value="Promote to Grade 12" <?php echo $promoStatus === 'Promote to Grade 12' ? 'selected' : ''; ?>>Promote to Grade 12</option>
                                        <?php endif; ?>
                                        <?php if ($showGraduateOption): ?>
                                        <option value="Graduate" <?php echo $promoStatus === 'Graduate' ? 'selected' : ''; ?>>Graduate</option>
                                        <?php endif; ?>
                                        <option value="Retained" <?php echo $promoStatus === 'Retained' ? 'selected' : ''; ?>>Retained</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="students[<?php echo $student['student_id']; ?>][teacher_remarks]" class="remarks-input" 
                                           value="<?php echo htmlspecialchars($promoRemarks); ?>" 
                                           placeholder="Add remarks...">
                                </td>
                                <td>
                                    <?php if ($isApproved): ?>
                                        <span class="saved-badge">Approved</span>
                                    <?php else: ?>
                                        <span style="color: #6b7280; font-size: 12px;">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Save All Button -->
                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" name="save_all_students" class="save-btn" style="padding: 12px 24px; font-size: 14px;">
                        💾 Save All Changes
                    </button>
                </div>
                </form>
            </div>
        <?php endif; ?>
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
    <script>
        // Select All checkbox functionality
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Add color coding to select dropdowns based on selection
        document.querySelectorAll('.promotion-select').forEach(select => {
            select.addEventListener('change', function() {
                // Remove all status classes
                this.classList.remove('promote', 'graduate', 'retain', 'pending');
                
                // Add appropriate class
                const value = this.value.toLowerCase().replace(' to ', '-').replace(' ', '-');
                if (value.includes('promote')) {
                    this.classList.add('promote');
                } else if (value.includes('graduate')) {
                    this.classList.add('graduate');
                } else if (value.includes('retain')) {
                    this.classList.add('retain');
                } else {
                    this.classList.add('pending');
                }
            });
            
            // Initialize class on load
            select.dispatchEvent(new Event('change'));
        });
        
        // ==============================
        // SUCCESS MODAL FUNCTIONS
        // ==============================
        function showSuccessModal(message) {
            const successModal = document.getElementById("successModal");
            const successMessage = document.getElementById("successMessage");
            
            if (successMessage) {
                successMessage.textContent = message;
            }
            
            if (successModal) {
                successModal.classList.add("active");
            }
        }

        function closeSuccessModal(shouldReload = false) {
            const successModal = document.getElementById('successModal');
            if (successModal) {
                successModal.classList.remove('active');
            }
            // Only reload if explicitly requested
            if (shouldReload === true) {
                location.reload();
            }
        }
        
        // Make function globally accessible
        window.closeSuccessModal = closeSuccessModal;
        
        // Close modal when clicking outside
        window.addEventListener("click", function(event) {
            const successModal = document.getElementById("successModal");
            if (successModal && event.target === successModal) {
                closeSuccessModal();
            }
        });
        
        // Handle form submission with loading modal
        const promotionForm = document.getElementById('promotionForm');
        if (promotionForm) {
            promotionForm.addEventListener('submit', function(e) {
                // Check if submitting Save All or Bulk Update
                const submitBtn = e.submitter;
                const isBulkUpdate = submitBtn && submitBtn.name === 'bulk_update_promotion';
                
                if (isBulkUpdate) {
                    // For bulk update, check if students are selected
                    const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
                    if (selectedCheckboxes.length === 0) {
                        alert('Please select at least one student.');
                        e.preventDefault();
                        return;
                    }
                    
                    const bulkStatus = document.getElementById('bulkStatus').value;
                    if (bulkStatus === 'Pending') {
                        alert('Please select a valid status (not Pending).');
                        e.preventDefault();
                        return;
                    }
                    
                    // Show loading modal
                    const loadingModal = document.getElementById('loadingModal');
                    if (loadingModal) {
                        loadingModal.classList.add('active');
                    }
                }
                // For Save All, let the form submit normally (no AJAX)
            });
        }
    </script>
</body>
</html>
