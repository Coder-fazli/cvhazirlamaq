<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#FF719A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="light-content">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    
    <!-- Direct CSS link for mobile compatibility -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css?v=<?php echo time(); ?>" type="text/css" media="all" />
    
    <!-- Enhanced Critical CSS for mobile and emoji support -->
    <style>
    :root {
        --primary-color: #FF719A;
        --accent-color: #FF8FA3;
        --text-light: #FFFFFF;
        --text-dark: #2C2C2C;
        --vh: 1vh;
    }
    
    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; 
    }
    
    html {
        height: 100vh;
        height: calc(var(--vh, 1vh) * 100);
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
    }
    
    body { 
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji', Roboto, sans-serif;
        overflow-x: hidden;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        min-height: 100vh;
        min-height: calc(var(--vh, 1vh) * 100);
        -webkit-user-select: none;
        -webkit-touch-callout: none;
        -webkit-tap-highlight-color: transparent;
    }
    
    .quiz-gradient { 
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%); 
        min-height: 100vh; 
        min-height: calc(var(--vh, 1vh) * 100);
        width: 100%; 
        position: relative; 
    }
    
    .landing-screen { 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
        padding: 30px; 
        min-height: 100vh; 
        min-height: calc(var(--vh, 1vh) * 100);
        text-align: center; 
    }
    
    .quiz-title { 
        font-size: 42px; 
        font-weight: 800; 
        color: var(--text-light); 
        margin-bottom: 12px; 
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); 
    }
    
    .start-button { 
        background: var(--text-light); 
        color: var(--primary-color); 
        padding: 20px 70px; 
        border-radius: 35px; 
        border: none; 
        font-size: 22px; 
        font-weight: 700; 
        cursor: pointer; 
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3); 
        margin: 30px 0;
        transition: transform 0.2s ease;
        -webkit-appearance: none;
        -webkit-tap-highlight-color: transparent;
    }
    
    .start-button:active {
        transform: scale(0.95);
    }
    
    .hidden { 
        display: none !important; 
    }
    
    /* Emoji and icon support */
    .feature-icon,
    .gender-emoji,
    .settings-button {
        text-rendering: optimizeLegibility;
        -webkit-font-feature-settings: "liga";
        font-feature-settings: "liga";
    }
    
    @media (max-width: 480px) {
        .landing-screen {
            padding: 20px 15px;
        }
        .quiz-title {
            font-size: 32px;
        }
        .start-button {
            padding: 16px 40px;
            font-size: 18px;
        }
    }
    </style>
    
    <script>
        // Fix mobile viewport height
        function setVH() {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', vh + 'px');
        }
        setVH();
        window.addEventListener('resize', setVH);
        window.addEventListener('orientationchange', setVH);
    </script>
    
    <?php wp_head(); ?>
</head>
<body <?php body_class('quiz-page'); ?>>