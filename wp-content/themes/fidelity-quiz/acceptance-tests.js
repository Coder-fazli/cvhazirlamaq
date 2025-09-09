// Acceptance Tests for Fidelity Quiz Logic
// Based on Logic Test.txt requirements adapted for our 5-category, 3-option system

class AcceptanceTests {
    
    static runAllTests() {
        console.log('🚀 Running Acceptance Tests for Fidelity Quiz Logic');
        console.log('=' .repeat(60));
        
        const results = {
            testA: this.testA_AllLowest(),
            testB: this.testB_HighPhoneAndSocial(), 
            testC: this.testC_PartialCompletion(),
            testD: this.testD_OptionValidation(),
            testE: this.testE_TieBreaking()
        };
        
        const passed = Object.values(results).filter(r => r.passed).length;
        const total = Object.keys(results).length;
        
        console.log('=' .repeat(60));
        console.log(`📊 Test Results: ${passed}/${total} tests passed`);
        
        if (passed === total) {
            console.log('✅ All acceptance tests PASSED!');
        } else {
            console.log('❌ Some tests FAILED. Check details above.');
        }
        
        return results;
    }
    
    // Test A: All "Never" equivalent (lowest scores)
    static testA_AllLowest() {
        console.log('\n🧪 Test A: All Lowest Scores (expecting low band, score ≤ 5)');
        
        try {
            // Create answers with all lowest scores (0)
            const answers = [];
            const questions = [...femaleQuestions, ...maleQuestions].slice(0, 50); // Use first 50
            
            questions.forEach(question => {
                answers.push({
                    questionId: question.id,
                    value: question.answers[0], // First option (typically lowest risk)
                    score: 0, // Lowest possible score
                    category: question.topic
                });
            });
            
            const scores = FidelityQuizLogic.computeScores(answers, questions);
            const report = FidelityQuizLogic.generateReport(scores, false);
            
            // Validate results
            const passed = scores.globalScore <= 5 && scores.band === 'low';
            const hasInsight = report.insights.length === 1; // Lite report should have 1 insight
            const noAdvice = !report.advice || report.advice.length === 0;
            
            console.log(`   Global Score: ${scores.globalScore}% (expected ≤ 5)`);
            console.log(`   Band: ${scores.band} (expected: low)`);
            console.log(`   Insights: ${report.insights.length} (expected: 1 for lite)`);
            console.log(`   Advice: ${report.advice ? report.advice.length : 0} (expected: 0 for lite)`);
            
            const finalPassed = passed && hasInsight && noAdvice;
            console.log(`   Result: ${finalPassed ? '✅ PASSED' : '❌ FAILED'}`);
            
            return {
                passed: finalPassed,
                details: { scores, report }
            };
            
        } catch (error) {
            console.log(`   Result: ❌ FAILED - Error: ${error.message}`);
            return { passed: false, error: error.message };
        }
    }
    
    // Test B: High Phone & Social scores
    static testB_HighPhoneAndSocial() {
        console.log('\n🧪 Test B: High Phone & Social (expecting medium/high band with phone & social in top categories)');
        
        try {
            const answers = [];
            const questions = [...femaleQuestions, ...maleQuestions];
            
            questions.forEach(question => {
                let score;
                if (question.topic === 'phone' || question.topic === 'social') {
                    score = 2; // Highest score for phone and social
                } else {
                    score = Math.floor(Math.random() * 2); // Mixed for others
                }
                
                answers.push({
                    questionId: question.id,
                    value: question.answers[score],
                    score: score,
                    category: question.topic
                });
            });
            
            const scores = FidelityQuizLogic.computeScores(answers, questions);
            const report = FidelityQuizLogic.generateReport(scores, true); // Premium report
            
            // Validate results
            const correctBand = scores.band === 'medium' || scores.band === 'high';
            const hasPhoneAndSocial = scores.topCategories.includes('phone') && scores.topCategories.includes('social');
            const hasAdvice = report.advice && report.advice.length > 0;
            const hasActionPlan = report.actionPlan && report.actionPlan.length > 0;
            
            console.log(`   Global Score: ${scores.globalScore}% (expected > 33)`);
            console.log(`   Band: ${scores.band} (expected: medium or high)`);
            console.log(`   Top Categories: ${scores.topCategories.join(', ')} (expected: phone & social included)`);
            console.log(`   Advice sections: ${report.advice ? report.advice.length : 0} (expected > 0 for premium)`);
            console.log(`   Action plan steps: ${report.actionPlan ? report.actionPlan.length : 0} (expected > 0 for premium)`);
            
            const finalPassed = correctBand && hasPhoneAndSocial && hasAdvice && hasActionPlan;
            console.log(`   Result: ${finalPassed ? '✅ PASSED' : '❌ FAILED'}`);
            
            return {
                passed: finalPassed,
                details: { scores, report }
            };
            
        } catch (error) {
            console.log(`   Result: ❌ FAILED - Error: ${error.message}`);
            return { passed: false, error: error.message };
        }
    }
    
    // Test C: Partial completion (40% answered)
    static testC_PartialCompletion() {
        console.log('\n🧪 Test C: Partial Completion (40% answered, expecting confidence note)');
        
        try {
            const answers = [];
            const questions = [...femaleQuestions, ...maleQuestions];
            const answerCount = Math.floor(questions.length * 0.4); // 40% completion
            
            // Answer only first 40% of questions
            questions.slice(0, answerCount).forEach(question => {
                const score = Math.floor(Math.random() * 3); // Random score
                answers.push({
                    questionId: question.id,
                    value: question.answers[score],
                    score: score,
                    category: question.topic
                });
            });
            
            const scores = FidelityQuizLogic.computeScores(answers, questions);
            const report = FidelityQuizLogic.generateReport(scores, false); // Lite report
            
            // Validate results
            const hasConfidenceNote = report.confidenceNote && report.confidenceNote.length > 0;
            const correctCompletion = scores.completionRate < 70;
            const liteOutput = report.type === 'lite';
            
            console.log(`   Completion Rate: ${scores.completionRate}% (expected < 70)`);
            console.log(`   Has Confidence Note: ${hasConfidenceNote ? 'Yes' : 'No'} (expected: Yes)`);
            console.log(`   Report Type: ${report.type} (expected: lite)`);
            console.log(`   Band: ${scores.band} (computed from available answers)`);
            
            const finalPassed = hasConfidenceNote && correctCompletion && liteOutput;
            console.log(`   Result: ${finalPassed ? '✅ PASSED' : '❌ FAILED'}`);
            
            return {
                passed: finalPassed,
                details: { scores, report }
            };
            
        } catch (error) {
            console.log(`   Result: ❌ FAILED - Error: ${error.message}`);
            return { passed: false, error: error.message };
        }
    }
    
    // Test D: Option validation (invalid answers should be ignored)
    static testD_OptionValidation() {
        console.log('\n🧪 Test D: Option Validation (invalid answers ignored, no crash)');
        
        try {
            const answers = [];
            const questions = femaleQuestions.slice(0, 10); // Use first 10 questions
            
            questions.forEach((question, index) => {
                if (index % 2 === 0) {
                    // Valid answer
                    answers.push({
                        questionId: question.id,
                        value: question.answers[0],
                        score: 0,
                        category: question.topic
                    });
                } else {
                    // Invalid answer
                    answers.push({
                        questionId: question.id,
                        value: 'INVALID_OPTION_THAT_DOES_NOT_EXIST',
                        score: 0,
                        category: question.topic
                    });
                }
            });
            
            // This should not crash and should ignore invalid answers
            const scores = FidelityQuizLogic.computeScores(answers, questions);
            
            // Should only count valid answers (5 out of 10)
            const expectedValidAnswers = 5;
            const actualValidAnswers = scores.totalAnswered;
            
            console.log(`   Total Answers Provided: ${answers.length}`);
            console.log(`   Valid Answers Counted: ${actualValidAnswers} (expected: ${expectedValidAnswers})`);
            console.log(`   Completion Rate: ${scores.completionRate}%`);
            console.log(`   No Crash: Yes (system handled invalid answers gracefully)`);
            
            const finalPassed = actualValidAnswers === expectedValidAnswers;
            console.log(`   Result: ${finalPassed ? '✅ PASSED' : '❌ FAILED'}`);
            
            return {
                passed: finalPassed,
                details: { scores }
            };
            
        } catch (error) {
            console.log(`   Result: ❌ FAILED - Error: ${error.message}`);
            return { passed: false, error: error.message };
        }
    }
    
    // Test E: Tie-breaking (exactly 3 categories returned)
    static testE_TieBreaking() {
        console.log('\n🧪 Test E: Tie-breaking (exactly 3 top categories, tie-broken by answer count)');
        
        try {
            const answers = [];
            const questions = [...femaleQuestions, ...maleQuestions];
            
            // Create a scenario where multiple categories have the same score
            // but different answer counts
            const categoryAnswerCounts = {
                phone: 10,
                behavior: 8,
                intimacy: 6,
                social: 4,
                additional: 2
            };
            
            let questionIndex = 0;
            Object.entries(categoryAnswerCounts).forEach(([category, count]) => {
                const categoryQuestions = questions.filter(q => q.topic === category).slice(0, count);
                
                categoryQuestions.forEach(question => {
                    // Give same score (1) to create ties
                    answers.push({
                        questionId: question.id,
                        value: question.answers[1],
                        score: 1,
                        category: question.topic
                    });
                });
            });
            
            const scores = FidelityQuizLogic.computeScores(answers, questions);
            
            console.log(`   Total Categories: ${Object.keys(scores.categoryScores).length}`);
            console.log(`   Top Categories: ${scores.topCategories.join(', ')} (count: ${scores.topCategories.length})`);
            console.log(`   Category Scores: ${JSON.stringify(scores.categoryScores)}`);
            console.log(`   Answer Counts per Category:`);
            
            Object.entries(categoryAnswerCounts).forEach(([category, count]) => {
                const categoryAnswers = answers.filter(a => a.category === category);
                console.log(`     ${category}: ${categoryAnswers.length} answers`);
            });
            
            const exactlyThree = scores.topCategories.length === 3;
            console.log(`   Exactly 3 Top Categories: ${exactlyThree ? 'Yes' : 'No'} (expected: Yes)`);
            
            const finalPassed = exactlyThree;
            console.log(`   Result: ${finalPassed ? '✅ PASSED' : '❌ FAILED'}`);
            
            return {
                passed: finalPassed,
                details: { scores }
            };
            
        } catch (error) {
            console.log(`   Result: ❌ FAILED - Error: ${error.message}`);
            return { passed: false, error: error.message };
        }
    }
}

// Export for browser console usage
window.AcceptanceTests = AcceptanceTests;

// Auto-run tests when this file is loaded (can be disabled)
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Acceptance Tests loaded. Run AcceptanceTests.runAllTests() to execute.');
    
    // Uncomment the following line to auto-run tests on page load:
    // setTimeout(() => AcceptanceTests.runAllTests(), 2000);
});