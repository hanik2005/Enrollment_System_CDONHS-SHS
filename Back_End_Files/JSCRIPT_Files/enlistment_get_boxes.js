document.addEventListener("DOMContentLoaded", () => {

    const trackSelect    = document.getElementById("track");
    const gradeSelect   = document.getElementById("grade_level");
    const sectionSelect = document.getElementById("section");
    const subjectsContainer = document.getElementById("subjects-container");

    if (!subjectsContainer) {
        console.error("subjects-container not found in HTML");
        return;
    }

    // Tracks are hardcoded: 1 = Academic, 2 = TechPro
    // No need to fetch from backend
    const tracks = [
        { track_id: 1, track_name: 'Academic' },
        { track_id: 2, track_name: 'TechPro' }
    ];
    
    tracks.forEach(track => {
        trackSelect.innerHTML += 
            `<option value="${track.track_id}">${track.track_name}</option>`;
    });

    /* LOAD SECTIONS + SUBJECTS BY TRACK */
    function loadSectionsAndSubjects() {

        if (!subjectsContainer) return;

        const grade = gradeSelect.value;
        const track = trackSelect.value;

        sectionSelect.innerHTML = `<option value="">Select Section</option>`;
        subjectsContainer.innerHTML = "";

        if (grade && track) {

            /* Load Sections by Track */
            fetch(`/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/get_sections_by_track.php?grade_level=${grade}`)
                .then(res => res.json())
                .then(data => {
                    if (Array.isArray(data)) {
                        data.forEach(section => {
                            sectionSelect.innerHTML += 
                                `<option value="${section.section_id}">${section.section_name} (${section.student_count} students)</option>`;
                        });
                    }
                })
                .catch(err => console.error("Error loading sections:", err));

            /* Load Subjects by Track (New Curriculum) */
            fetch(`/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/get_subjects_by_track.php?grade_level=${grade}&track_id=${track}`)
    .then(res => res.json())
    .then(data => {

        // Check success
        if (!data.success) {
            subjectsContainer.innerHTML = `
                <tr>
                    <td colspan="2">${data.message}</td>
                </tr>
            `;
            return;
        }

        const subjects = data.subjects; // <-- use this array

        if (!Array.isArray(subjects) || subjects.length === 0) {
            subjectsContainer.innerHTML = `
                <tr>
                    <td colspan="2">No subjects available for this track and grade level.</td>
                </tr>
            `;
            return;
        }

        subjectsContainer.innerHTML = "";

        // Group subjects by cluster
        let currentCluster = '';
        
        subjects.forEach(sub => {
            // Show cluster header if it changes
            if (sub.cluster_name !== currentCluster) {
                currentCluster = sub.cluster_name;
                subjectsContainer.innerHTML += `
                    <tr class="cluster-header">
                        <td colspan="2"><strong>${currentCluster} ${sub.subject_type === 'CORE' ? '(Core Subjects)' : '(Electives)'}</strong></td>
                    </tr>
                `;
            }
            
            let checked = sub.enrolled ? "checked" : "";
            let requiredBadge = sub.is_required ? '<span class="required-badge">Required</span>' : '';
            let ncBadge = sub.nc_equivalent ? `<span class="nc-badge">${sub.nc_equivalent}</span>` : '';
            
            subjectsContainer.innerHTML += `
                <tr>
                    <td>${sub.subject_name} ${requiredBadge} ${ncBadge}</td>
                    <td>
                        <input type="checkbox" 
                               name="subjects[]" 
                               value="${sub.subject_id}" 
                               ${checked}>
                    </td>
                </tr>
            `;
        });

    })
    .catch(err => {
        subjectsContainer.innerHTML = `
            <tr>
                <td colspan="2">Error loading subjects.</td>
            </tr>
        `;
        console.error(err);
    });
        }
    }

    gradeSelect.addEventListener("change", loadSectionsAndSubjects);
    trackSelect.addEventListener("change", loadSectionsAndSubjects);

});


/* ========================= */
/* FORM SUBMIT */
/* ========================= */
document.getElementById("enlistment-form")
.addEventListener("submit", function(e) {

    e.preventDefault();

    const grade_level = document.getElementById("grade_level").value;
    const track_id    = document.getElementById("track").value;
    const section_id  = document.getElementById("section").value;
    
    // Get ALL subject checkboxes (both checked and unchecked)
    const allSubjectCheckboxes = document.querySelectorAll('input[name="subjects[]"]');
    const checkedSubjects = document.querySelectorAll('input[name="subjects[]"]:checked');

    if (!grade_level || !track_id || !section_id) {
        alert("Please select grade level, track, and section.");
        return;
    }

    if (checkedSubjects.length === 0) {
        alert("Please select at least one subject.");
        return;
    }

    // Build array with all subjects and their checked state
    const subjects = Array.from(allSubjectCheckboxes).map(cb => ({
        subject_id: cb.value,
        requested: cb.checked ? 1 : 0
    }));

    fetch("/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/save_enlistment.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            grade_level,
            track_id,
            section_id,
            subjects
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Enlistment successfully saved! Status is now pending for admin approval.");
            window.location.href = 
            "/Enrollment_System_CDONHS-SHS/Website_Files/Student_Files/home.php";
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(err => console.error(err));

});
