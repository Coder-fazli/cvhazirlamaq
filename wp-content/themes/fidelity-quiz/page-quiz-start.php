<?php
/**
 * Template Name: Quiz Start Page
 * Post-Login Quiz Start Page
 */

// Debug: Confirm this template is loading
echo '<script>console.log("PAGE-QUIZ-START.PHP TEMPLATE IS LOADING!");</script>';

get_header(); ?>

<div class="quiz-container">
    <div class="quiz-gradient">
        <!-- Quiz Start Screen -->
        <div id="quiz-start-screen" class="quiz-start-screen">
            <!-- Hero Illustration -->
            <div class="quiz-start-hero">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/quiz-illustration.jpg" 
                     alt="Quiz Illustration" 
                     class="quiz-start-illustration"
                     loading="lazy">
            </div>
            
            <!-- Welcome Message -->
            <div class="quiz-start-content">
                <h1 class="quiz-start-title">Ready to Begin?</h1>
                <p class="quiz-start-subtitle">
                    Take our comprehensive relationship assessment and get personalized insights
                </p>
                
                <!-- Start Quiz Button -->
                <button class="start-button" onclick="beginQuiz()">Start Quiz</button>
                
                <!-- Quick Stats -->
                <div class="quiz-stats">
                    <div class="stat-item">
                        <span class="stat-number">50</span>
                        <span class="stat-label">Questions</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">~10</span>
                        <span class="stat-label">Minutes</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">✨</span>
                        <span class="stat-label">Insights</span>
                    </div>
                </div>
                
                <!-- Additional Actions -->
                <div class="secondary-actions" style="margin-top: 25px;">
                    <button class="secondary-button" onclick="showHistory()" 
                            style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; margin-right: 10px;">
                        View History
                    </button>
                    <button class="secondary-button" onclick="showProfileModal()" 
                            style="background: #9b59b6; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer;">
                        Profile
                    </button>
                </div>
            </div>
            
            <!-- Back to Home Link -->
            <div class="back-link" style="margin-top: 30px;">
                <a href="<?php echo home_url(); ?>" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px;">
                    ← Back to Home
                </a>
            </div>
        </div>
    </div>
    
    <!-- Footer Navigation -->
    <nav class="footer-nav">
        <div class="footer-nav-content">
            <a href="<?php echo home_url(); ?>" class="footer-nav-item">
                <div class="footer-nav-icon">🏠</div>
                <div class="footer-nav-label">Home</div>
            </a>
            <a href="#" class="footer-nav-item active" onclick="beginQuiz()">
                <div class="footer-nav-icon">🧪</div>
                <div class="footer-nav-label">Test</div>
            </a>
            <a href="#" class="footer-nav-item" onclick="showHistory()">
                <div class="footer-nav-icon">📊</div>
                <div class="footer-nav-label">Results</div>
            </a>
            <a href="#" class="footer-nav-item" onclick="showProfileModal()">
                <div class="footer-nav-icon">👤</div>
                <div class="footer-nav-label">Profile</div>
            </a>
        </div>
    </nav>
</div>

<?php get_footer(); ?>

<script>
console.log('PAGE-QUIZ-START INLINE SCRIPT LOADING');

// Quiz Start Page Specific JavaScript
// beginQuiz function is defined in footer.php globally

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    const authData = localStorage.getItem('quiz_auth');
    if (!authData || !JSON.parse(authData).isLoggedIn) {
        // Redirect to login if not authenticated
        window.location.href = '<?php echo home_url(); ?>';
        return;
    }
    
    // Animate illustration on load
    const illustration = document.querySelector('.quiz-start-illustration');
    if (illustration) {
        illustration.style.opacity = '0';
        illustration.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            illustration.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            illustration.style.opacity = '1';
            illustration.style.transform = 'translateY(0)';
        }, 200);
    }
    
    // Animate content
    const content = document.querySelector('.quiz-start-content');
    if (content) {
        content.style.opacity = '0';
        content.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            content.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            content.style.opacity = '1';
            content.style.transform = 'translateY(0)';
        }, 400);
    }
});
</script>