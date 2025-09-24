<?php
include 'includes/config.php';
include 'includes/header.php';

// Fetch profile data
$profile_stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $profile_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch skills
$skills_stmt = $pdo->query("SELECT * FROM skills ORDER BY category, skill_level DESC");
$skills = $skills_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch projects
$projects_stmt = $pdo->query("SELECT * FROM projects ORDER BY featured DESC, project_date DESC");
$projects = $projects_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch experiences
$experiences_stmt = $pdo->query("SELECT * FROM experiences ORDER BY start_date DESC");
$experiences = $experiences_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch education
$education_stmt = $pdo->query("SELECT * FROM education ORDER BY start_date DESC");
$education = $education_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Home Section -->
<section id="home" class="home-section">
    <div class="container">
        <div class="home-content">
            <div class="home-text">
                <h1><?php echo htmlspecialchars($profile['full_name'] ?? 'Your Name'); ?></h1>
                <h2><?php echo htmlspecialchars($profile['title'] ?? 'Web Developer'); ?></h2>
                <p><?php echo htmlspecialchars($profile['bio'] ?? 'A passionate developer creating amazing web experiences.'); ?></p>
                <div class="home-buttons">
                    <a href="#contact" class="btn btn-primary">Get In Touch</a>
                    <?php if($profile['resume_file']): ?>
                        <a href="assets/uploads/<?php echo $profile['resume_file']; ?>" class="btn btn-secondary" download>Download CV</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="home-image">
                <?php if($profile['profile_image']): ?>
                    <img src="assets/uploads/<?php echo $profile['profile_image']; ?>" alt="<?php echo htmlspecialchars($profile['full_name']); ?>">
                <?php else: ?>
                    <div class="placeholder-image">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about-section">
    <div class="container">
        <h2 class="section-title">About Me</h2>
        <div class="about-content">
            <div class="about-text">
                <p><?php echo nl2br(htmlspecialchars($profile['bio'] ?? 'Add your bio here through the admin panel.')); ?></p>
                <div class="about-info">
                    <div class="info-item">
                        <strong>Email:</strong>
                        <span><?php echo htmlspecialchars($profile['email'] ?? 'your.email@example.com'); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Phone:</strong>
                        <span><?php echo htmlspecialchars($profile['phone'] ?? '+1234567890'); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Location:</strong>
                        <span><?php echo htmlspecialchars($profile['location'] ?? 'Your City, Country'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section id="skills" class="skills-section">
    <div class="container">
        <h2 class="section-title">Skills</h2>
        <div class="skills-container">
            <?php
            $categories = [];
            foreach($skills as $skill) {
                $categories[$skill['category']][] = $skill;
            }
            
            foreach($categories as $category => $categorySkills): ?>
            <div class="skill-category">
                <h3><?php echo htmlspecialchars($category); ?></h3>
                <div class="skills-list">
                    <?php foreach($categorySkills as $skill): ?>
                    <div class="skill-item">
                        <div class="skill-info">
                            <span class="skill-name"><?php echo htmlspecialchars($skill['skill_name']); ?></span>
                            <span class="skill-percentage"><?php echo $skill['skill_level']; ?>%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-progress" style="width: <?php echo $skill['skill_level']; ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Projects Section -->
<section id="projects" class="projects-section">
    <div class="container">
        <h2 class="section-title">Projects</h2>
        <div class="projects-grid">
            <?php foreach($projects as $project): ?>
            <div class="project-card">
                <div class="project-image">
                    <?php if($project['image_url']): ?>
                        <img src="assets/uploads/<?php echo $project['image_url']; ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                    <?php else: ?>
                        <div class="project-placeholder">
                            <i class="fas fa-code"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="project-content">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p><?php echo htmlspecialchars($project['description']); ?></p>
                    <div class="project-technologies">
                        <?php
                        $techs = explode(',', $project['technologies']);
                        foreach($techs as $tech): ?>
                            <span class="tech-tag"><?php echo trim($tech); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="project-links">
                        <?php if($project['project_url']): ?>
                            <a href="<?php echo $project['project_url']; ?>" target="_blank" class="project-link">
                                <i class="fas fa-external-link-alt"></i> Live Demo
                            </a>
                        <?php endif; ?>
                        <?php if($project['github_url']): ?>
                            <a href="<?php echo $project['github_url']; ?>" target="_blank" class="project-link">
                                <i class="fab fa-github"></i> GitHub
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Experience Section -->
<section id="experience" class="experience-section">
    <div class="container">
        <h2 class="section-title">Experience</h2>
        <div class="timeline">
            <?php foreach($experiences as $exp): ?>
            <div class="timeline-item">
                <div class="timeline-date">
                    <?php echo date('M Y', strtotime($exp['start_date'])); ?> - 
                    <?php echo $exp['current_job'] ? 'Present' : date('M Y', strtotime($exp['end_date'])); ?>
                </div>
                <div class="timeline-content">
                    <h3><?php echo htmlspecialchars($exp['job_title']); ?></h3>
                    <h4><?php echo htmlspecialchars($exp['company']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Education Section -->
<section id="education" class="education-section">
    <div class="container">
        <h2 class="section-title">Education</h2>
        <div class="timeline">
            <?php foreach($education as $edu): ?>
            <div class="timeline-item">
                <div class="timeline-date">
                    <?php echo date('M Y', strtotime($edu['start_date'])); ?> - 
                    <?php echo $edu['current_study'] ? 'Present' : date('M Y', strtotime($edu['end_date'])); ?>
                </div>
                <div class="timeline-content">
                    <h3><?php echo htmlspecialchars($edu['degree']); ?></h3>
                    <h4><?php echo htmlspecialchars($edu['institution']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact-section">
    <div class="container">
        <h2 class="section-title">Get In Touch</h2>
        <div class="contact-content">
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p><?php echo htmlspecialchars($profile['email'] ?? 'your.email@example.com'); ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Phone</h3>
                        <p><?php echo htmlspecialchars($profile['phone'] ?? '+1234567890'); ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Location</h3>
                        <p><?php echo htmlspecialchars($profile['location'] ?? 'Your City, Country'); ?></p>
                    </div>
                </div>
            </div>
            <div class="contact-form">
                <form action="process/contact_process.php" method="POST">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="subject" placeholder="Subject" required>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>