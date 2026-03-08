// Global variables
let currentSections = [];
let currentStrands = [];
let currentTracks = [];

document.addEventListener("DOMContentLoaded", () => {

    const trackSelect   = document.getElementById("track");
    const strandSelect   = document.getElementById("strand");
    const gradeSelect   = document.getElementById("grade_level");
    const sectionSelect = document.getElementById("section");
    const summaryGrade = document.getElementById("summary-grade");
    const summaryTrack = document.getElementById("summary-track");
    const summaryStrand = document.getElementById("summary-strand");
    const summarySection = document.getElementById("summary-section");

    /* LOAD STRANDS */
    function loadStrands() {
        fetch(`/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/get_strands.php`)
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    currentStrands = data;

                    currentTracks = [...new Set(data.map(strand => strand.track_name).filter(Boolean))];
                    trackSelect.innerHTML = `<option value="">Select Track</option>`;
                    currentTracks.forEach(track => {
                        trackSelect.innerHTML += `<option value="${track}">${track}</option>`;
                    });

                    strandSelect.innerHTML = `<option value="">Select Track First</option>`;
                }
            })
            .catch(err => console.error("Error loading strands:", err));
    }

    loadStrands();

    function loadStrandsByTrack() {
        const selectedTrack = trackSelect.value;

        strandSelect.innerHTML = `<option value="">Select Strand</option>`;
        sectionSelect.innerHTML = `<option value="">Select Section</option>`;
        currentSections = [];

        if (!selectedTrack) {
            strandSelect.innerHTML = `<option value="">Select Track First</option>`;
            updateSummary();
            return;
        }

        const filtered = currentStrands.filter(s => s.track_name === selectedTrack);
        filtered.forEach(strand => {
            strandSelect.innerHTML +=
                `<option value="${strand.strand_id}">${strand.strand_name}</option>`;
        });

        updateSummary();
    }

    /* LOAD SECTIONS BY STRAND AND GRADE LEVEL */
    function loadSections() {

        const grade = gradeSelect.value;
        const strand = strandSelect.value;

        sectionSelect.innerHTML = `<option value="">Select Section</option>`;
        
        // Reset current sections
        currentSections = [];
        
        // Update summary display
        updateSummary();

        if (grade && strand) {

            /* Load Sections by Strand - using get_sections.php which uses strand_id */
            fetch(`/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/get_sections.php?grade_level=${grade}&strand_id=${strand}`)
                .then(res => res.json())
                .then(data => {
                    if (Array.isArray(data)) {
                        currentSections = data;
                        data.forEach(section => {
                            const studentCount = section.student_count || 0;
                            sectionSelect.innerHTML += 
                                `<option value="${section.section_id}">${section.section_name} (${studentCount} students)</option>`;
                        });
                    }
                })
                .catch(err => console.error("Error loading sections:", err));
        }
    }

    function updateSummary() {
        const grade = gradeSelect.value;
        const track = trackSelect.value;
        const strand = strandSelect.value;
        const section = sectionSelect.value;

        // Get strand name
        let strandName = 'Not selected';
        if (strand) {
            const strandObj = currentStrands.find(s => s.strand_id == strand);
            strandName = strandObj ? strandObj.strand_name : 'Not selected';
        }

        // Get section name
        let sectionName = 'Not selected';
        if (section) {
            const sectionObj = currentSections.find(s => s.section_id == section);
            sectionName = sectionObj ? sectionObj.section_name : 'Not selected';
        }

        // Update summary elements
        summaryGrade.textContent = grade ? grade : 'Not selected';
        summaryTrack.textContent = track ? track : 'Not selected';
        summaryStrand.textContent = strandName;
        summarySection.textContent = sectionName;
    }

    // Event listeners
    gradeSelect.addEventListener("change", () => {
        loadSections();
        updateSummary();
    });

    trackSelect.addEventListener("change", () => {
        loadStrandsByTrack();
        loadSections();
        updateSummary();
    });

    strandSelect.addEventListener("change", () => {
        loadSections();
        updateSummary();
    });

    sectionSelect.addEventListener("change", updateSummary);

});


/* ========================= */
/* FORM SUBMIT */
/* ========================= */
document.getElementById("enlistment-form")
.addEventListener("submit", function(e) {

    e.preventDefault();

    const grade_level = document.getElementById("grade_level").value;
    const track_name = document.getElementById("track").value;
    const strand_id   = document.getElementById("strand").value;
    const section_id  = document.getElementById("section").value;

    if (!grade_level || !track_name || !strand_id || !section_id) {
        alert("Please select grade level, track, strand, and section.");
        return;
    }

    // Submit grade_level, strand_id, and section_id only
    fetch("/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/save_enlistment.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            grade_level: grade_level,
            strand_id: strand_id,
            section_id: section_id
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
