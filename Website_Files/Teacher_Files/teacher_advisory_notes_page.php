<?php
include "../../Back_End_Files/PHP_Files/teacher_advisory_notes_backend.php";
include "../../Back_End_Files/PHP_Files/portal_ui_helper.php";

function escapeNoteDataAttr(string $value): string
{
    $safe = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    return str_replace(["\r\n", "\r", "\n"], '&#10;', $safe);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Advisory Notes - CDONHS-SHS</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/teacher/teacher_advisory_notes_design.css">
</head>
<body>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderPortalHeaderBanner('Teacher Portal', 'Advisory Notes', 'Advisory: ' . $advisoryText); ?>
        <div class="right">
            <button class="legacy-menu-trigger" type="button">
                <img src="<?php echo $profileImagePath; ?>" alt="Teacher Profile">
            </button>
            <div class="legacy-nav-links">
                <a href="home.php">Home</a>
                <a href="enrollment_summary_page.php">Enrollment Summary</a>
                <a href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="back-button-container">
        <a href="home.php" class="back-button">&larr; Back to Home</a>
    </div>

    <div class="notes-page">
        <div class="notes-title-card">
            <h1>Private Advisory Notes</h1>
            <p>Use this for behavior observations, follow-ups, and interventions per student.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="notes-alert <?php echo $messageType === 'success' ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="notes-stats">
            <div class="stats-card">
                <div class="stats-label">Total Students</div>
                <div class="stats-value"><?php echo (int) $notesStats['total_students']; ?></div>
            </div>
            <div class="stats-card">
                <div class="stats-label">With Existing Notes</div>
                <div class="stats-value"><?php echo (int) $notesStats['students_with_notes']; ?></div>
            </div>
        </div>

        <div class="notes-toolbar">
            <input type="text" id="notesSearch" placeholder="Search by student name, LRN, grade or status...">
        </div>

        <?php if (empty($advisorySectionId)): ?>
            <div class="notes-empty">
                No advisory section is assigned to your account yet.
            </div>
        <?php elseif (empty($notesRows)): ?>
            <div class="notes-empty">
                No students found in your advisory section.
            </div>
        <?php else: ?>
            <div class="notes-table-wrap">
                <table class="notes-table" id="notesTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Student Name</th>
                            <th>LRN</th>
                            <th>Class</th>
                            <th>Enlistment</th>
                            <th>Last Note Update</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        <?php foreach ($notesRows as $row): ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['lrn']); ?></td>
                                <td>
                                    Grade <?php echo (int) $row['grade_level']; ?>
                                    - <?php echo htmlspecialchars($row['strand_abbreviation']); ?>
                                    <?php echo htmlspecialchars($row['section_name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['enlistment_status']); ?></td>
                                <td>
                                    <?php
                                    if (!empty($row['note_updated_at']) && $row['has_any_note']) {
                                        echo htmlspecialchars(date('M d, Y h:i A', strtotime($row['note_updated_at'])));
                                    } else {
                                        echo "No notes yet";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button
                                        type="button"
                                        class="open-note-modal <?php echo $row['has_any_note'] ? 'btn-edit' : 'btn-add'; ?>"
                                        data-student-id="<?php echo (int) $row['student_id']; ?>"
                                        data-student-name="<?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-behavior-note="<?php echo escapeNoteDataAttr($row['behavior_note']); ?>"
                                        data-follow-up-note="<?php echo escapeNoteDataAttr($row['follow_up_note']); ?>"
                                        data-intervention-note="<?php echo escapeNoteDataAttr($row['intervention_note']); ?>"
                                    >
                                        <?php echo $row['has_any_note'] ? 'Edit Notes' : 'Add Notes'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="note-modal" id="noteModal">
        <div class="note-modal-content">
            <div class="note-modal-header">
                <h2 id="noteModalTitle">Student Notes</h2>
                <button type="button" class="close-modal-btn" id="closeNoteModal">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="student_id" id="noteStudentId">
                <input type="hidden" name="save_note" value="1">

                <label for="behavior_note">Behavior Notes</label>
                <textarea name="behavior_note" id="behavior_note" rows="4" placeholder="Behavior observations..."></textarea>

                <label for="follow_up_note">Follow-up Notes</label>
                <textarea name="follow_up_note" id="follow_up_note" rows="4" placeholder="Follow-up actions..."></textarea>

                <label for="intervention_note">Intervention Notes</label>
                <textarea name="intervention_note" id="intervention_note" rows="4" placeholder="Interventions done or needed..."></textarea>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="cancelNoteModal">Cancel</button>
                    <button type="submit" class="btn-save">Save Notes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/teacher_advisory_notes_function.js"></script>
</body>
</html>
