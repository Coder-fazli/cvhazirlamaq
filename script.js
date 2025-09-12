// Simple GitHub Pages compatible AI Name Rewriter
class NameRewriter {
    constructor() {
        this.initializeElements();
        this.bindEvents();
        this.loadAzerbaijaniTemplates();
    }

    initializeElements() {
        this.inputText = document.getElementById('inputText');
        this.outputSection = document.getElementById('outputSection');
        this.outputText = document.getElementById('outputText');
        this.outputStats = document.getElementById('outputStats');
        this.rewriteBtn = document.getElementById('rewriteBtn');
        this.copyBtn = document.getElementById('copyBtn');
        this.downloadBtn = document.getElementById('downloadBtn');
        this.creativityLevel = document.getElementById('creativityLevel');
        this.language = document.getElementById('language');
        this.style = document.getElementById('style');
    }

    bindEvents() {
        this.inputText.addEventListener('input', () => this.updateWordCount());
        this.rewriteBtn.addEventListener('click', () => this.rewriteText());
        this.copyBtn.addEventListener('click', () => this.copyToClipboard());
        this.downloadBtn.addEventListener('click', () => this.downloadText());
        this.initializeFAQ();
        this.initializeSmoothScrolling();
    }

    loadAzerbaijaniTemplates() {
        // Pre-loaded Azerbaijani name meanings and rewriting templates
        this.nameDatabase = {
            // Popular Azerbaijani names with meanings
            'ayla': 'Ayla adı türk mənşəli olub, "ay işığı" mənasını daşıyır. Bu ad saflığı, gözəlliyi və nur saçan xüsusiyyətləri simvollaşdırır.',
            'əli': 'Əli adı ərəb mənşəli olub "yüksək, üstün" mənasını daşıyır. İslam tarixində böyük əhəmiyyət kəsb edən bu ad güc və ləyaqəti simvollaşdırır.',
            'aysel': 'Aysel adı türk mənşəli olub, "ay selən" mənasını daşıyır. Bu ad gözəllik, incəlik və gecənin poetik gözəlliyini əks etdirir.',
            'elnur': 'Elnur adı türk-azərbaycan mənşəli olub, "xalqın nuru" mənasını daşıyır. Bu ad lidərlik, işıq saçma və xalqına xidmət etmə keyfiyyətlərini simvollaşdırır.',
            'günel': 'Günel adı türk mənşəli olub, "günün gözəlliyi" mənasını daşıyır. Bu ad parlaqlığı, enerji və həyat sevincini əks etdirir.',
            'leyla': 'Leyla adı ərəb mənşəli olub, "gecə gözəlliği" mənasını daşıyır. Klassik ədebiyyatda məhşur olan bu ad romantizm və gözəlliyi simvollaşdırır.'
        };

        this.rewritingTemplates = {
            'conservative': {
                'az': [
                    '{original} Bu ad Azərbaycan mədəniyyətində xüsusi yer tutur.',
                    'Tarixi mənbələrə görə, {original} Bu adın dərin mədəni kökləri vardır.',
                    '{original} Azərbaycan ənənələrində bu ad xüsusi əhəmiyyət daşıyır.'
                ],
                'en': [
                    '{original} This name holds special significance in Azerbaijani culture.',
                    'According to historical sources, {original} This name has deep cultural roots.',
                    '{original} In Azerbaijani traditions, this name carries special importance.'
                ]
            },
            'creative': {
                'az': [
                    '{original} Bu möhtəşəm ad Azərbaycan səmasında bir ulduz kimi parıldayır və daşıyıcısına xüsusi enerji bəxş edir.',
                    'Qədim vaxtlardan günümüzə qədər {original} bu ad öz gözəlliyini və mənasının dərinliyini qorumuşdur.',
                    '{original} - bu sadəcə ad deyil, bu bir hekayə, bir ənənə, Azərbaycan xalqının mədəni zənginliyinin bir parçasıdır.'
                ],
                'en': [
                    '{original} This magnificent name shines like a star in the Azerbaijani sky, bestowing special energy upon its bearer.',
                    'From ancient times to today, {original} this name has preserved its beauty and the depth of its meaning.',
                    '{original} - this is not just a name, this is a story, a tradition, a piece of the cultural richness of Azerbaijan.'
                ]
            },
            'poetic': {
                'az': [
                    '{original} Sanki şeir misraları kimi səslənən bu ad, Azərbaycan torpağının ruhunu, səmasının saflığını özündə daşıyır.',
                    'Hər {original} adında gizli bir nəğmə, gizli bir hekayə, Azərbaycan qadın gözəlliyinin və zərifliğinin təcəssümü var.',
                    '{original} - bu ad dağların əzəmətini, çayların ahəngini və Azərbaycan xalqının qəlbindəki sevginin təranəsini daşıyır.'
                ]
            }
        };
    }

    updateWordCount() {
        const text = this.inputText.value.trim();
        const wordCount = text ? text.split(/\s+/).length : 0;
        document.querySelector('.word-count').textContent = `${wordCount} Söz`;
        
        // Enable/disable rewrite button
        this.rewriteBtn.disabled = wordCount === 0;
        if (wordCount === 0) {
            this.rewriteBtn.style.opacity = '0.6';
        } else {
            this.rewriteBtn.style.opacity = '1';
        }
    }

    async rewriteText() {
        const text = this.inputText.value.trim();
        if (!text) {
            this.showNotification('Xahiş edirik mətn daxil edin', 'warning');
            return;
        }

        this.showLoading(true);

        // Simulate API processing time
        await new Promise(resolve => setTimeout(resolve, 2000));

        try {
            const rewrittenText = this.processText(text);
            this.displayResult({
                rewritten_text: rewrittenText,
                original_length: text.length,
                rewritten_length: rewrittenText.length,
                creativity_level: this.creativityLevel.value
            });
        } catch (error) {
            this.showNotification('Mətn emal edilərkən xəta baş verdi', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    processText(text) {
        const creativity = this.creativityLevel.value;
        const language = this.language.value;
        const style = this.style.value;
        
        // Check if it's a single name
        const isName = text.split(' ').length === 1 && /^[a-zA-ZəöüçşığĞÜÇŞıİÖƏ]+$/.test(text);
        
        if (isName) {
            return this.rewriteName(text.toLowerCase(), creativity, language);
        } else {
            return this.rewriteGenericText(text, creativity, language, style);
        }
    }

    rewriteName(name, creativity, language) {
        // Check if we have this name in our database
        if (this.nameDatabase[name]) {
            const baseInfo = this.nameDatabase[name];
            return this.enhanceNameDescription(baseInfo, creativity, language);
        }
        
        // Generate creative description for unknown names
        return this.generateNameDescription(name, creativity, language);
    }

    enhanceNameDescription(baseInfo, creativity, language) {
        const templates = this.rewritingTemplates[creativity]?.[language] || this.rewritingTemplates['creative'][language];
        const template = templates[Math.floor(Math.random() * templates.length)];
        
        if (creativity === 'poetic' && language === 'az') {
            const poeticTemplates = this.rewritingTemplates.poetic.az;
            const poeticTemplate = poeticTemplates[Math.floor(Math.random() * poeticTemplates.length)];
            return baseInfo + ' ' + poeticTemplate.replace('{original}', '');
        }
        
        return baseInfo + ' ' + this.addCreativeElements(baseInfo, creativity);
    }

    generateNameDescription(name, creativity, language) {
        const capitalizedName = name.charAt(0).toUpperCase() + name.slice(1);
        
        if (language === 'az') {
            const descriptions = [
                `${capitalizedName} adı Azərbaycan mədəniyyətində xüsusi məna daşıyır və öz sahibinə xüsusi xarakter bəxş edir.`,
                `${capitalizedName} - bu gözəl ad Azərbaycan ənənələrində dərin kökləri olan və mədəni zənginliyimizi əks etdirən adlardan biridir.`,
                `${capitalizedName} adının daşıyıcısı özündə Azərbaycan xalqının şərəf, ləyaqət və mədəni dəyərlərini birləşdirir.`
            ];
            
            let result = descriptions[Math.floor(Math.random() * descriptions.length)];
            
            if (creativity === 'creative' || creativity === 'poetic') {
                result += ` Bu ad sanki Azərbaycan torpağının ruhunu, xalqımızın qədim hikmətini və gələcəyə olan ümidini özündə ehtiva edir.`;
            }
            
            return result;
        } else {
            return `${capitalizedName} is a beautiful name with special significance in Azerbaijani culture, embodying the rich traditions and values of the Azerbaijani people.`;
        }
    }

    rewriteGenericText(text, creativity, language, style) {
        // Simple text enhancement based on creativity level
        let result = text;
        
        if (creativity === 'creative' || creativity === 'poetic') {
            // Add more descriptive language
            result = this.addDescriptiveWords(result, language);
        }
        
        if (style === 'academic') {
            result = this.makeAcademic(result, language);
        } else if (style === 'poetic') {
            result = this.makePoetic(result, language);
        }
        
        return result;
    }

    addDescriptiveWords(text, language) {
        if (language === 'az') {
            // Add Azerbaijani descriptive elements
            return text.replace(/gözəl/g, 'möhtəşəm gözəl')
                      .replace(/yaxşı/g, 'əla və mükəmməl')
                      .replace(/böyük/g, 'heyrətamiz böyük');
        }
        return text;
    }

    makeAcademic(text, language) {
        // Add academic tone
        if (language === 'az') {
            return `Tədqiqat nəticələrində müəyyən edilmişdir ki, ${text.toLowerCase()} Bu məsələ elmі baxımdan dərin təhlil tələb edir.`;
        }
        return `Research indicates that ${text.toLowerCase()} This matter requires thorough academic analysis.`;
    }

    makePoetic(text, language) {
        if (language === 'az') {
            return `${text} - sanki şeir misraları kimi səslənən bu sözlər, qəlbin ən dərin tellərini oxşayır.`;
        }
        return text;
    }

    addCreativeElements(text, creativity) {
        const elements = [
            'Bu gözəl detallar mədəniyyətimizdə xüsusi yer tutur.',
            'Azərbaycan ənənələrində bu cür xüsusiyyətlər böyük əhəmiyyət kəsb edir.',
            'Xalqımızın zəngin mədəni irsinin bir hissəsi olaraq bu məlumatlar dəyərlidir.'
        ];
        
        return elements[Math.floor(Math.random() * elements.length)];
    }

    displayResult(data) {
        this.outputText.textContent = data.rewritten_text;
        this.outputStats.textContent = `${data.rewritten_length} simvol, ${data.rewritten_text.split(' ').length} söz`;
        this.outputSection.style.display = 'block';
        this.outputSection.scrollIntoView({ behavior: 'smooth' });
        this.showNotification('Mətn uğurla yenidən yazıldı!', 'success');
    }

    copyToClipboard() {
        const text = this.outputText.textContent;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                this.showNotification('Mətn kopyalandı!', 'success');
                this.animateButton(this.copyBtn);
            });
        } else {
            // Fallback
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            this.showNotification('Mətn kopyalandı!', 'success');
            this.animateButton(this.copyBtn);
        }
    }

    downloadText() {
        const text = this.outputText.textContent;
        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'yeniden-yazilmis-metn.txt';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        this.showNotification('Fayl yükləndi!', 'success');
        this.animateButton(this.downloadBtn);
    }

    showLoading(show) {
        if (show) {
            this.rewriteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Emal edilir...';
            this.rewriteBtn.disabled = true;
        } else {
            this.rewriteBtn.innerHTML = '<i class="fas fa-magic"></i> Yenidən yaz';
            this.rewriteBtn.disabled = false;
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        // Add styles
        const style = document.createElement('style');
        style.textContent = `
            .notification {
                position: fixed;
                top: 100px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                color: white;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                z-index: 10001;
                font-weight: 500;
                animation: slideIn 0.3s ease;
            }
            .notification-success { background: #38a169; }
            .notification-error { background: #e53e3e; }
            .notification-warning { background: #ed8936; }
            .notification-info { background: #3182ce; }
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        
        if (!document.querySelector('style[data-notification]')) {
            style.setAttribute('data-notification', 'true');
            document.head.appendChild(style);
        }
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    animateButton(button) {
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 150);
    }

    initializeFAQ() {
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                faqItems.forEach(faqItem => faqItem.classList.remove('active'));
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    }

    initializeSmoothScrolling() {
        const navLinks = document.querySelectorAll('.nav-links a[href^="#"]');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    const headerHeight = 80;
                    const targetPosition = targetElement.offsetTop - headerHeight;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new NameRewriter();
    console.log('🎯 AI Name Rewriter - GitHub Pages Edition loaded successfully!');
});