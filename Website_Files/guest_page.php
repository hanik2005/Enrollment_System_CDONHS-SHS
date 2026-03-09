<?php
$schoolAddress = '2nd 3rd St, Cagayan De Oro City, 9000 Misamis Oriental';
$encodedSchoolAddress = urlencode($schoolAddress);
$googleMapsApiKey = getenv('GOOGLE_MAPS_API_KEY') ?: '';
$schoolMapUrl = $googleMapsApiKey !== ''
    ? "https://www.google.com/maps/embed/v1/place?key={$googleMapsApiKey}&q={$encodedSchoolAddress}"
    : "https://www.google.com/maps?q={$encodedSchoolAddress}&z=17&output=embed";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CDONHS-SHS | Guest Page</title>
    <link rel="icon" href="../Assets/LOGO.png" type="image/jpg">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Design/guest_design.css">
</head>
<body>

<!-- ===== HEADER ===== -->
<div class="header">

    <div class="left">
        <img src="../Assets/LOGO.png" alt="CDONHS Logo">
        <span>CDONHS-SHS</span>
    </div>

    <div class="right">
        <a href="login.php" class="login-btn">Login</a>
    </div>

</div>
<!-- ===== HERO SECTION ===== -->
<div class="hero-section">
<div class="hero-overlay">
    <h1 class="drop-title">
        Welcome to Cagayan De Oro National High School Senior High
    </h1>
   
    <p class="drop-text">
        Online Enrollment For Students.
    </p>

    <div class="hero-buttons drop-btn">
        <a href="Student_Online_Form.php" class="primary-btn">Student Enrollment</a>
    </div>
</div>
</div>


<!-- ===== ABOUT SECTION ===== -->
<div class="about-section scroll-hidden" id="about">

    <h2>About the School</h2>
    <p>
        Cagayan De Oro National High School - Senior High School 
        provides quality education that prepares learners for higher education, 
        employment, and entrepreneurship. The institution is committed to 
        excellence, integrity, and service.
    </p>

</div>

<!-- ===== VISION MISSION SECTION ===== -->
<div class="vision-mission">

    <div class="vm-box scroll-hidden" id="vision">
        <h2>Vision</h2>
        <p>
            We dream of Filipinos who passionately love their country 
            and whose values and competencies enable them to realize 
            their full potential and contribute meaningfully to building the nation.
        </p>
    </div>

    <div class="vm-box scroll-hidden" id="mission">
        <h2>Mission</h2>
        <p>
            To protect and promote the right of every Filipino to quality, 
            equitable, culture-based, and complete basic education where:
            <br><br>
            Students learn in a child-friendly, gender-sensitive, 
            safe, and motivating environment.
            <br>
            Teachers facilitate learning and constantly nurture every learner.
            <br>
            Administrators and staff ensure an enabling and supportive environment.
        </p>
    </div>

</div>

<!-- ===== LOCATION SECTION ===== -->
<section class="location-section scroll-hidden" id="location">
    <div class="location-copy">
        <span class="location-label">School Address</span>
        <h2>Find CDONHS - Senior High on Google Maps</h2>
        <p>
            <?php echo htmlspecialchars($schoolAddress, ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <a
            href="https://www.google.com/maps/search/?api=1&query=<?php echo $encodedSchoolAddress; ?>"
            class="secondary-btn"
            target="_blank"
            rel="noopener noreferrer"
        >
            Open in Google Maps
        </a>
    </div>

    <div class="location-map">
        <iframe
            src="<?php echo htmlspecialchars($schoolMapUrl, ENT_QUOTES, 'UTF-8'); ?>"
            title="Map showing CDONHS - Senior High"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
    </div>
</section>




<!-- ===== FOOTER ===== -->
<footer class="footer">

    <div class="footer-content">

        <div class="footer-school">
            <img src="../Assets/LOGO.png" alt="School Logo">
            <h3>CDONHS - Senior High</h3>
            <p>Empowering students through quality education.</p>
        </div>

        <div class="footer-links">
            <h4>Quick Links</h4>
            <a href="#about">About</a>
            <a href="#vision">Vision</a>
            <a href="#mission">Mission</a>
            <a href="#location">Location</a>
            <a href="login.php">Login</a>
        </div>

        <div class="footer-contact">
            <h4>Contact</h4>
            <p><?php echo htmlspecialchars($schoolAddress, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Email: cdonhsshsacc@gmail.com</p>
            <p>Phone: +63 900 000 000</p>
        </div>

    </div>

    <div class="footer-bottom">
        © 2026 Cagayan De Oro National High School - Senior High School
    </div>

</footer>

<script>

const observer = new IntersectionObserver((entries) => {

    entries.forEach(entry => {

        if(entry.isIntersecting){
            entry.target.classList.add("scroll-show");
        }

    });

});

const hiddenElements = document.querySelectorAll(".scroll-hidden");

hiddenElements.forEach(el => observer.observe(el));

</script>

</body>
</html>
