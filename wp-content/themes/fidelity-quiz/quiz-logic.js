// Enhanced Fidelity Quiz Logic System
// Adapted from Logic Test requirements for our 5-category, 3-option system

// Data Models
const Categories = {
    PHONE: 'phone',
    BEHAVIOR: 'behavior', 
    INTIMACY: 'intimacy',
    SOCIAL: 'social',
    ADDITIONAL: 'additional'
};

const Bands = {
    LOW: 'low',
    MEDIUM: 'medium', 
    HIGH: 'high'
};

// Session Management
class QuizSession {
    constructor(targetGender) {
        this.id = this.generateSessionId();
        this.targetGender = targetGender;
        this.answers = [];
        this.progress = 0;
        this.startTime = new Date().toISOString();
        this.lastUpdated = new Date().toISOString();
    }

    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
}

// Core Logic Functions
class FidelityQuizLogic {
    
    // Start a new session
    static startSession(targetGender) {
        const session = new QuizSession(targetGender);
        this.saveSessionToStorage(session);
        return session;
    }

    // Save answer to session
    static saveAnswer(session, questionId, selectedValue, selectedText, topic) {
        const answer = {
            questionId: questionId,
            value: selectedText,
            score: selectedValue, // 0, 1, or 2
            category: topic,
            timestamp: new Date().toISOString()
        };

        // Remove existing answer for this question if any
        session.answers = session.answers.filter(a => a.questionId !== questionId);
        session.answers.push(answer);
        
        // Update progress
        const totalQuestions = session.targetGender === 'male' ? 
            (window.maleQuestions?.length || 50) : 
            (window.femaleQuestions?.length || 50);
        session.progress = Math.round((session.answers.length / totalQuestions) * 100);
        session.lastUpdated = new Date().toISOString();
        
        this.saveSessionToStorage(session);
        return session;
    }

    // Compute comprehensive scores
    static computeScores(answers, questions) {
        // Validate answers
        const validAnswers = this.validateAnswers(answers, questions);
        
        if (validAnswers.length === 0) {
            return this.getDefaultScores();
        }

        // Calculate global score (adapted for 3-option system)
        const totalPossibleScore = validAnswers.length * 2; // Max score per question is 2
        const actualScore = validAnswers.reduce((sum, answer) => sum + answer.score, 0);
        const globalScore = Math.round((actualScore / totalPossibleScore) * 100);

        // Determine band using adapted thresholds
        let band;
        if (globalScore <= 33) {
            band = Bands.LOW;
        } else if (globalScore <= 66) {
            band = Bands.MEDIUM;
        } else {
            band = Bands.HIGH;
        }

        // Calculate category scores
        const categoryScores = this.calculateCategoryScores(validAnswers);
        
        // Get top 3 categories
        const topCategories = this.getTopCategories(categoryScores, validAnswers);

        return {
            globalScore,
            band,
            categoryScores,
            topCategories,
            totalAnswered: validAnswers.length,
            totalQuestions: questions.length,
            completionRate: Math.round((validAnswers.length / questions.length) * 100)
        };
    }

    // Validate answers against questions
    static validateAnswers(answers, questions) {
        const validAnswers = [];
        
        answers.forEach(answer => {
            const question = questions.find(q => q.id === answer.questionId);
            if (question && question.answers && question.answers.includes(answer.value)) {
                validAnswers.push(answer);
            } else {
                console.warn(`Invalid answer for question ${answer.questionId}: ${answer.value}`);
            }
        });

        return validAnswers;
    }

    // Calculate scores by category
    static calculateCategoryScores(answers) {
        const categoryScores = {};
        const categoryData = {};

        // Group answers by category
        answers.forEach(answer => {
            if (!categoryData[answer.category]) {
                categoryData[answer.category] = {
                    totalScore: 0,
                    count: 0
                };
            }
            categoryData[answer.category].totalScore += answer.score;
            categoryData[answer.category].count += 1;
        });

        // Calculate percentage for each category
        Object.keys(categoryData).forEach(category => {
            const data = categoryData[category];
            const maxPossible = data.count * 2; // Max 2 per question in 3-option system
            categoryScores[category] = Math.round((data.totalScore / maxPossible) * 100);
        });

        return categoryScores;
    }

    // Get top 3 categories with tie-breaking
    static getTopCategories(categoryScores, answers) {
        const categories = Object.entries(categoryScores)
            .map(([category, score]) => ({
                category,
                score,
                count: answers.filter(a => a.category === category).length
            }))
            .sort((a, b) => {
                // Primary sort: by score (descending)
                if (b.score !== a.score) return b.score - a.score;
                // Tie-breaker: by number of answers (descending)
                return b.count - a.count;
            })
            .slice(0, 3)
            .map(item => item.category);

        return categories;
    }

    // Generate report based on scores
    static generateReport(scores, premium = false) {
        const templates = this.getContentTemplates();
        const bandTemplate = templates.bands[scores.band];
        
        const report = {
            type: premium ? 'full' : 'lite',
            bandTitle: bandTemplate.title,
            bandSummary: bandTemplate.summary,
            insights: [],
            globalScore: scores.globalScore,
            band: scores.band,
            completionRate: scores.completionRate
        };

        // Add confidence note if completion is low
        if (scores.completionRate < 70) {
            report.confidenceNote = "Results may be less accurate due to incomplete responses. For more reliable insights, consider completing more questions.";
        }

        // Add category insights
        scores.topCategories.slice(0, premium ? 3 : 1).forEach(category => {
            const categoryScore = scores.categoryScores[category];
            const categoryBand = this.getCategoryBand(categoryScore);
            const template = templates.categories[category][categoryBand];
            
            report.insights.push({
                category,
                insight: template.insight,
                score: categoryScore
            });

            if (premium && template.advice) {
                if (!report.advice) report.advice = [];
                report.advice.push({
                    category,
                    tips: template.advice
                });
            }
        });

        // Add action plan for premium
        if (premium) {
            report.actionPlan = templates.actionPlans[scores.band];
        }

        return report;
    }

    // Session management functions
    static pauseSession(session) {
        session.lastUpdated = new Date().toISOString();
        this.saveSessionToStorage(session);
    }

    static resumeSession(sessionId) {
        try {
            const sessionsData = localStorage.getItem('quiz_sessions');
            if (!sessionsData) return null;
            
            const sessions = JSON.parse(sessionsData);
            return sessions[sessionId] || null;
        } catch (error) {
            console.error('Error resuming session:', error);
            return null;
        }
    }

    static finishSession(session) {
        const questions = session.targetGender === 'male' ? 
            (window.maleQuestions || []) : 
            (window.femaleQuestions || []);
        const scores = this.computeScores(session.answers, questions);
        
        // Save to history
        this.saveToHistory(session, scores);
        
        // Clean up session
        this.removeSessionFromStorage(session.id);
        
        return scores;
    }

    // History management
    static getHistory(userId = 'default') {
        try {
            const historyData = localStorage.getItem('quiz_history');
            if (!historyData) return [];
            
            const history = JSON.parse(historyData);
            return history[userId] || [];
        } catch (error) {
            console.error('Error getting history:', error);
            return [];
        }
    }

    static saveToHistory(session, scores) {
        try {
            const historyData = localStorage.getItem('quiz_history');
            const history = historyData ? JSON.parse(historyData) : {};
            const userId = 'default'; // Can be expanded for multi-user
            
            if (!history[userId]) history[userId] = [];
            
            history[userId].unshift({
                date: new Date().toISOString(),
                targetGender: session.targetGender,
                band: scores.band,
                globalScore: scores.globalScore,
                topCategories: scores.topCategories,
                completionRate: scores.completionRate
            });

            // Keep only last 10 records
            history[userId] = history[userId].slice(0, 10);
            
            localStorage.setItem('quiz_history', JSON.stringify(history));
        } catch (error) {
            console.error('Error saving to history:', error);
        }
    }

    // Storage helpers
    static saveSessionToStorage(session) {
        try {
            const sessionsData = localStorage.getItem('quiz_sessions');
            const sessions = sessionsData ? JSON.parse(sessionsData) : {};
            sessions[session.id] = session;
            localStorage.setItem('quiz_sessions', JSON.stringify(sessions));
        } catch (error) {
            console.error('Error saving session:', error);
        }
    }

    static removeSessionFromStorage(sessionId) {
        try {
            const sessionsData = localStorage.getItem('quiz_sessions');
            if (!sessionsData) return;
            
            const sessions = JSON.parse(sessionsData);
            delete sessions[sessionId];
            localStorage.setItem('quiz_sessions', JSON.stringify(sessions));
        } catch (error) {
            console.error('Error removing session:', error);
        }
    }

    // Helper functions
    static getCategoryBand(score) {
        if (score <= 33) return Bands.LOW;
        if (score <= 66) return Bands.MEDIUM;
        return Bands.HIGH;
    }

    static getDefaultScores() {
        return {
            globalScore: 0,
            band: Bands.LOW,
            categoryScores: {},
            topCategories: [],
            totalAnswered: 0,
            totalQuestions: 0,
            completionRate: 0
        };
    }

    // Content templates for reports
    static getContentTemplates() {
        return {
            bands: {
                low: {
                    title: "Aşağı Risk",
                    summary: "Münasibətinizdə ümumiyyətlə sabitlik və güvən əlaməti müşahidə edilir. Kiçik narahatlıqlar ola bilər, lakin ciddi problemlər görünmür."
                },
                medium: {
                    title: "Orta Risk", 
                    summary: "Münasibətinizdə bəzi diqqət tələb edən sahələr var. Bu əlamətlər həmişə problemli olmasa da, ünsiyyət və anlaşma yaxşılaşdırıla bilər."
                },
                high: {
                    title: "Yüksək Risk",
                    summary: "Münasibətinizdə ciddi diqqət tələb edən sahələr mövcuddur. Açıq ünsiyyət və qarşılıqlı anlaşma üçün daha çox səy göstərməyi düşünün."
                }
            },
            categories: {
                phone: {
                    low: {
                        insight: "Telefon istifadəsində açıqlıq və şəffaflıq var. Bu, sağlam münasibət əlamətidir.",
                        advice: ["Telefon paylaşımında olan güvəni qoruyun", "Həmişə açıq ünsiyyət saxlayın", "Bir-birinizin şəxsiyyətinə hörmət edin"]
                    },
                    medium: {
                        insight: "Telefon məxfiliyində bəzi dəyişikliklər müşahidə edilir. Bu, müzakirə edilməli ola bilər.",
                        advice: ["Narahatlıqları açıq şəkildə müzakirə edin", "Qarşılıqlı güvəni gücləndirin", "Şəxsi məkan haqqında danışın", "Texnologiya istifadəsi haqqında razılığa gəlin"]
                    },
                    high: {
                        insight: "Telefon istifadəsində əhəmiyyətli gizlilik artımı var. Bu, dərin söhbət tələb edə bilər.",
                        advice: ["Dürüst və açıq söhbət aparın", "Narahatlıqları sakit şəkildə ifadə edin", "Qarşılıqlı güvəni yenidən qurun", "Peşəkar məsləhət almağı düşünün", "Sərhədlər və gözləntilər müəyyən edin"]
                    }
                },
                behavior: {
                    low: {
                        insight: "Gündəlik davranışlarda sabitlik və uyğunluq var. Bu, yaxşı əlamətdir.",
                        advice: ["Pozitiv davranışları dəstəkləyin", "Rutinləri qoruyun", "Bir-birinizi təqdir edin"]
                    },
                    medium: {
                        insight: "Bəzi davranış dəyişiklikləri müşahidə edilir. Bunları anlamaq üçün ünsiyyət vacibdir.",
                        advice: ["Dəyişikliklərin səbəblərini araşdırın", "Dəstək və anlayış göstərin", "Birgə vaxt keçirməyi artırın", "Stress faktorlarını müəyyən edin"]
                    },
                    high: {
                        insight: "Davranışlarda əhəmiyyətli dəyişikliklər var. Bu, diqqətli yanaşma tələb edir.",
                        advice: ["Həssas mövzuları ehtiyatla müzakirə edin", "Dəstək sistemi yaradın", "Peşəkar köməkdən istifadə edin", "Səbirlə problemləri həll edin", "Özünüzə də diqqət yetirin"]
                    }
                },
                intimacy: {
                    low: {
                        insight: "Yaxınlıq və emosional əlaqədə sabitlik var. Münasibət sağlam görünür.",
                        advice: ["Yaxınlığı qoruyun və inkişaf etdirin", "Romantikliyi diri saxlayın", "Emosional dəstəyi davam etdirin"]
                    },
                    medium: {
                        insight: "Yaxınlıq sahəsində bəzi çətinliklər yaşanır. Ünsiyyət və anlayış vacibdir.",
                        advice: ["Emosional ehtiyacları müzakirə edin", "Yaxınlıq üçün vaxt ayırın", "Qarşılıqlı anlaşmanı güclündirin", "Stresi azaltmaq üçün çalışın"]
                    },
                    high: {
                        insight: "Yaxınlıq və emosional əlaqədə ciddi problemlər var. Xüsusi diqqət tələb edilir.",
                        advice: ["Açıq və dürüst söhbət aparın", "Emosional ehtiyacları başa düşün", "Cütlük terapiyasından yararlanın", "Səbir və anlayış göstərin", "Yaxınlığı yavaş-yavaş bərpa edin"]
                    }
                },
                social: {
                    low: {
                        insight: "Sosial münasibətlərdə açıqlıq və paylaşım var. Bu, sağlam əlamətdir.",
                        advice: ["Sosial şəffaflığı qoruyun", "Birgə sosial fəaliyyətlər planlaşdırın", "Dostluqlara hörmət edin"]
                    },
                    medium: {
                        insight: "Sosial dairədə bəzi dəyişikliklər müşahidə edilir. Bu, müzakirə tələb edə bilər.",
                        advice: ["Yeni dostluklar haqqında danışın", "Sosial planları paylaşın", "Qarşılıqlı güvəni gücləndirin", "Birgə sosial vaxt keçirin"]
                    },
                    high: {
                        insight: "Sosial əlaqələrdə əhəmiyyətli gizlilik artımı var. Diqqətli yanaşma lazımdır.",
                        advice: ["Sosial dəyişikliklər haqqında açıq danışın", "Narahatlıqları ifadə edin", "Qarşılıqlı hörmət yaradın", "Sosial sərhədləri müəyyən edin", "Peşəkar məsləhət alın"]
                    }
                },
                additional: {
                    low: {
                        insight: "Digər sahələrdə sabitlik və şəffaflıq müşahidə edilir.",
                        advice: ["Mövcud sabitliyi qoruyun", "Açıq ünsiyyəti davam etdirin", "Bir-birinizi dəstəkləyin"]
                    },
                    medium: {
                        insight: "Əlavə sahələrdə kiçik narahatlıqlar var, lakin idarə edilə bilər.",
                        advice: ["Kiçik problemləri erkən həll edin", "Düzenli yoxlamalar aparın", "Qarşılıqlı dəstək göstərin", "Açıq ünsiyyəti qoruyun"]
                    },
                    high: {
                        insight: "Əlavə sahələrdə ciddi diqqət tələb edən məsələlər var.",
                        advice: ["Bütün narahatlıqları siyahıya alın", "Prioritetləri müəyyən edin", "Addım-addım həll yolları tapın", "Peşəkar dəstək axtarın", "Uzunmüddətli plan hazırlayın"]
                    }
                }
            },
            actionPlans: {
                low: [
                    "Bu gün: Münasibətinizdə pozitiv tərəfləri qeyd edin",
                    "7 gün ərzində: Partner ilə keyfiyyətli vaxt keçirin",
                    "Uzunmüddətli: Sağlam ünsiyyət səviyyənizi qoruyun"
                ],
                medium: [
                    "Bu gün: Narahatlıq yaşadığınız sahəni müəyyən edin",
                    "7 gün ərzində: Partner ilə açıq və dürüst söhbət aparın",
                    "Uzunmüddətli: Münasibətə yatırım etməyi planlaşdırın",
                    "Əlavə: Qarşılıqlı ehtiyacları daha yaxşı anlamağa çalışın"
                ],
                high: [
                    "Bu gün: Özünüzə qayğı göstərin və dəstək axtarın",
                    "7 gün ərzində: Problemli sahələri prioritetləşdirin",
                    "2 həftə ərzində: Peşəkar məsləhət almağı düşünün",
                    "Uzunmüddətli: Münasibətin gələcəyi haqqında düşünün",
                    "Əlavə: Özünüzü və partnerinizi təhlil edin"
                ]
            }
        };
    }
}

// Quiz UI Integration Functions for our new design
class QuizUI {
    static currentSession = null;
    static currentQuestionIndex = 0;
    static currentGender = null;
    static questionPool = [];

    // Initialize quiz with gender selection
    static initializeQuiz(gender) {
        this.currentGender = gender;
        this.questionPool = gender === 'male' ? 
            (window.maleQuestions || []) : 
            (window.femaleQuestions || []);
        
        console.log(`Initializing quiz for ${gender}, found ${this.questionPool.length} questions`);
        console.log('First question:', this.questionPool[0]);
        
        if (this.questionPool.length === 0) {
            console.error('No questions found for gender:', gender);
            alert('No questions available for this quiz. Please contact support.');
            return;
        }
        
        this.currentSession = FidelityQuizLogic.startSession(gender);
        this.currentQuestionIndex = 0;
        
        // Show question screen and load first question
        this.showQuestionScreen();
        this.loadCurrentQuestion();
        this.updateProgress();
    }

    // Show question screen with our new design
    static showQuestionScreen() {
        document.getElementById('gender-selection').classList.add('hidden');
        document.getElementById('quiz-app').classList.add('hidden');
        document.getElementById('question-screen').classList.remove('hidden');
    }

    // Load current question into our new UI elements
    static loadCurrentQuestion() {
        const question = this.questionPool[this.currentQuestionIndex];
        if (!question) {
            this.finishQuiz();
            return;
        }

        // Update question text (static data uses 'question' field)
        const questionText = document.getElementById('question-text');
        if (questionText) {
            questionText.textContent = question.question || question.text || 'Question text not available';
        }

        // Handle question image (static data uses 'image' field)
        const imageContainer = document.getElementById('question-image-container');
        const questionImage = document.getElementById('question-image');
        const imagePlaceholder = document.getElementById('image-placeholder');
        
        const imageUrl = question.image || question.imageUrl;
        if (imageUrl && imageUrl.trim() !== '') {
            // Show actual image
            questionImage.src = imageUrl;
            questionImage.style.display = 'block';
            if (imagePlaceholder) imagePlaceholder.style.display = 'none';
        } else {
            // Show placeholder
            questionImage.style.display = 'none';
            if (imagePlaceholder) imagePlaceholder.style.display = 'flex';
        }

        // Load answer options (static data uses 'answers' field)
        const answerContainer = document.getElementById('answer-options');
        const answers = question.answers || question.options || [];
        
        if (answerContainer && answers.length > 0) {
            answerContainer.innerHTML = '';
            
            answers.forEach((answer, index) => {
                const answerPill = document.createElement('div');
                answerPill.className = 'answer-pill';
                answerPill.onclick = () => this.selectAnswer(index, answer, question);
                
                answerPill.innerHTML = `
                    <div class="answer-chip">${String.fromCharCode(65 + index)}</div>
                    <div class="answer-text">${answer}</div>
                `;
                
                answerContainer.appendChild(answerPill);
            });
        } else {
            console.error('No answer options found for question:', question);
        }
    }

    // Handle answer selection
    static selectAnswer(answerIndex, answerText, question) {
        // Remove previous selections
        document.querySelectorAll('.answer-pill').forEach(pill => {
            pill.classList.remove('selected');
        });
        
        // Mark current selection
        event.currentTarget.classList.add('selected');
        
        // Save answer to session (score based on answer index: 0=high risk, 1=medium, 2=low risk)
        const answerScore = answerIndex; // 0, 1, or 2
        FidelityQuizLogic.saveAnswer(
            this.currentSession,
            question.id,
            answerScore,
            answerText,
            question.topic
        );
        
        // Move to next question after short delay
        setTimeout(() => {
            this.nextQuestion();
        }, 800);
    }

    // Move to next question
    static nextQuestion() {
        this.currentQuestionIndex++;
        
        if (this.currentQuestionIndex >= this.questionPool.length) {
            this.finishQuiz();
        } else {
            this.loadCurrentQuestion();
            this.updateProgress();
        }
    }

    // Update progress bar and heart position
    static updateProgress() {
        const totalQuestions = this.questionPool.length;
        const currentProgress = this.currentQuestionIndex;
        const progressPercent = Math.min((currentProgress / totalQuestions) * 100, 100);
        
        // Update progress bar fill
        const progressFill = document.getElementById('progress-fill');
        if (progressFill) {
            progressFill.style.width = progressPercent + '%';
        }
        
        // Update heart position
        const progressHeart = document.getElementById('progress-heart');
        if (progressHeart) {
            progressHeart.style.left = Math.max(progressPercent, 15) + '%';
        }
        
        // Update progress text
        const progressText = document.getElementById('progress-text');
        if (progressText) {
            progressText.textContent = `${currentProgress + 1} of ${totalQuestions} completed`;
        }
        
        console.log(`Progress: ${currentProgress + 1}/${totalQuestions} (${progressPercent}%)`);
    }

    // Finish quiz and show results
    static finishQuiz() {
        if (!this.currentSession) return;
        
        const scores = FidelityQuizLogic.finishSession(this.currentSession);
        
        // Show paywall screen
        this.showPaywall(scores);
    }

    // Show paywall with basic results
    static showPaywall(scores) {
        document.getElementById('question-screen').classList.add('hidden');
        document.getElementById('paywall-screen').classList.remove('hidden');
        
        // Could show basic score here if desired
        console.log('Quiz completed with scores:', scores);
    }

    // View lite results
    static viewLiteResults() {
        document.getElementById('paywall-screen').classList.add('hidden');
        document.getElementById('lite-results').classList.remove('hidden');
        
        // Show basic results
        this.displayResults(false);
    }

    // Show full results (premium)
    static viewFullResults() {
        document.getElementById('paywall-screen').classList.add('hidden');
        document.getElementById('full-results').classList.remove('hidden');
        
        // Show full results
        this.displayResults(true);
    }

    // Display results in UI
    static displayResults(isPremium) {
        if (!this.currentSession) return;
        
        const questions = this.questionPool;
        const scores = FidelityQuizLogic.computeScores(this.currentSession.answers, questions);
        const report = FidelityQuizLogic.generateReport(scores, isPremium);
        
        // Update score displays
        const scoreElements = isPremium ? 
            ['full-overall-score', 'full-risk-level'] : 
            ['overall-score', 'risk-level'];
            
        const scoreElement = document.getElementById(scoreElements[0]);
        const riskElement = document.getElementById(scoreElements[1]);
        
        if (scoreElement) scoreElement.textContent = scores.globalScore;
        if (riskElement) riskElement.textContent = report.bandTitle;
        
        // Show topic breakdown for premium
        if (isPremium) {
            const topicContainer = document.getElementById('topic-breakdown');
            if (topicContainer && report.insights) {
                topicContainer.innerHTML = '';
                
                report.insights.forEach(insight => {
                    const topicElement = document.createElement('div');
                    topicElement.className = 'premium-feature';
                    topicElement.innerHTML = `
                        <span class="feature-check">✓</span>
                        <span class="premium-feature-text">${insight.category}: ${insight.insight}</span>
                    `;
                    topicContainer.appendChild(topicElement);
                });
            }
        }
    }

    // Restart quiz
    static restartQuiz() {
        this.currentSession = null;
        this.currentQuestionIndex = 0;
        
        // Hide result screens
        document.getElementById('lite-results').classList.add('hidden');
        document.getElementById('full-results').classList.add('hidden');
        document.getElementById('paywall-screen').classList.add('hidden');
        document.getElementById('question-screen').classList.add('hidden');
        
        // Show gender selection or main screen
        if (document.getElementById('gender-selection').classList.contains('hidden')) {
            document.getElementById('quiz-app').classList.remove('hidden');
        } else {
            document.getElementById('gender-selection').classList.remove('hidden');
        }
    }
}

// Export for use in other files
window.FidelityQuizLogic = FidelityQuizLogic;
window.QuizSession = QuizSession;
window.Categories = Categories;
window.Bands = Bands;
window.QuizUI = QuizUI;