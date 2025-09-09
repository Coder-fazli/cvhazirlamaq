    <!-- Load quiz data and logic for our beautiful new design -->
    <script src="<?php echo get_template_directory_uri(); ?>/quiz-data.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/quiz-logic.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/acceptance-tests.js"></script>
    
    <script>
        // Define REST API endpoints
        window.QUIZ_API = {
            baseUrl: '<?php echo esc_url_raw(rest_url('quiz/v1/')); ?>',
            nonce: '<?php echo wp_create_nonce('wp_rest'); ?>'
        };
        
        // Global question cache
        window.questionsCache = {};
        
        // Function to load questions from REST API
        window.loadQuestions = async function(gender) {
            console.log('🔄 Loading questions for gender:', gender);
            
            if (window.questionsCache[gender]) {
                console.log('✅ Returning cached questions for', gender, ':', window.questionsCache[gender].length, 'questions');
                return window.questionsCache[gender];
            }
            
            try {
                const response = await fetch(window.QUIZ_API.baseUrl + 'questions?gender=' + gender);
                if (!response.ok) {
                    throw new Error('Failed to load questions');
                }
                const questions = await response.json();
                console.log('📥 Raw API response:', questions.length, 'questions');
                console.log('📋 Sample raw question:', questions[0]);
                
                // Convert REST API format to frontend format
                const convertedQuestions = questions.map(q => ({
                    id: q.id,
                    text: q.text,
                    topic: q.topics.length > 0 ? q.topics[0] : 'general',
                    answers: q.options || [], // API uses 'options', frontend expects 'answers'
                    scores: q.scores || [],
                    imageUrl: q.imageUrl || '',
                    order: q.order || 0,
                    weight: q.weight || 1.0,
                    hint: q.hint || ''
                }));
                
                console.log('🔄 Converted questions:', convertedQuestions.length);
                console.log('📋 Sample converted question:', convertedQuestions[0]);
                console.log('🖼️ Questions with images:', convertedQuestions.filter(q => q.imageUrl).length, 'out of', convertedQuestions.length);
                
                // Cache the questions
                window.questionsCache[gender] = convertedQuestions;
                return convertedQuestions;
            } catch (error) {
                console.error('Error loading questions:', error);
                return [];
            }
        };
        
        // Replace hardcoded questions with API calls
        window.maleQuestions = [];
        window.femaleQuestions = [];
        
        // Initialize questions on page load
        (async function() {
            try {
                window.maleQuestions = await window.loadQuestions('male');
                window.femaleQuestions = await window.loadQuestions('female');
                console.log('Questions loaded from API:', {
                    male: window.maleQuestions.length,
                    female: window.femaleQuestions.length
                });
            } catch (error) {
                console.error('Failed to initialize questions:', error);
            }
        })();
    </script>
    <script>
        console.log('Footer script starting...');
        
        // Quiz flow constants now handled by functions.php
        
        // Define login functions FIRST - immediately available
        window.loginWithGoogle = function() {
            // Simulate Google login (in real app, would use Google OAuth)
            const authState = {
                isLoggedIn: true,
                user: {
                    name: 'User',
                    email: 'user@gmail.com',
                    avatar: null
                },
                loginMethod: 'google'
            };
            
            localStorage.setItem('quiz_auth', JSON.stringify(authState));
            
            // Redirect to dedicated quiz start page
            window.location.replace(window.location.origin + '/new-wordpress/quiz-start/');
        };
        
        window.showEmailLogin = function() {
            const form = document.getElementById('email-login-form');
            if (form) {
                form.classList.toggle('active');
            }
        };
        
        // Add logout function
        window.logout = function() {
            console.log('Logging out...');
            localStorage.removeItem('quiz_auth');
            localStorage.removeItem('quiz_session');
            localStorage.removeItem('quiz_history');
            // Redirect to home page
            window.location.href = window.location.origin + '/new-wordpress/';
        };
        
        // beginQuiz function now handled by functions.php
        
        // Clean function for internal quiz flow
        window.startQuiz = function() {
            console.log('startQuiz called');
            if (typeof quizState !== 'undefined') {
                quizState.currentStep = 'gender-selection';
                console.log('Set currentStep to gender-selection');
                if (typeof showScreen === 'function') {
                    console.log('Calling showScreen function');
                    showScreen('gender-selection');
                } else {
                    console.log('showScreen function not available, trying manual approach');
                    // Try to manually show gender selection
                    const genderSelection = document.getElementById('gender-selection');
                    const landingScreen = document.getElementById('quiz-app');
                    
                    if (genderSelection) {
                        console.log('Found gender-selection element, showing it');
                        genderSelection.classList.remove('hidden');
                        genderSelection.style.display = 'block';
                    } else {
                        console.log('gender-selection element not found');
                    }
                    
                    if (landingScreen) {
                        console.log('Found landing screen, hiding it');
                        landingScreen.classList.add('hidden');
                        landingScreen.style.display = 'none';
                    }
                }
            } else {
                console.log('quizState not available');
            }
        };
        
        console.log('All critical functions assigned to window');
        
        // Enhanced Quiz App State Management with Session Support
        let quizState = {
            currentStep: 'landing',
            userGender: null,
            selectedGender: null,
            currentQuestionIndex: 0,
            answers: [],
            questions: [],
            score: null,
            riskLevel: null,
            hasSubscription: false,
            topicScores: {},
            session: null,
            canResume: false
        };

        // Check authentication immediately (before DOM loads)
        const quickAuthCheck = localStorage.getItem('quiz_auth');
        if (quickAuthCheck) {
            try {
                const quickAuth = JSON.parse(quickAuthCheck);
                console.log('Auth check:', quickAuth, 'Current path:', window.location.pathname);
                // Only redirect if we're on the home page and user is logged in, BUT NOT if there are URL parameters
                const hasUrlParams = window.location.search !== '';
                if (quickAuth.isLoggedIn && 
                    (window.location.pathname === '/new-wordpress/' || window.location.pathname === '/new-wordpress/index.php' || window.location.pathname.endsWith('new-wordpress')) &&
                    !hasUrlParams) {
                    console.log('Redirecting to quiz-start page');
                    // Redirect immediately, don't wait for DOM
                    window.location.replace(window.location.origin + '/new-wordpress/quiz-start/');
                }
            } catch (error) {
                console.error('Quick auth check error:', error);
            }
        }

        // Diagnostic function for testing specific question
        window.testSpecificQuestion = async function() {
            console.log('🧪 Testing specific question...');
            try {
                const response = await fetch('http://localhost/new-wordpress/wp-json/quiz/v1/questions?gender=female');
                const questions = await response.json();
                
                // Find the specific question the user mentioned
                const targetQuestion = questions.find(q => q.text.includes('vurğulayır'));
                if (targetQuestion) {
                    console.log('🎯 Found target question:', {
                        id: targetQuestion.id,
                        text: targetQuestion.text,
                        imageUrl: targetQuestion.imageUrl
                    });
                    
                    // Test the image directly
                    const testImg = new Image();
                    testImg.onload = function() {
                        console.log('✅ Target question image loads successfully');
                    };
                    testImg.onerror = function() {
                        console.error('❌ Target question image failed to load');
                    };
                    testImg.src = targetQuestion.imageUrl;
                    
                    return targetQuestion;
                } else {
                    console.log('❌ Could not find target question');
                    console.log('📋 Available questions:', questions.map(q => q.text.substring(0, 30) + '...'));
                    return null;
                }
            } catch (error) {
                console.error('❌ Test failed:', error);
                return null;
            }
        };

        // Check for resumable session on load
        document.addEventListener('DOMContentLoaded', function() {
            checkForResumableSession();
            initializeAuth();
            
            // Start Quiz button event handling now managed by functions.php
            
            // URL parameter detection now handled by functions.php
        });

        // Authentication state
        let authState = {
            isLoggedIn: false,
            user: null,
            loginMethod: null
        };

        // Answer options - Dynamic (now using question.answers)

        // Screen Management Functions
        function showScreen(screenId) {
            // Hide all screens
            const screens = ['quiz-app', 'gender-selection', 'question-screen', 'paywall-screen', 'lite-results', 'full-results', 'settings-screen'];
            screens.forEach(screen => {
                const element = document.getElementById(screen);
                if (element) {
                    element.classList.add('hidden');
                }
            });

            // Show requested screen
            const targetScreen = document.getElementById(screenId);
            if (targetScreen) {
                targetScreen.classList.remove('hidden');
                targetScreen.classList.add('fade-in');
            }
        }

        // Quiz Flow Functions - already defined at top

        // Enhanced Gender Selection Functions - make globally accessible
        window.selectGenderCard = function(gender) {
            // Remove previous selections
            document.querySelectorAll('.gender-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selection to clicked card
            const selectedCard = document.getElementById(gender + '-card');
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
            
            // Store selected gender
            quizState.selectedGender = gender;
            
            // Enable continue button
            const continueButton = document.getElementById('continue-button');
            if (continueButton) {
                continueButton.classList.remove('disabled');
            }
        }

        // Make proceedWithSelectedGender globally accessible
        window.proceedWithSelectedGender = async function() {
            if (!quizState.selectedGender) return;
            
            const continueButton = document.getElementById('continue-button');
            if (continueButton) {
                continueButton.textContent = 'Loading Questions...';
                continueButton.disabled = true;
            }
            
            try {
                quizState.userGender = quizState.selectedGender;
                
                // Load questions from API
                const questions = await window.loadQuestions(quizState.userGender);
                if (questions.length === 0) {
                    throw new Error('No questions loaded from API');
                }
                
                quizState.questions = [...questions];
                console.log('Loaded', questions.length, 'questions for', quizState.userGender);
                
                // Create new session using the new logic system
                quizState.session = FidelityQuizLogic.startSession(quizState.userGender);
                quizState.currentQuestionIndex = 0;
                quizState.answers = [];
                
                // Shuffle questions for variety
                shuffleArray(quizState.questions);
                
                showQuestionScreen();
                
            } catch (error) {
                console.error('Error loading questions:', error);
                alert('Failed to load questions. Please try again.');
                
                if (continueButton) {
                    continueButton.textContent = 'Continue';
                    continueButton.disabled = false;
                }
            }
        }

        function showQuestionScreen() {
            quizState.currentStep = 'questions';
            showScreen('question-screen');
            
            // Initialize the yellow progress bar for the quiz
            quizProgressBar = new ResponsiveProgressBar('quiz-progress-container', {
                currentStep: 0,
                totalSteps: quizState.questions.length,
                label: 'Quiz Progress',
                color: '#FBBF24', // Yellow color
                height: 14
            });
            
            displayCurrentQuestion();
        }

        // Responsive Progress Bar Component Class
        class ResponsiveProgressBar {
            constructor(containerId, options = {}) {
                this.container = document.getElementById(containerId);
                this.options = {
                    currentStep: options.currentStep || 0,
                    totalSteps: options.totalSteps || 100,
                    color: options.color || '#FFFFFF',
                    height: options.height || 12,
                    showPercentage: options.showPercentage !== false,
                    showLabel: options.showLabel !== false,
                    label: options.label || 'Progress',
                    animated: options.animated !== false,
                    ...options
                };
                
                if (this.container) {
                    this.render();
                }
            }
            
            render() {
                const percentage = Math.min(100, Math.max(0, (this.options.currentStep / this.options.totalSteps) * 100));
                
                this.container.innerHTML = `
                    <div class="progress-container">
                        ${this.options.showLabel ? `
                            <div class="progress-label">${this.options.label}</div>
                        ` : ''}
                        <div class="progress-bar-wrapper" style="height: ${this.options.height}px;">
                            <div class="progress-fill" data-percentage="${percentage}" style="
                                background: linear-gradient(90deg, 
                                    ${this.options.color}90, 
                                    ${this.options.color}, 
                                    ${this.options.color}95
                                );
                                width: 0%;
                            "></div>
                            <div class="progress-heart-indicator">&#x1F498;</div>
                        </div>
                        ${this.options.showPercentage ? `
                            <div class="progress-percentage">
                                <span class="current-step">${this.options.currentStep}</span> of 
                                <span class="total-steps">${this.options.totalSteps}</span> completed
                                (<span class="percentage-value">0</span>%)
                            </div>
                        ` : ''}
                    </div>
                `;
                
                // Trigger animation after render
                setTimeout(() => this.animateToPercentage(percentage), 100);
            }
            
            animateToPercentage(percentage) {
                const progressFill = this.container.querySelector('.progress-fill');
                const percentageValue = this.container.querySelector('.percentage-value');
                const heartIndicator = this.container.querySelector('.progress-heart-indicator');
                
                if (progressFill) {
                    progressFill.style.width = percentage + '%';
                }
                
                // Move heart indicator along the progress bar
                if (heartIndicator) {
                    const progressBarWidth = this.container.querySelector('.progress-bar-wrapper').offsetWidth || 280;
                    const heartWidth = 40; // Heart indicator width
                    const heartPosition = (percentage / 100) * progressBarWidth - (heartWidth / 2);
                    heartIndicator.style.left = Math.max(-20, heartPosition) + 'px';
                }
                
                if (percentageValue) {
                    this.animateNumber(0, percentage, 800, (value) => {
                        percentageValue.textContent = Math.round(value);
                    });
                }
            }
            
            animateNumber(start, end, duration, callback) {
                const startTime = performance.now();
                const animate = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    const current = start + (end - start) * easeOut;
                    
                    callback(current);
                    
                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    }
                };
                requestAnimationFrame(animate);
            }
            
            updateProgress(currentStep, totalSteps = null) {
                this.options.currentStep = currentStep;
                if (totalSteps !== null) {
                    this.options.totalSteps = totalSteps;
                }
                
                const percentage = Math.min(100, Math.max(0, (this.options.currentStep / this.options.totalSteps) * 100));
                
                // Update text elements
                const currentStepEl = this.container.querySelector('.current-step');
                const totalStepsEl = this.container.querySelector('.total-steps');
                
                if (currentStepEl) currentStepEl.textContent = this.options.currentStep;
                if (totalStepsEl) totalStepsEl.textContent = this.options.totalSteps;
                
                // Animate to new percentage
                this.animateToPercentage(percentage);
            }
            
            setColor(color) {
                this.options.color = color;
                const progressFill = this.container.querySelector('.progress-fill');
                if (progressFill) {
                    progressFill.style.background = `linear-gradient(90deg, ${color}90, ${color}, ${color}95)`;
                }
            }
        }

        // Enhanced Progress Bar with Heart Animation (Legacy)
        function updateProgress() {
            const progressFill = document.getElementById('progress-fill');
            const heartIndicator = document.getElementById('heart-indicator');
            const currentQuestionSpan = document.getElementById('current-question');
            const totalQuestionsSpan = document.getElementById('total-questions');

            if (progressFill && heartIndicator) {
                const progress = ((quizState.currentQuestionIndex + 1) / quizState.questions.length) * 100;
                const progressBarWidth = 280; // Match CSS width
                const heartHalfWidth = 26; // Half of heart width for centering
                const heartPosition = (progress / 100) * progressBarWidth - heartHalfWidth;
                
                progressFill.style.width = progress + '%';
                heartIndicator.style.left = heartPosition + 'px';
                
                if (currentQuestionSpan) currentQuestionSpan.textContent = quizState.currentQuestionIndex + 1;
                if (totalQuestionsSpan) totalQuestionsSpan.textContent = quizState.questions.length;
                
                // Create floating hearts animation
                createFloatingHearts();
            }
        }

        function createFloatingHearts() {
            const heartsContainer = document.getElementById('floating-hearts');
            if (!heartsContainer) return;

            // Create 12-15 dense floating hearts around the progress area
            const heartCount = Math.floor(Math.random() * 4) + 12;
            const heartTypes = ['❤️', '💕', '💖', '💗', '💙', '🧡'];
            
            for (let i = 0; i < heartCount; i++) {
                const heart = document.createElement('div');
                
                // Random size classes
                const sizeClasses = ['small', '', 'medium'];
                const randomSize = sizeClasses[Math.floor(Math.random() * sizeClasses.length)];
                heart.className = `floating-heart ${randomSize}`.trim();
                
                // Use HTML entities for hearts
                const heartEntities = ['&#x2764;&#xFE0F;', '&#x1F495;', '&#x1F496;', '&#x1F497;', '&#x1F499;', '&#x1F9E1;'];
                heart.innerHTML = heartEntities[Math.floor(Math.random() * heartEntities.length)];
                
                // Random position around the entire progress area (wider spread)
                heart.style.left = (Math.random() * 320 - 35) + 'px';
                heart.style.top = (Math.random() * 60 + 10) + 'px';
                
                // Random delays for staggered animation
                heart.style.animationDelay = (Math.random() * 0.8) + 's';
                
                heartsContainer.appendChild(heart);
                
                // Remove heart after animation completes
                setTimeout(() => {
                    if (heart.parentNode) {
                        heart.parentNode.removeChild(heart);
                    }
                }, 2500 + Math.random() * 500);
            }
        }

        function displayCurrentQuestion() {
            const question = quizState.questions[quizState.currentQuestionIndex];
            const questionText = document.getElementById('question-text');
            const answerOptionsContainer = document.getElementById('answer-options');

            if (question) {
                // Update NEW progress bar with exact JSON requirements format
                const progressFill = document.getElementById('progress-fill');
                const progressText = document.getElementById('progress-text');
                
                if (progressFill && progressText) {
                    const currentStep = quizState.currentQuestionIndex + 1;
                    const totalSteps = quizState.questions.length;
                    const percentage = Math.round((currentStep / totalSteps) * 100);
                    
                    progressFill.style.width = percentage + '%';
                    progressText.textContent = `${currentStep} of ${totalSteps} completed (${percentage}%)`;
                }

                // Update question display with responsive font sizing
                questionText.textContent = question.text;
                
                // Apply responsive font sizing based on question length
                const questionLength = question.text.length;
                questionText.classList.remove('long-question', 'very-long-question');
                
                if (questionLength > 80) {
                    questionText.classList.add('very-long-question');
                    console.log('📝 Very long question detected:', questionLength, 'characters');
                } else if (questionLength > 50) {
                    questionText.classList.add('long-question');
                    console.log('📝 Long question detected:', questionLength, 'characters');
                } else {
                    console.log('📝 Normal length question:', questionLength, 'characters');
                }
                
                // Handle question image - NEW LOGIC for proper image loading
                const imageContainer = document.getElementById('question-image-container');
                const questionImage = document.getElementById('question-image');
                const imagePlaceholder = document.getElementById('image-placeholder');
                
                console.log('🖼️ Processing question image:', {
                    questionId: question.id,
                    questionText: question.text.substring(0, 50) + '...',
                    hasImageUrl: !!question.imageUrl,
                    imageUrl: question.imageUrl,
                    imageContainer: !!imageContainer,
                    questionImage: !!questionImage,
                    imagePlaceholder: !!imagePlaceholder
                });
                
                if (question.imageUrl && question.imageUrl.trim() !== '') {
                    console.log('🖼️ Loading image from admin:', question.imageUrl);
                    
                    if (questionImage && imageContainer && imagePlaceholder) {
                        // Hide placeholder and show image
                        imagePlaceholder.style.display = 'none';
                        questionImage.style.display = 'block';
                        questionImage.src = question.imageUrl;
                        
                        // Handle load/error events
                        questionImage.onload = () => {
                            console.log('✅ Image loaded successfully from admin');
                            imagePlaceholder.style.display = 'none';
                            questionImage.style.display = 'block';
                        };
                        
                        questionImage.onerror = () => {
                            console.error('❌ Image failed to load, showing placeholder');
                            questionImage.style.display = 'none';
                            imagePlaceholder.style.display = 'flex';
                        };
                    }
                } else {
                    console.log('❌ No image URL - showing heart placeholder');
                    if (questionImage && imagePlaceholder) {
                        questionImage.style.display = 'none';
                        imagePlaceholder.style.display = 'flex';
                    }
                }

                // Display answer options with NEW design - matching JSON requirements
                answerOptionsContainer.innerHTML = '';
                const answerLetters = ['A', 'B', 'C', 'D', 'E']; // Letter chips for answers
                
                question.answers.forEach((answerText, index) => {
                    const button = document.createElement('button');
                    button.className = 'answer-button-new';
                    
                    // Create the chip and text structure
                    button.innerHTML = `
                        <div class="answer-chip">${answerLetters[index]}</div>
                        <div class="answer-text-new">${answerText}</div>
                    `;
                    
                    const score = question.scores ? question.scores[index] : index;
                    button.onclick = () => selectAnswer(score, answerText, index);
                    button.setAttribute('data-answer', answerLetters[index]);
                    
                    // Add slight delay for staggered animation
                    setTimeout(() => {
                        button.style.opacity = '1';
                        button.style.transform = 'translateX(0)';
                    }, index * 100);
                    
                    button.style.opacity = '0';
                    button.style.transform = 'translateX(20px)';
                    button.style.transition = 'all 0.3s ease';
                    
                    answerOptionsContainer.appendChild(button);
                });
            }
        }

        function selectAnswer(value, option, buttonIndex) {
            const question = quizState.questions[quizState.currentQuestionIndex];
            
            // Visual feedback for NEW button design
            const buttons = document.querySelectorAll('.answer-button-new');
            buttons.forEach((btn, idx) => {
                if (idx === buttonIndex) {
                    btn.classList.add('selected');
                } else {
                    btn.classList.remove('selected');
                }
            });
            
            // Add haptic feedback if supported
            if ('vibrate' in navigator) {
                navigator.vibrate(50); // Light haptic feedback as per JSON requirements
            }

            // Save answer to session using new logic system
            if (quizState.session) {
                quizState.session = FidelityQuizLogic.saveAnswer(
                    quizState.session, 
                    question.id, 
                    value, // answer index (0, 1, or 2)
                    option, // answer text
                    question.topic
                );
            }

            // Store answer locally for compatibility
            quizState.answers.push({
                questionId: question.id,
                value: value,
                option: option,
                topic: question.topic
            });

            // Move to next question after delay
            setTimeout(() => {
                quizState.currentQuestionIndex++;
                
                if (quizState.currentQuestionIndex >= quizState.questions.length) {
                    // Quiz completed
                    finishQuiz();
                } else {
                    // Show next question
                    displayCurrentQuestion();
                }
            }, 500);
        }

        function finishQuiz() {
            // Use new logic system for scoring
            if (quizState.session) {
                const scores = FidelityQuizLogic.finishSession(quizState.session);
                quizState.scores = scores;
                quizState.score = scores.globalScore;
                quizState.riskLevel = scores.band;
                quizState.topicScores = scores.categoryScores;
            } else {
                // Fallback to old system
                calculateScore();
            }
            
            if (quizState.hasSubscription) {
                showFullResults();
            } else {
                showPaywall();
            }
        }

        function calculateScore() {
            // Updated for 3-option format (0, 1, 2)
            const totalPossibleScore = quizState.answers.length * 2; // Max value is now 2
            const actualScore = quizState.answers.reduce((sum, answer) => sum + answer.value, 0);
            
            // Convert to percentage (inverted - lower values = better fidelity)
            quizState.score = Math.max(0, 100 - Math.round((actualScore / totalPossibleScore) * 100));
            
            // Determine risk level (adjusted thresholds for 3-option format)
            if (quizState.score >= 75) {
                quizState.riskLevel = 'low';
            } else if (quizState.score >= 50) {
                quizState.riskLevel = 'medium';
            } else {
                quizState.riskLevel = 'high';
            }

            // Calculate topic scores - updated for new topics and 3-option format
            const topics = ['phone', 'behavior', 'intimacy', 'social', 'additional'];
            topics.forEach(topic => {
                const topicAnswers = quizState.answers.filter(a => a.topic === topic);
                if (topicAnswers.length > 0) {
                    const topicScore = Math.max(0, 100 - Math.round((topicAnswers.reduce((sum, a) => sum + a.value, 0) / (topicAnswers.length * 2)) * 100));
                    quizState.topicScores[topic] = topicScore;
                }
            });
        }

        function showPaywall() {
            quizState.currentStep = 'paywall';
            showScreen('paywall-screen');
        }

        function viewLiteResults() {
            quizState.currentStep = 'lite-result';
            showScreen('lite-results');
            displayLiteResults();
        }

        function displayLiteResults() {
            const scoreElement = document.getElementById('overall-score');
            const riskElement = document.getElementById('risk-level');
            
            if (scoreElement) {
                // Animate score counting up
                animateScore(scoreElement, 0, quizState.score, 1500);
            }
            if (riskElement) {
                setTimeout(() => {
                    riskElement.textContent = quizState.riskLevel.charAt(0).toUpperCase() + quizState.riskLevel.slice(1) + ' Risk';
                    riskElement.className = 'risk-level risk-' + quizState.riskLevel;
                }, 1000);
            }
        }

        function animateScore(element, start, end, duration) {
            const startTime = performance.now();
            
            function updateScore(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                const current = Math.round(start + (end - start) * progress);
                element.textContent = current;
                
                if (progress < 1) {
                    requestAnimationFrame(updateScore);
                }
            }
            
            requestAnimationFrame(updateScore);
        }

        function upgradeToPremium() {
            // Simulate premium purchase
            quizState.hasSubscription = true;
            showFullResults();
        }

        function showFullResults() {
            quizState.currentStep = 'full-result';
            showScreen('full-results');
            displayFullResults();
        }

        function displayFullResults() {
            const scoreElement = document.getElementById('full-overall-score');
            const riskElement = document.getElementById('full-risk-level');
            const topicBreakdown = document.getElementById('topic-breakdown');
            
            if (scoreElement) {
                animateScore(scoreElement, 0, quizState.score, 1500);
            }
            if (riskElement) {
                setTimeout(() => {
                    riskElement.textContent = quizState.riskLevel.charAt(0).toUpperCase() + quizState.riskLevel.slice(1) + ' Risk';
                    riskElement.className = 'risk-level risk-' + quizState.riskLevel;
                }, 1000);
            }

            // Display topic breakdown
            if (topicBreakdown) {
                let topicHTML = '<h3 style="margin-bottom: 20px; color: var(--text-dark); font-size: 18px;">Topic Breakdown</h3>';
                
                Object.keys(quizState.topicScores).forEach((topic, index) => {
                    const score = quizState.topicScores[topic];
                    setTimeout(() => {
                        topicHTML += `
                            <div class="premium-feature" style="animation: slideUp 0.3s ease-out;">
                                <span class="premium-feature-text">${topic.charAt(0).toUpperCase() + topic.slice(1)}: ${score}%</span>
                            </div>
                        `;
                        topicBreakdown.innerHTML = topicHTML;
                    }, index * 200);
                });
            }
        }

        function showSettings() {
            showScreen('settings-screen');
        }

        function goToLanding() {
            quizState.currentStep = 'landing';
            // Reset gender selection
            document.querySelectorAll('.gender-card').forEach(card => {
                card.classList.remove('selected');
            });
            const continueButton = document.getElementById('continue-button');
            if (continueButton) {
                continueButton.classList.add('disabled');
            }
            quizState.selectedGender = null;
            showScreen('quiz-app');
        }

        function restartQuiz() {
            // Reset state with session support
            quizState = {
                currentStep: 'landing',
                userGender: null,
                selectedGender: null,
                currentQuestionIndex: 0,
                answers: [],
                questions: [],
                score: null,
                riskLevel: null,
                hasSubscription: false,
                topicScores: {},
                session: null,
                canResume: false
            };
            
            goToLanding();
        }

        // Session Management Functions
        function checkForResumableSession() {
            // Check if there's a saved session in localStorage
            const sessionsData = localStorage.getItem('quiz_sessions');
            if (!sessionsData) return;
            
            try {
                const sessions = JSON.parse(sessionsData);
                const sessionIds = Object.keys(sessions);
                
                if (sessionIds.length > 0) {
                    const latestSessionId = sessionIds[sessionIds.length - 1];
                    const session = sessions[latestSessionId];
                    
                    if (session && session.progress > 0 && session.progress < 100) {
                        quizState.canResume = true;
                        showResumeOption(session);
                    }
                }
            } catch (error) {
                console.error('Error checking resumable session:', error);
            }
        }

        function showResumeOption(session) {
            // Only show resume button if user is logged in
            if (!authState.isLoggedIn) return;
            
            // Add resume button to the post-login screen
            const postLoginScreen = document.getElementById('post-login-screen');
            const startButton = postLoginScreen ? postLoginScreen.querySelector('.start-button') : null;
            
            if (startButton && !document.getElementById('resume-button')) {
                const resumeButton = document.createElement('button');
                resumeButton.id = 'resume-button';
                resumeButton.className = 'start-button resume-button';
                resumeButton.innerHTML = `Resume Quiz (${session.progress}% complete)`;
                resumeButton.onclick = () => resumeQuiz(session.id);
                
                startButton.parentNode.insertBefore(resumeButton, startButton.nextSibling);
                
                // Add some styling
                resumeButton.style.marginTop = '10px';
                resumeButton.style.backgroundColor = '#f39c12';
            }
        }

        async function resumeQuiz(sessionId) {
            const session = FidelityQuizLogic.resumeSession(sessionId);
            if (!session) {
                alert('Session could not be resumed. Starting a new quiz.');
                startQuiz();
                return;
            }

            // Restore quiz state from session
            quizState.session = session;
            quizState.userGender = session.targetGender;
            quizState.selectedGender = session.targetGender;
            
            // Load questions from API
            const questions = await window.loadQuestions(session.targetGender);
            quizState.questions = [...questions];
            quizState.answers = [...session.answers];
            quizState.currentQuestionIndex = session.answers.length;

            // Shuffle questions but maintain answered ones
            shuffleArray(quizState.questions);
            
            showQuestionScreen();
        }

        function pauseQuiz() {
            if (quizState.session) {
                FidelityQuizLogic.pauseSession(quizState.session);
                alert('Quiz paused and saved. You can resume later.');
                goToLanding();
            }
        }

        // Make showHistory globally accessible
        window.showHistory = function() {
            const history = FidelityQuizLogic.getHistory();
            if (history.length === 0) {
                alert('No quiz history found.');
                return;
            }

            let historyHtml = '<div style="max-height: 400px; overflow-y: auto; padding: 20px;">';
            historyHtml += '<h3 style="margin-bottom: 20px;">Quiz History</h3>';
            
            history.forEach((entry, index) => {
                const date = new Date(entry.date).toLocaleDateString();
                const bandColor = entry.band === 'low' ? '#2ecc71' : 
                                 entry.band === 'medium' ? '#f39c12' : '#e74c3c';
                
                historyHtml += `
                    <div style="border: 1px solid #ddd; margin-bottom: 10px; padding: 15px; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <strong>${date}</strong>
                            <span style="background: ${bandColor}; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                                ${entry.band.toUpperCase()} RISK
                            </span>
                        </div>
                        <div style="margin-top: 8px; color: #666;">
                            Score: ${entry.globalScore}% | Gender: ${entry.targetGender} | 
                            Completion: ${entry.completionRate}%
                        </div>
                        <div style="margin-top: 5px; font-size: 12px; color: #888;">
                            Top concerns: ${entry.topCategories.join(', ')}
                        </div>
                    </div>
                `;
            });
            
            historyHtml += '</div>';
            
            // Create modal for history
            showModal('Quiz History', historyHtml);
        }

        function showModal(title, content) {
            // Simple modal implementation
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.5); z-index: 10000; display: flex;
                align-items: center; justify-content: center;
            `;
            
            modal.innerHTML = `
                <div style="background: white; padding: 20px; border-radius: 10px; max-width: 90%; max-height: 90%;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h2>${title}</h2>
                        <button onclick="this.closest('.modal').remove()" style="border: none; background: #e74c3c; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer;">×</button>
                    </div>
                    ${content}
                </div>
            `;
            
            modal.className = 'modal';
            document.body.appendChild(modal);
        }

        // Authentication Functions
        function initializeAuth() {
            // Check if user was previously logged in
            const savedAuth = localStorage.getItem('quiz_auth');
            if (savedAuth) {
                try {
                    authState = JSON.parse(savedAuth);
                    // Only redirect if we're on the home page and user is logged in, BUT NOT if there are URL parameters
                    const hasUrlParams = window.location.search !== '';
                    if (authState.isLoggedIn && 
                        (window.location.pathname === '/new-wordpress/' || window.location.pathname === '/new-wordpress/index.php' || window.location.pathname.endsWith('new-wordpress')) &&
                        !hasUrlParams) {
                        // Hide login elements immediately
                        const loginScreen = document.getElementById('login-screen');
                        const authSection = document.getElementById('auth-section');
                        if (loginScreen) loginScreen.style.display = 'none';
                        if (authSection) authSection.style.display = 'none';
                        
                        // Redirect to quiz start page immediately
                        setTimeout(() => {
                            window.location.replace(window.location.origin + '/new-wordpress/quiz-start/');
                        }, 100);
                        return;
                    }
                } catch (error) {
                    console.error('Error loading saved auth:', error);
                }
            }
        }

        // Login functions moved to top of script

        function handleEmailLogin(event) {
            event.preventDefault();
            const form = event.target;
            const email = form.querySelector('input[type="email"]').value;
            const password = form.querySelector('input[type="password"]').value;
            
            // Simple validation (in real app, would authenticate with backend)
            if (email.includes('@gmail.com') && password.length >= 6) {
                authState = {
                    isLoggedIn: true,
                    user: {
                        name: email.split('@')[0],
                        email: email,
                        avatar: null
                    },
                    loginMethod: 'email'
                };
                
                localStorage.setItem('quiz_auth', JSON.stringify(authState));
                
                // Redirect to dedicated quiz start page
                window.location.href = window.location.origin + '/new-wordpress/quiz-start/';
            } else {
                alert('Please enter a valid Gmail address and password (min 6 characters)');
            }
        }

        function showPostLoginScreen() {
            document.getElementById('login-screen').style.display = 'none';
            document.getElementById('post-login-screen').style.display = 'block';
        }

        // Logout function moved to top of script for reliability

        // Footer Navigation Functions
        function navigateTo(section) {
            // Update active state
            document.querySelectorAll('.footer-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Find and activate the clicked item
            const clickedItem = event.target.closest('.footer-nav-item');
            if (clickedItem) {
                clickedItem.classList.add('active');
            }
            
            // Navigate based on section
            switch(section) {
                case 'home':
                    goToLanding();
                    break;
                case 'test':
                    if (authState.isLoggedIn) {
                        startQuiz();
                    } else {
                        alert('Please log in to take the test');
                    }
                    break;
                case 'results':
                    if (authState.isLoggedIn) {
                        showHistory();
                    } else {
                        alert('Please log in to view results');
                    }
                    break;
                case 'profile':
                    if (authState.isLoggedIn) {
                        showProfileModal();
                    } else {
                        alert('Please log in to view profile');
                    }
                    break;
            }
        }

        // Make showProfileModal globally accessible  
        window.showProfileModal = function() {
            const user = authState.user;
            const profileHtml = `
                <div style="text-align: center; padding: 20px;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 30px; color: white;">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <h3>${user.name}</h3>
                    <p style="color: #666; margin-bottom: 20px;">${user.email}</p>
                    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Login method: ${user.loginMethod || authState.loginMethod}</p>
                    
                    <div style="display: flex; gap: 10px; justify-content: center; margin-top: 30px;">
                        <button onclick="showHistory()" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer;">
                            View History
                        </button>
                        <button onclick="logout(); document.querySelector('.modal').remove();" style="background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer;">
                            Logout
                        </button>
                    </div>
                </div>
            `;
            
            showModal('Profile', profileHtml);
        }

        // DELETED DUPLICATE displayCurrentQuestion FUNCTION
        // The first displayCurrentQuestion function (line ~537) with image handling is now active

        // Utility Functions
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        // Global progress bar instance for the quiz
        let quizProgressBar = null;

        // Initialize app when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // App is ready
            console.log('Enhanced Fidelity Quiz App loaded');
            
            // Add email login form handler
            const emailForm = document.getElementById('email-login-form');
            if (emailForm) {
                emailForm.addEventListener('submit', handleEmailLogin);
            }
            
            // Initialize responsive progress bar for demo/testing
            // Uncomment the following lines to add a demo progress bar to any element with id="demo-progress"
            /*
            if (document.getElementById('demo-progress')) {
                const demoProgress = new ResponsiveProgressBar('demo-progress', {
                    currentStep: 0,
                    totalSteps: 50,
                    label: 'Quiz Progress',
                    color: '#FFFFFF',
                    height: 12
                });
                
                // Demo animation - uncomment to test
                let step = 0;
                const demoInterval = setInterval(() => {
                    step += 1;
                    demoProgress.updateProgress(step);
                    if (step >= 50) {
                        clearInterval(demoInterval);
                    }
                }, 200);
            }
            */
            
            // Prevent zooming on mobile
            document.addEventListener('gesturestart', function (e) {
                e.preventDefault();
            });
            
            // Handle mobile viewport
            function setVH() {
                let vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
            }

            window.addEventListener('resize', setVH);
            setVH();
        });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>