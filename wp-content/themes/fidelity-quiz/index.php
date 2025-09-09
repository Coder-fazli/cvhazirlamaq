<?php
/**
 * The main template file for Fidelity Quiz theme
 */

// Check if we're on the quiz-start page and redirect to correct template
if (is_page('quiz-start') || (isset($_GET['pagename']) && $_GET['pagename'] == 'quiz-start') || 
    strpos($_SERVER['REQUEST_URI'], '/quiz-start') !== false) {
    include(get_template_directory() . '/page-quiz-start.php');
    return;
}

get_header(); ?>

<div class="quiz-container">
    <div class="quiz-gradient">
        <div id="quiz-app" class="landing-screen fade-in <?php echo fidelity_quiz_should_show_gender_selection() ? 'hidden' : ''; ?>">
            <!-- Settings Button -->
            <button class="settings-button" onclick="showSettings()">&#x2699;&#xFE0F;</button>

            <!-- Hero Image -->
            <div class="hero-image-container">
                <?php 
                $hero_image = get_template_directory_uri() . '/assets/home-page.jpg';
                if (file_exists(get_template_directory() . '/assets/home-page.jpg')): ?>
                    <img src="<?php echo $hero_image; ?>" alt="Daily Quiz" class="hero-image">
                <?php else: ?>
                    <div class="hero-image" style="background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 60px;">&#x1F495;</div>
                <?php endif; ?>
            </div>
            
            <!-- Main Content -->
            <h1 class="quiz-title">Daily Quiz</h1>
            
            <p class="quiz-subtitle">
                Answer and discover insights about your relationship
            </p>

            <!-- Feature List -->
            <div class="feature-list">
                <div class="feature">
                    <span class="feature-icon">&#x2728;</span>
                    <span class="feature-text">50 carefully crafted questions</span>
                </div>
                <div class="feature">
                    <span class="feature-icon">&#x1F4CA;</span>
                    <span class="feature-text">Detailed risk assessment</span>
                </div>
                <div class="feature">
                    <span class="feature-icon">&#x1F4A1;</span>
                    <span class="feature-text">Personalized insights & advice</span>
                </div>
            </div>

            <!-- Login/Start Button -->
            <div id="auth-section">
                <!-- Login Screen (Default) -->
                <div id="login-screen" class="login-screen">
                    <div class="login-options">
                        <button class="login-button google" onclick="loginWithGoogle()">
                            <span style="font-size: 20px;">🔍</span>
                            Login with Google
                        </button>
                        <button class="login-button email" onclick="showEmailLogin()">
                            <span style="font-size: 20px;">✉️</span>
                            Login with Email
                        </button>
                    </div>
                    
                    <!-- Email Login Form -->
                    <form id="email-login-form" class="login-form">
                        <input type="email" class="login-input" placeholder="Enter your Gmail" required>
                        <input type="password" class="login-input" placeholder="Enter your password" required>
                        <button type="submit" class="login-button email">Sign In</button>
                    </form>
                </div>
                
                <!-- Post-Login Screen (Hidden initially) -->
                <div id="post-login-screen" style="display: none;">
                    <div class="post-login-hero">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/hero-rounded.jpg" 
                             alt="Quiz Hero" class="hero-image-rounded"
                             onerror="this.style.display='none'; document.querySelector('.post-login-hero').innerHTML='<div style=&quot;width:200px;height:200px;background:linear-gradient(135deg,#FF719A,#FF8FA3);border-radius:25px;margin:0 auto 30px;display:flex;align-items:center;justify-content:center;font-size:60px;color:white;&quot;>💕</div>';">
                    </div>
                    <button class="start-button" onclick="startQuiz()">Start Quiz</button>
                    
                    <!-- Additional Actions -->
                    <div class="secondary-actions" style="margin-top: 15px;">
                        <button class="secondary-button" onclick="showHistory()" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 25px; cursor: pointer; margin-right: 10px;">
                            View History
                        </button>
                    </div>
                </div>
            </div>

            <!-- Disclaimer -->
            <p class="disclaimer">
                This quiz is for informational purposes only and should not replace professional relationship counseling.
            </p>
        </div>

        <!-- Gender Selection Screen (Hidden initially) -->
        <div id="gender-selection" class="gender-selection <?php echo fidelity_quiz_should_show_gender_selection() ? '' : 'hidden'; ?>">
            <button class="back-button" onclick="goToLanding()">← Back</button>
            
            <div class="gender-content">
                <h2 class="gender-title">Who is this test for?</h2>
                <p class="gender-subtitle">This helps us personalize your insights and recommendations</p>
                
                <div class="gender-cards">
                    <div class="gender-card" id="male-card" onclick="selectGenderCard('male')">
                        <div class="card-icon">
                            <span class="gender-emoji">&#x1F468;</span>
                        </div>
                        <span class="gender-text">Male</span>
                        <div class="selection-indicator">
                            <div class="selected-dot" id="male-dot"></div>
                        </div>
                    </div>

                    <div class="gender-card" id="female-card" onclick="selectGenderCard('female')">
                        <div class="card-icon">
                            <span class="gender-emoji">&#x1F469;</span>
                        </div>
                        <span class="gender-text">Female</span>
                        <div class="selection-indicator">
                            <div class="selected-dot" id="female-dot"></div>
                        </div>
                    </div>
                </div>

                <button class="continue-button disabled" id="continue-button" onclick="proceedWithSelectedGender()">Continue</button>
            </div>
        </div>

        <!-- NEW Beautiful Question Screen with Our Design -->
        <div id="question-screen" class="quiz-container-new hidden">
            <!-- Progress Section -->
            <div class="progress-section">
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                        <div class="progress-heart-indicator" id="progress-heart">♥</div>
                        <div class="floating-hearts">
                            <span class="mini-heart">♥</span>
                            <span class="mini-heart">♥</span>
                            <span class="mini-heart">♥</span>
                            <span class="mini-heart">♥</span>
                            <span class="mini-heart">♥</span>
                            <span class="mini-heart">♥</span>
                            <span class="mini-heart">♥</span>
                            <span class="mini-heart">♥</span>
                            <span class="mini-heart">♥</span>
                        </div>
                    </div>
                </div>
                <div class="progress-label" id="progress-text">1 of 50 completed</div>
            </div>

            <!-- Question Text -->
            <div class="question-text" id="question-text">
                Loading question...
            </div>

            <!-- Image Placeholder -->
            <div class="image-placeholder" id="question-image-container">
                <div class="placeholder-content" id="image-placeholder">
                    <span class="placeholder-icon">🖼️</span>
                    <span class="placeholder-text">IMAGE</span>
                </div>
                <img id="question-image" alt="Question image" style="display: none;">
            </div>

            <!-- Answer List -->
            <div class="answer-list answer-list-spaced" id="answer-options">
                <!-- Answer buttons will be populated by JavaScript -->
            </div>

        </div>

        <!-- Paywall Screen (Hidden initially) -->
        <div id="paywall-screen" class="paywall-screen hidden">
            <h2 class="paywall-title">Unlock Your Complete Results</h2>
            
            <div class="premium-features">
                <div class="premium-feature">
                    <span class="feature-check">✓</span>
                    <span class="premium-feature-text">Detailed topic breakdown</span>
                </div>
                <div class="premium-feature">
                    <span class="feature-check">✓</span>
                    <span class="premium-feature-text">Personalized advice & tips</span>
                </div>
                <div class="premium-feature">
                    <span class="feature-check">✓</span>
                    <span class="premium-feature-text">Relationship insights</span>
                </div>
                <div class="premium-feature">
                    <span class="feature-check">✓</span>
                    <span class="premium-feature-text">Risk assessment analysis</span>
                </div>
            </div>

            <button class="upgrade-button" onclick="upgradeToPremium()">Get Full Results - $9.99</button>
            <button class="continue-free" onclick="viewLiteResults()">Continue with Basic Results</button>
        </div>

        <!-- Lite Results Screen (Hidden initially) -->
        <div id="lite-results" class="result-screen hidden">
            <h2 class="result-title">Your Daily Score</h2>
            
            <div class="score-card">
                <div class="score-number" id="overall-score">--</div>
                <div class="score-label">Overall Score</div>
                <div class="risk-level" id="risk-level">Calculating...</div>
            </div>

            <button class="upgrade-button" onclick="showPaywall()">Get Detailed Analysis</button>
            <button class="continue-free" onclick="restartQuiz()">Take Quiz Again</button>
        </div>

        <!-- Full Results Screen (Hidden initially) -->
        <div id="full-results" class="result-screen hidden">
            <h2 class="result-title">Complete Analysis</h2>
            
            <div class="score-card">
                <div class="score-number" id="full-overall-score">--</div>
                <div class="score-label">Overall Score</div>
                <div class="risk-level" id="full-risk-level">Calculating...</div>
            </div>

            <div id="topic-breakdown" class="premium-features">
                <!-- Topic breakdown will be populated by JavaScript -->
            </div>

            <button class="continue-free" onclick="restartQuiz()">Take Quiz Again</button>
        </div>

        <!-- Settings Screen (Hidden initially) -->
        <div id="settings-screen" class="result-screen hidden">
            <h2 class="result-title">Settings</h2>
            
            <div class="premium-features">
                <div class="premium-feature">
                    <span class="premium-feature-text">Version 1.0</span>
                </div>
                <div class="premium-feature">
                    <span class="premium-feature-text">Made with WordPress</span>
                </div>
            </div>

            <button class="continue-free" onclick="goToLanding()">Back to Home</button>
        </div>
    </div>
    
    <!-- Footer Navigation -->
    <nav class="footer-nav">
        <div class="footer-nav-content">
            <a href="#" class="footer-nav-item active" onclick="navigateTo('home')">
                <div class="footer-nav-icon">🏠</div>
                <div class="footer-nav-label">Home</div>
            </a>
            <a href="#" class="footer-nav-item" onclick="navigateTo('test')">
                <div class="footer-nav-icon">🧪</div>
                <div class="footer-nav-label">Test</div>
            </a>
            <a href="#" class="footer-nav-item" onclick="navigateTo('results')">
                <div class="footer-nav-icon">📊</div>
                <div class="footer-nav-label">Results</div>
            </a>
            <a href="#" class="footer-nav-item" onclick="navigateTo('profile')">
                <div class="footer-nav-icon">👤</div>
                <div class="footer-nav-label">Profile</div>
            </a>
        </div>
    </nav>
</div>

<script>
// Integration functions for our new quiz design
let selectedGender = null;

// Start quiz function
function startQuiz() {
    if (selectedGender) {
        QuizUI.initializeQuiz(selectedGender);
    } else {
        // If no gender selected, show gender selection
        document.getElementById('quiz-app').classList.add('hidden');
        document.getElementById('gender-selection').classList.remove('hidden');
    }
}

// Gender selection functions
function selectGenderCard(gender) {
    selectedGender = gender;
    
    // Update UI
    document.querySelectorAll('.gender-card').forEach(card => {
        card.classList.remove('selected');
    });
    document.getElementById(gender + '-card').classList.add('selected');
    
    // Show selected dot
    document.querySelectorAll('.selected-dot').forEach(dot => {
        dot.style.opacity = '0';
    });
    document.getElementById(gender + '-dot').style.opacity = '1';
    
    // Enable continue button
    const continueButton = document.getElementById('continue-button');
    continueButton.classList.remove('disabled');
}

function proceedWithSelectedGender() {
    if (selectedGender) {
        QuizUI.initializeQuiz(selectedGender);
    }
}

// Result screen functions
function viewLiteResults() {
    QuizUI.viewLiteResults();
}

function upgradeToPremium() {
    QuizUI.viewFullResults();
}

function showPaywall() {
    document.getElementById('lite-results').classList.add('hidden');
    document.getElementById('paywall-screen').classList.remove('hidden');
}

function restartQuiz() {
    QuizUI.restartQuiz();
}

// Navigation functions
function goToLanding() {
    selectedGender = null;
    document.getElementById('gender-selection').classList.add('hidden');
    document.getElementById('quiz-app').classList.remove('hidden');
}

function showSettings() {
    document.getElementById('quiz-app').classList.add('hidden');
    document.getElementById('settings-screen').classList.remove('hidden');
}

function showHistory() {
    // Could implement history viewing here
    console.log('History:', FidelityQuizLogic.getHistory());
}

// Login functions (placeholder implementations)
function loginWithGoogle() {
    // Show post-login screen
    document.getElementById('login-screen').style.display = 'none';
    document.getElementById('post-login-screen').style.display = 'block';
}

function showEmailLogin() {
    document.getElementById('email-login-form').style.display = 'block';
}

// Footer navigation
function navigateTo(section) {
    console.log('Navigate to:', section);
    // Could implement navigation here
}
</script>

<?php get_footer(); ?>