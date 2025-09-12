<?php
// OpenAI GPT-4o Mini Handler
// Handles all AI processing with advanced prompting

require_once 'config.php';

class OpenAIHandler {
    private $apiKey;
    private $model;
    
    public function __construct() {
        $this->apiKey = OPENAI_API_KEY;
        $this->model = OPENAI_MODEL;
    }
    
    /**
     * Generate AI-powered content rewriting
     */
    public function rewriteContent($text, $creativity = 'balanced', $language = 'az', $style = 'modern') {
        try {
            // Build the system prompt based on language
            $systemPrompt = $this->buildSystemPrompt($language, $creativity, $style);
            
            // Build the user prompt
            $userPrompt = $this->buildUserPrompt($text, $creativity, $language, $style);
            
            // Add anti-detection techniques
            $temperature = $this->getTemperature($creativity);
            $maxTokens = $this->calculateMaxTokens($text);
            
            // Make API call
            $response = $this->callOpenAI($systemPrompt, $userPrompt, $temperature, $maxTokens);
            
            if ($response && isset($response['choices'][0]['message']['content'])) {
                $rewrittenText = trim($response['choices'][0]['message']['content']);
                
                // Post-process for anti-detection
                $rewrittenText = $this->applyAntiDetection($rewrittenText, $creativity);
                
                return [
                    'success' => true,
                    'rewritten_text' => $rewrittenText,
                    'original_length' => strlen($text),
                    'rewritten_length' => strlen($rewrittenText),
                    'creativity_level' => $creativity,
                    'language' => $language,
                    'style' => $style
                ];
            } else {
                throw new Exception('Invalid response from OpenAI API');
            }
            
        } catch (Exception $e) {
            logError('OpenAI Handler Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'AI processing failed. Please try again.',
                'debug' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Build system prompt based on language and creativity
     */
    private function buildSystemPrompt($language, $creativity, $style) {
        $prompts = [
            'az' => [
                'conservative' => "Siz Azərbaycan dilində professional mətn redaktoru və mədəniyyət mütəxəssisisiniz. Verilən məlumatları dəqiq, lakin təzə və orijinal şəkildə yenidən yazın. Azərbaycan mədəniyyəti və dil xüsusiyyətlərini nəzərə alın. Heç vaxt kopyalama etməyin - hər cümləni tamamilə yenidən qurub, mənasını saxlayın.",
                
                'balanced' => "Siz yaradıcı Azərbaycan yazıçısı və mədəniyyət üzrə mütəxəssisiniz. Verilən məlumatları yaradıcı, canlı və orijinal şəkildə yenidən yazın. Azərbaycan dilinin zənginliyindən, xalq hikmətlərindən və mədəni dəyərlərdən istifadə edin. Məzmunu saxlayıb, ifadəni tamamilə dəyişdirin.",
                
                'creative' => "Siz möhtəşəm Azərbaycan yazıçısı və poet ruhlu mütəxəssisiniz. Verilən məlumatları son dərəcə yaradıcı, rəngarəng və orijinal şəkildə yenidən yazın. Azərbaycan ədəbiyyatının gözəllikləri, metaforalar, təşbihlər və mədəni zənginliklə məzmunu canlandırın. Hər sözü incə və düşünülmüş seçin.",
                
                'poetic' => "Siz Azərbaycan ədəbiyyatının ustası və poetik ruhlu yazıçısınız. Verilən məlumatları şeir kimi ahəngdar, metaforik və son dərəcə gözəl şəkildə yenidən yazın. Azərbaycan dilinin poetik imkanlarını, təbiət təsvirlərini və milli ruh dərinliklərini əks etdirin. Hər cümləni sanət əsəri kimi yazın."
            ],
            'en' => [
                'conservative' => "You are a professional content editor specializing in Azerbaijani culture and names. Rewrite the given information accurately but with fresh, original phrasing. Consider Azerbaijani cultural context and linguistic nuances. Never copy - completely restructure every sentence while preserving meaning.",
                
                'balanced' => "You are a creative writer and Azerbaijani culture expert. Rewrite the given information in a creative, vivid, and original way. Use the richness of Azerbaijani language understanding, folk wisdom, and cultural values. Maintain the content but completely transform the expression.",
                
                'creative' => "You are an exceptional writer with deep knowledge of Azerbaijani culture. Rewrite the given information in an extremely creative, colorful, and original manner. Bring the content to life with Azerbaijani literary beauty, metaphors, comparisons, and cultural richness. Choose every word carefully and thoughtfully.",
                
                'poetic' => "You are a master of Azerbaijani literature with a poetic soul. Rewrite the given information in a harmonious, metaphorical, and extremely beautiful way, like poetry. Reflect the poetic possibilities of understanding Azerbaijani culture, nature descriptions, and national spirit depths. Write every sentence as a work of art."
            ]
        ];
        
        $basePrompt = $prompts[$language][$creativity] ?? $prompts['az']['balanced'];
        
        // Add style-specific instructions
        $styleInstructions = $this->getStyleInstructions($style, $language);
        
        return $basePrompt . "\n\n" . $styleInstructions;
    }
    
    /**
     * Get style-specific instructions
     */
    private function getStyleInstructions($style, $language) {
        $instructions = [
            'az' => [
                'modern' => "Müasir, canlı və dostcasına yazın. Gənc nəslin başa düşəcəyi, lakin ədəbiyyat keyfiyyətini saxlayan dildə.",
                'academic' => "Elmi-tədqiqat üslubunda, faktlara əsaslanan, dərin təhlilli yazın. Mütəxəssis oxucu üçün nəzərdə tutulmuş keyfiyyətdə.",
                'poetic' => "Şeir ahəngində, təsvirli və metaforik yazın. Hər söz seçimi incə və poetik olsun.",
                'casual' => "Sərbəst, doğal və rahat oxunan üslubda yazın. Sıravi söhbət kimi, lakin məzmunlu."
            ],
            'en' => [
                'modern' => "Write in a modern, lively, and friendly tone. Use language that young people can understand while maintaining literary quality.",
                'academic' => "Write in an academic research style, fact-based with deep analysis. Quality intended for expert readers.",
                'poetic' => "Write in poetic rhythm, descriptive and metaphorical. Let every word choice be refined and poetic.",
                'casual' => "Write in a free, natural, and easily readable style. Like casual conversation, but meaningful."
            ]
        ];
        
        return $instructions[$language][$style] ?? $instructions['az']['modern'];
    }
    
    /**
     * Build user prompt with context
     */
    private function buildUserPrompt($text, $creativity, $language, $style) {
        // Detect if it's a name or general text
        $isName = $this->detectIfName($text);
        
        if ($isName && $language === 'az') {
            return "Bu ad haqqında yaradıcı və orijinal məqalə yazın: \"$text\"

Tələblər:
- Adın mənası və mənşəyi haqqında
- Azərbaycan mədəniyyətindəki yeri
- Tarixi və müasir istifadəsi
- Mədəni əhəmiyyəti
- Tamamilə orijinal olmalıdır
- Yaradıcılıq səviyyəsi: $creativity
- Yazı üslubu: $style

Minimum 150-200 söz yazın. Hər cümləni fərqli və yaradıcı qurun.";
        } elseif ($isName && $language === 'en') {
            return "Write a creative and original article about this name: \"$text\"

Requirements:
- Meaning and origin of the name
- Its place in Azerbaijani culture
- Historical and modern usage
- Cultural significance
- Must be completely original
- Creativity level: $creativity
- Writing style: $style

Write 150-200 words minimum. Make every sentence different and creative.";
        } else {
            // General text rewriting
            if ($language === 'az') {
                return "Bu mətni tamamilə yenidən yazın, yaradıcı və orijinal edin:

\"$text\"

Tələblər:
- Məzmunu saxlayın, ifadəni tamamilə dəyişdirin
- Hər cümləni fərqli qurun
- Yaradıcılıq səviyyəsi: $creativity
- Yazı üslubu: $style
- Plagiat olmamalıdır";
            } else {
                return "Completely rewrite this text, make it creative and original:

\"$text\"

Requirements:
- Keep the meaning, completely change the expression
- Structure every sentence differently
- Creativity level: $creativity
- Writing style: $style
- Must not be plagiarized";
            }
        }
    }
    
    /**
     * Detect if input is likely a name
     */
    private function detectIfName($text) {
        $words = explode(' ', trim($text));
        return count($words) <= 3 && preg_match('/^[a-zA-ZəöüçşığĞÜÇŞıİÖƏ\s]+$/', $text);
    }
    
    /**
     * Get temperature based on creativity level
     */
    private function getTemperature($creativity) {
        $temperatures = [
            'conservative' => 0.3,
            'balanced' => 0.7,
            'creative' => 0.9,
            'poetic' => 1.0
        ];
        
        return $temperatures[$creativity] ?? 0.7;
    }
    
    /**
     * Calculate max tokens based on input
     */
    private function calculateMaxTokens($text) {
        $inputLength = strlen($text);
        if ($inputLength < 50) return 500;  // Short input, longer output for names
        if ($inputLength < 200) return 800;
        return min(1500, $inputLength * 4);
    }
    
    /**
     * Apply anti-detection techniques
     */
    private function applyAntiDetection($text, $creativity) {
        // Add subtle variations
        if ($creativity === 'creative' || $creativity === 'poetic') {
            // Add more natural imperfections for higher creativity
            $text = $this->addNaturalVariations($text);
        }
        
        return $text;
    }
    
    /**
     * Add natural variations to text
     */
    private function addNaturalVariations($text) {
        // Add occasional natural hesitations or emphasis (very subtle)
        $variations = [
            '. Həqiqətən, ' => '. ',
            '. Əslində, ' => '. ',
            ' - yəni ' => ', ',
            ' (başqa sözlə) ' => ', '
        ];
        
        // Only apply one variation randomly and rarely
        if (rand(1, 100) <= 15) { // 15% chance
            $keys = array_keys($variations);
            $randomKey = $keys[array_rand($keys)];
            $text = str_replace($randomKey, $variations[$randomKey], $text);
        }
        
        return $text;
    }
    
    /**
     * Make API call to OpenAI
     */
    private function callOpenAI($systemPrompt, $userPrompt, $temperature, $maxTokens) {
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];
        
        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
            'frequency_penalty' => 0.3,
            'presence_penalty' => 0.1
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => OPENAI_API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // Force HTTP/1.1 to avoid HTTP/2 issues
            CURLOPT_ENCODING => '', // Enable all supported encodings
            CURLOPT_USERAGENT => 'AI-Name-Rewriter/1.0',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            $curlError = curl_error($ch);
            $curlErrno = curl_errno($ch);
            logError("OpenAI Handler Error: CURL Error (Code: $curlErrno): $curlError");
            throw new Exception('CURL Error: ' . $curlError);
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            $errorResponse = json_decode($response, true);
            $errorMessage = $errorResponse['error']['message'] ?? 'Unknown API error';
            throw new Exception("OpenAI API Error ($httpCode): $errorMessage");
        }
        
        return json_decode($response, true);
    }
}
?>