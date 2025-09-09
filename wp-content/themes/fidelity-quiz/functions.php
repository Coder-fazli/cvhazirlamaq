<?php
/**
 * Fidelity Quiz Theme Functions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function fidelity_quiz_setup() {
    // Add theme support for post thumbnails
    add_theme_support('post-thumbnails');
    
    // Add theme support for title tag
    add_theme_support('title-tag');
    
    // Add theme support for HTML5
    add_theme_support('html5', array(
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption'
    ));
    
    // Add theme support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
}
add_action('after_setup_theme', 'fidelity_quiz_setup');

/**
 * Enqueue Scripts and Styles
 */
function fidelity_quiz_scripts() {
    // Main stylesheet with cache busting
    wp_enqueue_style('fidelity-quiz-style', get_stylesheet_uri(), array(), time());
    
    // Load quiz-data.js for our beautiful new design
    wp_enqueue_script('fidelity-quiz-data', get_template_directory_uri() . '/quiz-data.js', array(), time(), false);
    
    // Add REST API URL and nonce for AJAX calls
    wp_localize_script('wp-api', 'wpApiSettings', array(
        'root' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
    
    // Add viewport meta tag for mobile
    add_action('wp_head', 'fidelity_quiz_viewport_meta');
}
add_action('wp_enqueue_scripts', 'fidelity_quiz_scripts');

/**
 * Add viewport meta tag
 */
function fidelity_quiz_viewport_meta() {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">' . "\n";
}

/**
 * Remove admin bar for clean mobile experience
 */
function fidelity_quiz_remove_admin_bar() {
    if (!current_user_can('manage_options')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'fidelity_quiz_remove_admin_bar');

/**
 * Custom Quiz Data Endpoints (for AJAX functionality)
 */
function fidelity_quiz_ajax_endpoints() {
    // Register AJAX endpoints for logged in and non-logged in users
    add_action('wp_ajax_get_quiz_questions', 'fidelity_quiz_get_questions');
    add_action('wp_ajax_nopriv_get_quiz_questions', 'fidelity_quiz_get_questions');
    
    add_action('wp_ajax_save_quiz_result', 'fidelity_quiz_save_result');
    add_action('wp_ajax_nopriv_save_quiz_result', 'fidelity_quiz_save_result');
}
add_action('init', 'fidelity_quiz_ajax_endpoints');

/**
 * Get quiz questions via AJAX
 */
function fidelity_quiz_get_questions() {
    $gender = sanitize_text_field($_POST['gender'] ?? 'male');
    
    // Sample questions - in a real implementation, these would come from the database
    $male_questions = array(
        array(
            'id' => 'q1',
            'text' => 'How often do you check your partner\'s phone or social media without their permission?',
            'topic' => 'phone'
        ),
        array(
            'id' => 'q2',
            'text' => 'How often do you feel jealous when your partner talks to people of the opposite sex?',
            'topic' => 'trust'
        ),
        array(
            'id' => 'q3',
            'text' => 'How often do you hide conversations or interactions from your partner?',
            'topic' => 'communication'
        ),
        array(
            'id' => 'q4',
            'text' => 'How often do you feel the need to know where your partner is at all times?',
            'topic' => 'trust'
        ),
        array(
            'id' => 'q5',
            'text' => 'How often do you go through your partner\'s personal belongings?',
            'topic' => 'trust'
        )
    );
    
    $female_questions = array(
        array(
            'id' => 'q1f',
            'text' => 'How often do you check your partner\'s phone or social media without their permission?',
            'topic' => 'phone'
        ),
        array(
            'id' => 'q2f',
            'text' => 'How often do you feel jealous when your partner talks to people of the opposite sex?',
            'topic' => 'trust'
        ),
        array(
            'id' => 'q3f',
            'text' => 'How often do you hide conversations or interactions from your partner?',
            'topic' => 'communication'
        ),
        array(
            'id' => 'q4f',
            'text' => 'How often do you feel the need to know where your partner is at all times?',
            'topic' => 'trust'
        ),
        array(
            'id' => 'q5f',
            'text' => 'How often do you go through your partner\'s personal belongings?',
            'topic' => 'trust'
        )
    );
    
    $questions = ($gender === 'female') ? $female_questions : $male_questions;
    
    wp_send_json_success($questions);
}

/**
 * Save quiz result via AJAX
 */
function fidelity_quiz_save_result() {
    $score = intval($_POST['score'] ?? 0);
    $risk_level = sanitize_text_field($_POST['risk_level'] ?? 'low');
    $gender = sanitize_text_field($_POST['gender'] ?? 'male');
    
    // In a real implementation, you might save this to the database
    // For now, just return success
    
    wp_send_json_success(array(
        'message' => 'Result saved successfully',
        'score' => $score,
        'risk_level' => $risk_level
    ));
}

/**
 * Custom body class for quiz pages
 */
function fidelity_quiz_body_class($classes) {
    if (is_home() || is_front_page()) {
        $classes[] = 'quiz-page';
    }
    return $classes;
}
add_filter('body_class', 'fidelity_quiz_body_class');

/**
 * Disable WordPress default styles for clean mobile experience
 */
function fidelity_quiz_dequeue_styles() {
    // Remove default WordPress styles that might interfere
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', 'fidelity_quiz_dequeue_styles', 100);

/**
 * Add theme support for mobile app-like experience
 */
function fidelity_quiz_mobile_meta() {
    echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="light-content">' . "\n";
    echo '<meta name="theme-color" content="#FF719A">' . "\n";
}
add_action('wp_head', 'fidelity_quiz_mobile_meta');

/**
 * Remove unnecessary WordPress features for quiz app
 */
function fidelity_quiz_remove_wp_features() {
    // Remove comment support
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
    
    // Remove trackbacks
    remove_post_type_support('post', 'trackbacks');
}
add_action('init', 'fidelity_quiz_remove_wp_features');

/**
 * Custom CSS for mobile viewport fixes
 */
function fidelity_quiz_mobile_css() {
    echo '<style>
        html {
            height: 100vh;
            height: calc(var(--vh, 1vh) * 100);
        }
        
        body {
            min-height: 100vh;
            min-height: calc(var(--vh, 1vh) * 100);
            overflow-x: hidden;
        }
        
        @media screen and (max-width: 768px) {
            body {
                -webkit-user-select: none;
                -webkit-touch-callout: none;
                -webkit-tap-highlight-color: transparent;
            }
        }
    </style>';
}
add_action('wp_head', 'fidelity_quiz_mobile_css');

/**
 * Redirect to quiz page on theme activation
 */
function fidelity_quiz_activation_redirect() {
    if (is_admin() && isset($_GET['activated'])) {
        wp_redirect(home_url('/'));
        exit;
    }
}
add_action('after_switch_theme', 'fidelity_quiz_activation_redirect');

/**
 * Quiz Flow Management
 */

// Check if we should show gender selection based on URL parameter
function fidelity_quiz_should_show_gender_selection() {
    return isset($_GET['step']) && $_GET['step'] === 'gender';
}

// Get the gender selection URL
function fidelity_quiz_get_gender_selection_url() {
    return home_url('/?step=gender');
}

// Handle quiz flow redirects
function fidelity_quiz_handle_flow_redirects() {
    // Only run on frontend
    if (is_admin()) {
        return;
    }
    
    // Add JavaScript variables and functions for quiz flow
    add_action('wp_head', 'fidelity_quiz_add_flow_js');
}
add_action('init', 'fidelity_quiz_handle_flow_redirects');

// Add JavaScript for quiz flow
function fidelity_quiz_add_flow_js() {
    ?>
    <script>
        // Quiz flow constants and functions
        const GENDER_SELECTION_URL = '<?php echo fidelity_quiz_get_gender_selection_url(); ?>';
        
        // Clean quiz start function
        window.beginQuiz = function() {
            window.location.assign(GENDER_SELECTION_URL);
        };
        
        // Auto-show gender selection if parameter is present
        <?php if (fidelity_quiz_should_show_gender_selection()): ?>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Auto-showing gender selection from PHP');
            const genderSelection = document.getElementById('gender-selection');
            const landingScreen = document.getElementById('quiz-app');
            
            if (genderSelection) {
                genderSelection.classList.remove('hidden');
                genderSelection.style.display = 'block';
            }
            
            if (landingScreen) {
                landingScreen.classList.add('hidden');
                landingScreen.style.display = 'none';
            }
        });
        <?php endif; ?>
        
        // Question image handling functions
        window.setQuestionImage = function(imageUrl) {
            const container = document.getElementById('question-image-container');
            const image = document.getElementById('question-image');
            const placeholder = document.getElementById('question-image-placeholder');
            
            if (imageUrl && imageUrl.trim() !== '') {
                image.src = imageUrl;
                image.style.display = 'block';
                image.onload = function() {
                    container.classList.add('has-image');
                };
                image.onerror = function() {
                    // If image fails to load, show placeholder
                    container.classList.remove('has-image');
                    image.style.display = 'none';
                };
            } else {
                // No image provided, show placeholder
                container.classList.remove('has-image');
                image.style.display = 'none';
            }
        };
        
        window.clearQuestionImage = function() {
            const container = document.getElementById('question-image-container');
            const image = document.getElementById('question-image');
            
            container.classList.remove('has-image');
            image.style.display = 'none';
            image.src = '';
        };
    </script>
    <?php
}

// Add body classes for quiz flow states
function fidelity_quiz_flow_body_class($classes) {
    if (fidelity_quiz_should_show_gender_selection()) {
        $classes[] = 'showing-gender-selection';
    }
    return $classes;
}
add_filter('body_class', 'fidelity_quiz_flow_body_class');

// Template redirect handling
function fidelity_quiz_template_redirect() {
    // Handle quiz-start page redirects to correct template
    if (is_page('quiz-start')) {
        // Let WordPress handle this naturally with page-quiz-start.php
        return;
    }
}
add_action('template_redirect', 'fidelity_quiz_template_redirect');

/**
 * =============================================================================
 * QUIZ MANAGEMENT SYSTEM
 * =============================================================================
 */

/**
 * Register Quiz Question Custom Post Type
 */
function register_quiz_question_post_type() {
    $labels = array(
        'name'               => 'Questions',
        'singular_name'      => 'Question',
        'menu_name'          => 'Questions',
        'add_new'            => 'Add New Question',
        'add_new_item'       => 'Add New Question',
        'edit_item'          => 'Edit Question',
        'new_item'           => 'New Question',
        'view_item'          => 'View Question',
        'search_items'       => 'Search Questions',
        'not_found'          => 'No questions found',
        'not_found_in_trash' => 'No questions found in trash',
        'all_items'          => 'All Questions',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-editor-help',
        'menu_position'       => 25,
        'capability_type'     => 'post',
        'capabilities'        => array(
            'edit_post'          => 'manage_options',
            'read_post'          => 'manage_options',
            'delete_post'        => 'manage_options',
            'edit_posts'         => 'manage_options',
            'edit_others_posts'  => 'manage_options',
            'delete_posts'       => 'manage_options',
            'publish_posts'      => 'manage_options',
            'read_private_posts' => 'manage_options',
        ),
        'supports'            => array('title', 'editor', 'thumbnail', 'revisions'),
        'has_archive'         => false,
        'rewrite'             => false,
        'show_in_rest'        => true,
        'taxonomies'          => array('quiz_gender', 'quiz_topic'),
    );

    register_post_type('quiz_question', $args);
}
add_action('init', 'register_quiz_question_post_type');

/**
 * Register Quiz Gender Taxonomy
 */
function register_quiz_gender_taxonomy() {
    $labels = array(
        'name'              => 'Gender',
        'singular_name'     => 'Gender',
        'search_items'      => 'Search Genders',
        'all_items'         => 'All Genders',
        'edit_item'         => 'Edit Gender',
        'update_item'       => 'Update Gender',
        'add_new_item'      => 'Add New Gender',
        'new_item_name'     => 'New Gender Name',
        'menu_name'         => 'Gender',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud'     => false,
        'show_in_rest'      => true,
        'meta_box_cb'       => 'quiz_gender_meta_box',
    );

    register_taxonomy('quiz_gender', array('quiz_question'), $args);
}
add_action('init', 'register_quiz_gender_taxonomy');

/**
 * Register Quiz Topic Taxonomy
 */
function register_quiz_topic_taxonomy() {
    $labels = array(
        'name'              => 'Topics',
        'singular_name'     => 'Topic',
        'search_items'      => 'Search Topics',
        'all_items'         => 'All Topics',
        'edit_item'         => 'Edit Topic',
        'update_item'       => 'Update Topic',
        'add_new_item'      => 'Add New Topic',
        'new_item_name'     => 'New Topic Name',
        'menu_name'         => 'Topics',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud'     => false,
        'show_in_rest'      => true,
    );

    register_taxonomy('quiz_topic', array('quiz_question'), $args);
}
add_action('init', 'register_quiz_topic_taxonomy');

/**
 * Create default taxonomy terms
 */
function create_default_quiz_terms() {
    // Create gender terms
    if (!term_exists('male', 'quiz_gender')) {
        wp_insert_term('Male', 'quiz_gender', array('slug' => 'male'));
    }
    if (!term_exists('female', 'quiz_gender')) {
        wp_insert_term('Female', 'quiz_gender', array('slug' => 'female'));
    }

    // Create topic terms
    $topics = array('phone', 'social', 'work', 'intimacy', 'communication', 'finance', 'trust');
    foreach ($topics as $topic) {
        if (!term_exists($topic, 'quiz_topic')) {
            wp_insert_term(ucfirst($topic), 'quiz_topic', array('slug' => $topic));
        }
    }
}
add_action('init', 'create_default_quiz_terms', 20);

/**
 * Register custom meta fields with REST API
 */
function register_quiz_question_meta_with_rest() {
    // Register quiz options (array of strings)
    register_post_meta('quiz_question', '_qq_options', array(
        'show_in_rest' => array(
            'schema' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                ),
                'context' => array('view', 'edit')
            )
        ),
        'single' => true,
        'type' => 'array',
        'description' => 'Question answer options',
        'sanitize_callback' => function($value) {
            return is_array($value) ? array_map('sanitize_text_field', $value) : array();
        },
        'auth_callback' => '__return_true',
    ));

    // Register quiz option scores (array of integers)
    register_post_meta('quiz_question', '_qq_option_scores', array(
        'show_in_rest' => array(
            'schema' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'integer',
                    'minimum' => 0,
                    'maximum' => 4
                ),
                'context' => array('view', 'edit')
            )
        ),
        'single' => true,
        'type' => 'array',
        'description' => 'Question answer scores',
        'sanitize_callback' => function($value) {
            return is_array($value) ? array_map('intval', $value) : array();
        },
        'auth_callback' => '__return_true',
    ));

    // Register question order
    register_post_meta('quiz_question', '_qq_order', array(
        'show_in_rest' => array(
            'schema' => array(
                'type' => 'integer',
                'minimum' => 1,
                'maximum' => 100,
                'context' => array('view', 'edit')
            )
        ),
        'single' => true,
        'type' => 'integer',
        'description' => 'Question display order',
        'sanitize_callback' => 'absint',
        'auth_callback' => '__return_true',
    ));

    // Register active flag
    register_post_meta('quiz_question', '_qq_active', array(
        'show_in_rest' => array(
            'schema' => array(
                'type' => 'boolean',
                'context' => array('view', 'edit')
            )
        ),
        'single' => true,
        'type' => 'boolean',
        'description' => 'Whether question is active',
        'sanitize_callback' => function($value) {
            return $value === '1' || $value === true ? true : false;
        },
        'auth_callback' => '__return_true',
    ));

    // Register question weight
    register_post_meta('quiz_question', '_qq_weight', array(
        'show_in_rest' => array(
            'schema' => array(
                'type' => 'number',
                'minimum' => 0.1,
                'maximum' => 5.0,
                'context' => array('view', 'edit')
            )
        ),
        'single' => true,
        'type' => 'number',
        'description' => 'Question scoring weight',
        'sanitize_callback' => 'floatval',
        'auth_callback' => '__return_true',
    ));

    // Register question hint
    register_post_meta('quiz_question', '_qq_hint', array(
        'show_in_rest' => array(
            'schema' => array(
                'type' => 'string',
                'context' => array('view', 'edit')
            )
        ),
        'single' => true,
        'type' => 'string',
        'description' => 'Optional question hint',
        'sanitize_callback' => 'sanitize_textarea_field',
        'auth_callback' => '__return_true',
    ));
}
add_action('init', 'register_quiz_question_meta_with_rest', 25);

/**
 * Custom meta box for gender selection (radio buttons)
 */
function quiz_gender_meta_box($post, $box) {
    $terms = get_terms(array(
        'taxonomy' => 'quiz_gender',
        'hide_empty' => false,
    ));
    
    $current = wp_get_object_terms($post->ID, 'quiz_gender');
    $current_id = !empty($current) ? $current[0]->term_id : 0;
    
    echo '<div id="taxonomy-quiz_gender">';
    echo '<ul>';
    foreach ($terms as $term) {
        echo '<li>';
        echo '<label><input type="radio" name="tax_input[quiz_gender][]" value="' . $term->term_id . '"';
        if ($current_id == $term->term_id) echo ' checked="checked"';
        echo '> ' . $term->name . '</label>';
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
}

/**
 * Add Quiz Question Meta Boxes
 */
function add_quiz_question_meta_boxes() {
    add_meta_box(
        'quiz_question_options',
        'Question Options & Scoring',
        'quiz_question_options_callback',
        'quiz_question',
        'normal',
        'high'
    );

    add_meta_box(
        'quiz_question_settings',
        'Question Settings',
        'quiz_question_settings_callback',
        'quiz_question',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_quiz_question_meta_boxes');

/**
 * Question Options Meta Box Callback
 */
function quiz_question_options_callback($post) {
    wp_nonce_field('quiz_question_meta_nonce', 'quiz_question_meta_nonce');
    
    $options = get_post_meta($post->ID, '_qq_options', true);
    $scores = get_post_meta($post->ID, '_qq_option_scores', true);
    
    // Default to 5 options if empty
    if (empty($options)) {
        $options = array('Never', 'Rarely', 'Sometimes', 'Often', 'Always');
        $scores = array(0, 1, 2, 3, 4);
    }
    
    echo '<table class="form-table">';
    echo '<tr><th>Answer Options & Scores</th><td>';
    echo '<div id="quiz-options-container">';
    
    for ($i = 0; $i < 5; $i++) {
        $option_value = isset($options[$i]) ? esc_attr($options[$i]) : '';
        $score_value = isset($scores[$i]) ? intval($scores[$i]) : $i;
        
        echo '<div class="quiz-option-row" style="margin-bottom: 10px; display: flex; align-items: center;">';
        echo '<span style="width: 30px; font-weight: bold;">' . ($i + 1) . '.</span>';
        echo '<input type="text" name="qq_options[]" value="' . $option_value . '" placeholder="Answer option ' . ($i + 1) . '" style="width: 60%; margin-right: 10px;">';
        echo '<label style="margin-right: 5px;">Score:</label>';
        echo '<input type="number" name="qq_option_scores[]" value="' . $score_value . '" min="0" max="4" style="width: 60px;">';
        echo '</div>';
    }
    
    echo '</div>';
    echo '<p class="description">Enter up to 5 answer options with their corresponding scores (0-4). Leave empty if fewer options needed.</p>';
    echo '</td></tr>';
    echo '</table>';
}

/**
 * Question Settings Meta Box Callback
 */
function quiz_question_settings_callback($post) {
    $order = get_post_meta($post->ID, '_qq_order', true);
    $active = get_post_meta($post->ID, '_qq_active', true);
    $hint = get_post_meta($post->ID, '_qq_hint', true);
    $weight = get_post_meta($post->ID, '_qq_weight', true);
    
    // Default values
    if ($active === '') $active = '1';
    if ($weight === '') $weight = '1';
    
    echo '<table class="form-table">';
    
    echo '<tr><th><label for="qq_order">Display Order</label></th>';
    echo '<td><input type="number" id="qq_order" name="qq_order" value="' . esc_attr($order) . '" min="1" max="50" style="width: 100px;">';
    echo '<p class="description">Order within gender group (1-50)</p></td></tr>';
    
    echo '<tr><th><label for="qq_active">Active</label></th>';
    echo '<td><label><input type="checkbox" id="qq_active" name="qq_active" value="1"' . checked($active, '1', false) . '> Include in quiz</label></td></tr>';
    
    echo '<tr><th><label for="qq_weight">Weight</label></th>';
    echo '<td><input type="number" id="qq_weight" name="qq_weight" value="' . esc_attr($weight) . '" min="0.1" max="5" step="0.1" style="width: 100px;">';
    echo '<p class="description">Scoring weight (default: 1.0)</p></td></tr>';
    
    echo '<tr><th><label for="qq_hint">Hint Text</label></th>';
    echo '<td><textarea id="qq_hint" name="qq_hint" rows="3" style="width: 100%;">' . esc_textarea($hint) . '</textarea>';
    echo '<p class="description">Optional hint shown under question</p></td></tr>';
    
    echo '</table>';
}

/**
 * Save Quiz Question Meta Data
 */
function save_quiz_question_meta($post_id) {
    // Check nonce
    if (!isset($_POST['quiz_question_meta_nonce']) || !wp_verify_nonce($_POST['quiz_question_meta_nonce'], 'quiz_question_meta_nonce')) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check post type
    if (get_post_type($post_id) !== 'quiz_question') {
        return;
    }

    // Save options and scores
    if (isset($_POST['qq_options'])) {
        $options = array_map('sanitize_text_field', $_POST['qq_options']);
        $options = array_filter($options, 'strlen'); // Remove empty values
        update_post_meta($post_id, '_qq_options', $options);
    }

    if (isset($_POST['qq_option_scores'])) {
        $scores = array_map('intval', $_POST['qq_option_scores']);
        $scores = array_slice($scores, 0, count($options)); // Match options length
        update_post_meta($post_id, '_qq_option_scores', $scores);
    }

    // Save settings
    $order = isset($_POST['qq_order']) ? intval($_POST['qq_order']) : '';
    $active = isset($_POST['qq_active']) ? '1' : '0';
    $hint = isset($_POST['qq_hint']) ? sanitize_textarea_field($_POST['qq_hint']) : '';
    $weight = isset($_POST['qq_weight']) ? floatval($_POST['qq_weight']) : 1.0;

    update_post_meta($post_id, '_qq_order', $order);
    update_post_meta($post_id, '_qq_active', $active);
    update_post_meta($post_id, '_qq_hint', $hint);
    update_post_meta($post_id, '_qq_weight', $weight);

    // Validate gender assignment
    $gender_terms = wp_get_object_terms($post_id, 'quiz_gender');
    if (count($gender_terms) !== 1) {
        // Force assignment to male if no gender or multiple genders
        wp_set_object_terms($post_id, 'male', 'quiz_gender');
        add_filter('redirect_post_location', function($location) {
            return add_query_arg('gender_warning', '1', $location);
        });
    }

    // Validate unique order within gender
    if ($order && !empty($gender_terms)) {
        $gender_slug = $gender_terms[0]->slug;
        $existing = get_posts(array(
            'post_type' => 'quiz_question',
            'post_status' => 'any',
            'meta_key' => '_qq_order',
            'meta_value' => $order,
            'tax_query' => array(
                array(
                    'taxonomy' => 'quiz_gender',
                    'field' => 'slug',
                    'terms' => $gender_slug,
                )
            ),
            'exclude' => array($post_id),
            'fields' => 'ids',
        ));

        if (!empty($existing)) {
            add_filter('redirect_post_location', function($location) use ($order) {
                return add_query_arg('order_warning', $order, $location);
            });
        }
    }
}
add_action('save_post', 'save_quiz_question_meta');

/**
 * Show admin notices for validation warnings
 */
function quiz_question_admin_notices() {
    if (isset($_GET['gender_warning'])) {
        echo '<div class="notice notice-warning is-dismissible"><p>Warning: Question must have exactly one gender assigned. Defaulted to Male.</p></div>';
    }
    
    if (isset($_GET['order_warning'])) {
        $order = intval($_GET['order_warning']);
        echo '<div class="notice notice-warning is-dismissible"><p>Warning: Order ' . $order . ' is already used by another question in this gender.</p></div>';
    }
}
add_action('admin_notices', 'quiz_question_admin_notices');

/**
 * Custom Admin Columns for Quiz Questions
 */
function quiz_question_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['gender'] = 'Gender';
    $new_columns['topics'] = 'Topics';
    $new_columns['order'] = 'Order';
    $new_columns['active'] = 'Active';
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_quiz_question_posts_columns', 'quiz_question_admin_columns');

/**
 * Fill custom admin columns
 */
function quiz_question_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'gender':
            $gender_terms = wp_get_object_terms($post_id, 'quiz_gender');
            if (!empty($gender_terms)) {
                echo '<span class="gender-' . $gender_terms[0]->slug . '">' . $gender_terms[0]->name . '</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'topics':
            $topic_terms = wp_get_object_terms($post_id, 'quiz_topic');
            if (!empty($topic_terms)) {
                $topics = array_map(function($term) { return $term->name; }, $topic_terms);
                echo implode(', ', $topics);
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'order':
            $order = get_post_meta($post_id, '_qq_order', true);
            echo $order ? $order : '<span style="color: #999;">—</span>';
            break;
            
        case 'active':
            $active = get_post_meta($post_id, '_qq_active', true);
            if ($active === '1') {
                echo '<span style="color: #46b450;">✓ Active</span>';
            } else {
                echo '<span style="color: #dc3232;">✗ Inactive</span>';
            }
            break;
    }
}
add_action('manage_quiz_question_posts_custom_column', 'quiz_question_admin_column_content', 10, 2);

/**
 * Make columns sortable
 */
function quiz_question_sortable_columns($columns) {
    $columns['gender'] = 'gender';
    $columns['order'] = 'order';
    $columns['active'] = 'active';
    return $columns;
}
add_filter('manage_edit-quiz_question_sortable_columns', 'quiz_question_sortable_columns');

/**
 * Handle custom column sorting
 */
function quiz_question_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');

    switch ($orderby) {
        case 'gender':
            $query->set('meta_key', '_quiz_gender_sort');
            $query->set('orderby', 'meta_value');
            break;
        case 'order':
            $query->set('meta_key', '_qq_order');
            $query->set('orderby', 'meta_value_num');
            break;
        case 'active':
            $query->set('meta_key', '_qq_active');
            $query->set('orderby', 'meta_value');
            break;
    }
}
add_action('pre_get_posts', 'quiz_question_column_orderby');

/**
 * Add gender and topic filters to admin
 */
function quiz_question_admin_filters() {
    global $typenow;
    
    if ($typenow !== 'quiz_question') {
        return;
    }

    // Gender filter
    $gender_terms = get_terms(array('taxonomy' => 'quiz_gender', 'hide_empty' => false));
    if (!empty($gender_terms)) {
        echo '<select name="gender_filter">';
        echo '<option value="">All Genders</option>';
        foreach ($gender_terms as $term) {
            $selected = isset($_GET['gender_filter']) && $_GET['gender_filter'] === $term->slug ? ' selected' : '';
            echo '<option value="' . $term->slug . '"' . $selected . '>' . $term->name . '</option>';
        }
        echo '</select>';
    }

    // Topic filter
    $topic_terms = get_terms(array('taxonomy' => 'quiz_topic', 'hide_empty' => false));
    if (!empty($topic_terms)) {
        echo '<select name="topic_filter">';
        echo '<option value="">All Topics</option>';
        foreach ($topic_terms as $term) {
            $selected = isset($_GET['topic_filter']) && $_GET['topic_filter'] === $term->slug ? ' selected' : '';
            echo '<option value="' . $term->slug . '"' . $selected . '>' . $term->name . '</option>';
        }
        echo '</select>';
    }

    // Active filter
    echo '<select name="active_filter">';
    echo '<option value="">All Questions</option>';
    echo '<option value="1"' . (isset($_GET['active_filter']) && $_GET['active_filter'] === '1' ? ' selected' : '') . '>Active Only</option>';
    echo '<option value="0"' . (isset($_GET['active_filter']) && $_GET['active_filter'] === '0' ? ' selected' : '') . '>Inactive Only</option>';
    echo '</select>';
}
add_action('restrict_manage_posts', 'quiz_question_admin_filters');

/**
 * Apply admin filters
 */
function quiz_question_apply_admin_filters($query) {
    global $pagenow, $typenow;
    
    if ($pagenow === 'edit.php' && $typenow === 'quiz_question' && $query->is_main_query()) {
        $tax_query = array('relation' => 'AND');

        // Gender filter
        if (isset($_GET['gender_filter']) && !empty($_GET['gender_filter'])) {
            $tax_query[] = array(
                'taxonomy' => 'quiz_gender',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['gender_filter']),
            );
        }

        // Topic filter
        if (isset($_GET['topic_filter']) && !empty($_GET['topic_filter'])) {
            $tax_query[] = array(
                'taxonomy' => 'quiz_topic',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['topic_filter']),
            );
        }

        if (count($tax_query) > 1) {
            $query->set('tax_query', $tax_query);
        }

        // Active filter
        if (isset($_GET['active_filter']) && $_GET['active_filter'] !== '') {
            $query->set('meta_key', '_qq_active');
            $query->set('meta_value', sanitize_text_field($_GET['active_filter']));
        }
    }
}
add_action('pre_get_posts', 'quiz_question_apply_admin_filters');

/**
 * Add Quick Edit fields
 */
function quiz_question_quick_edit_fields($column_name, $post_type) {
    if ($post_type !== 'quiz_question') return;
    
    static $printNonce = true;
    if ($printNonce) {
        $printNonce = false;
        wp_nonce_field('quiz_question_quick_edit', 'quiz_question_quick_edit_nonce');
    }

    switch ($column_name) {
        case 'order':
            ?>
            <fieldset class="inline-edit-col-left">
                <div class="inline-edit-col">
                    <label>
                        <span class="title">Order</span>
                        <span class="input-text-wrap">
                            <input type="number" name="qq_order" class="ptitle" value="" min="1" max="50">
                        </span>
                    </label>
                </div>
            </fieldset>
            <?php
            break;
            
        case 'active':
            ?>
            <fieldset class="inline-edit-col-left">
                <div class="inline-edit-col">
                    <label>
                        <input type="checkbox" name="qq_active" value="1">
                        <span class="checkbox-title">Active</span>
                    </label>
                </div>
            </fieldset>
            <?php
            break;
    }
}
add_action('quick_edit_custom_box', 'quiz_question_quick_edit_fields', 10, 2);

/**
 * Save Quick Edit data
 */
function save_quiz_question_quick_edit($post_id) {
    if (!isset($_POST['quiz_question_quick_edit_nonce']) || !wp_verify_nonce($_POST['quiz_question_quick_edit_nonce'], 'quiz_question_quick_edit')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (get_post_type($post_id) !== 'quiz_question') {
        return;
    }

    if (isset($_POST['qq_order'])) {
        update_post_meta($post_id, '_qq_order', intval($_POST['qq_order']));
    }

    $active = isset($_POST['qq_active']) ? '1' : '0';
    update_post_meta($post_id, '_qq_active', $active);
}
add_action('save_post', 'save_quiz_question_quick_edit');

/**
 * Bulk actions for quiz questions
 */
function quiz_question_bulk_actions($bulk_actions) {
    $bulk_actions['activate_questions'] = 'Activate';
    $bulk_actions['deactivate_questions'] = 'Deactivate';
    return $bulk_actions;
}
add_filter('bulk_actions-edit-quiz_question', 'quiz_question_bulk_actions');

/**
 * Handle bulk actions
 */
function handle_quiz_question_bulk_actions($redirect_to, $doaction, $post_ids) {
    if ($doaction !== 'activate_questions' && $doaction !== 'deactivate_questions') {
        return $redirect_to;
    }

    $count = 0;
    foreach ($post_ids as $post_id) {
        if (get_post_type($post_id) === 'quiz_question') {
            $value = ($doaction === 'activate_questions') ? '1' : '0';
            update_post_meta($post_id, '_qq_active', $value);
            $count++;
        }
    }

    $redirect_to = add_query_arg('bulk_' . $doaction, $count, $redirect_to);
    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-quiz_question', 'handle_quiz_question_bulk_actions', 10, 3);

/**
 * Show bulk action notices
 */
function quiz_question_bulk_action_notices() {
    if (isset($_REQUEST['bulk_activate_questions'])) {
        $count = intval($_REQUEST['bulk_activate_questions']);
        printf('<div class="notice notice-success is-dismissible"><p>%d questions activated.</p></div>', $count);
    }

    if (isset($_REQUEST['bulk_deactivate_questions'])) {
        $count = intval($_REQUEST['bulk_deactivate_questions']);
        printf('<div class="notice notice-success is-dismissible"><p>%d questions deactivated.</p></div>', $count);
    }
}
add_action('admin_notices', 'quiz_question_bulk_action_notices');

/**
 * Add Quiz Import submenu
 */
function quiz_questions_add_import_menu() {
    add_submenu_page(
        'edit.php?post_type=quiz_question',
        'Import Questions',
        'Import',
        'manage_options',
        'quiz-import',
        'quiz_questions_import_page'
    );
}
add_action('admin_menu', 'quiz_questions_add_import_menu');

/**
 * Quiz Import Page
 */
function quiz_questions_import_page() {
    if (isset($_POST['import_questions']) && wp_verify_nonce($_POST['quiz_import_nonce'], 'quiz_import_action')) {
        $result = process_quiz_import();
        echo '<div class="notice notice-success"><p>' . $result . '</p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1>Import Quiz Questions</h1>
        
        <div class="card">
            <h2>Import from JSON Data</h2>
            <p>Paste your existing quiz data in JSON format below, or upload a JSON file.</p>
            
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('quiz_import_action', 'quiz_import_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">JSON Data</th>
                        <td>
                            <textarea name="json_data" rows="20" cols="100" placeholder='Example format:
{
  "male": [
    {
      "id": "q1",
      "text": "How often do you check your partner\'s phone?",
      "topic": "phone",
      "options": ["Never", "Rarely", "Sometimes", "Often", "Always"],
      "scores": [0, 1, 2, 3, 4],
      "order": 1,
      "image": "https://example.com/image.jpg"
    }
  ],
  "female": [...]
}'></textarea>
                            <p class="description">Paste your quiz questions data here in JSON format.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Or Upload File</th>
                        <td>
                            <input type="file" name="json_file" accept=".json,.txt">
                            <p class="description">Upload a JSON file containing your quiz data.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Import Mode</th>
                        <td>
                            <label>
                                <input type="radio" name="import_mode" value="add" checked>
                                Add new questions (skip if title already exists)
                            </label><br>
                            <label>
                                <input type="radio" name="import_mode" value="update">
                                Update existing questions (match by title and gender)
                            </label><br>
                            <label>
                                <input type="radio" name="import_mode" value="replace">
                                Replace all questions (delete existing first)
                            </label>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="import_questions" class="button-primary" value="Import Questions">
                </p>
            </form>
        </div>
        
        <div class="card">
            <h2>Sample Data</h2>
            <p>Here's a sample of your current questions to help you format the import data:</p>
            
            <?php
            // Show sample current questions
            $sample_male = get_posts(array(
                'post_type' => 'quiz_question',
                'posts_per_page' => 2,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'quiz_gender',
                        'field' => 'slug',
                        'terms' => 'male',
                    )
                )
            ));
            
            if (!empty($sample_male)) {
                echo '<h3>Current Male Questions (sample):</h3>';
                echo '<pre>';
                foreach ($sample_male as $post) {
                    $options = get_post_meta($post->ID, '_qq_options', true);
                    $scores = get_post_meta($post->ID, '_qq_option_scores', true);
                    $order = get_post_meta($post->ID, '_qq_order', true);
                    $topics = wp_get_object_terms($post->ID, 'quiz_topic');
                    
                    echo "Title: " . $post->post_title . "\n";
                    echo "Options: " . json_encode($options) . "\n";
                    echo "Scores: " . json_encode($scores) . "\n";
                    echo "Order: " . $order . "\n";
                    echo "Topic: " . (!empty($topics) ? $topics[0]->slug : 'none') . "\n\n";
                }
                echo '</pre>';
            } else {
                echo '<p>No existing questions found. You can start by importing your first set of questions.</p>';
            }
            ?>
        </div>
    </div>
    <?php
}

/**
 * Process quiz import
 */
function process_quiz_import() {
    $json_data = '';
    
    // Get data from textarea or file
    if (!empty($_POST['json_data'])) {
        $json_data = stripslashes($_POST['json_data']);
    } elseif (!empty($_FILES['json_file']['tmp_name'])) {
        $json_data = file_get_contents($_FILES['json_file']['tmp_name']);
    }
    
    if (empty($json_data)) {
        return 'Error: No data provided.';
    }
    
    $data = json_decode($json_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return 'Error: Invalid JSON format. ' . json_last_error_msg();
    }
    
    $import_mode = sanitize_text_field($_POST['import_mode']);
    $stats = array('created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0);
    
    // Replace mode: delete existing questions first
    if ($import_mode === 'replace') {
        $existing = get_posts(array(
            'post_type' => 'quiz_question',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'fields' => 'ids'
        ));
        
        foreach ($existing as $post_id) {
            wp_delete_post($post_id, true);
        }
    }
    
    // Process each gender
    foreach ($data as $gender => $questions) {
        if (!in_array($gender, array('male', 'female'))) {
            continue;
        }
        
        foreach ($questions as $question_data) {
            try {
                $result = import_single_question($question_data, $gender, $import_mode);
                $stats[$result]++;
            } catch (Exception $e) {
                $stats['errors']++;
            }
        }
    }
    
    return sprintf(
        'Import complete! Created: %d, Updated: %d, Skipped: %d, Errors: %d',
        $stats['created'], $stats['updated'], $stats['skipped'], $stats['errors']
    );
}

/**
 * Import a single question
 */
function import_single_question($data, $gender, $mode) {
    $title = sanitize_text_field($data['text'] ?? $data['title'] ?? '');
    if (empty($title)) {
        throw new Exception('Missing question title');
    }
    
    // Check if question exists (by title and gender)
    $existing = get_posts(array(
        'post_type' => 'quiz_question',
        'title' => $title,
        'posts_per_page' => 1,
        'tax_query' => array(
            array(
                'taxonomy' => 'quiz_gender',
                'field' => 'slug',
                'terms' => $gender,
            )
        ),
        'fields' => 'ids'
    ));
    
    $post_exists = !empty($existing);
    $post_id = $post_exists ? $existing[0] : null;
    
    // Skip if exists and mode is 'add'
    if ($post_exists && $mode === 'add') {
        return 'skipped';
    }
    
    // Create or update post
    $post_data = array(
        'post_title' => $title,
        'post_content' => sanitize_textarea_field($data['content'] ?? ''),
        'post_type' => 'quiz_question',
        'post_status' => 'publish',
    );
    
    if ($post_exists && $mode === 'update') {
        $post_data['ID'] = $post_id;
        $result = wp_update_post($post_data);
        $action = 'updated';
    } else {
        $result = wp_insert_post($post_data);
        $post_id = $result;
        $action = 'created';
    }
    
    if (is_wp_error($result) || !$post_id) {
        throw new Exception('Failed to create/update post');
    }
    
    // Set gender taxonomy
    wp_set_object_terms($post_id, $gender, 'quiz_gender');
    
    // Set topic taxonomy
    if (!empty($data['topic'])) {
        $topic = sanitize_text_field($data['topic']);
        wp_set_object_terms($post_id, $topic, 'quiz_topic');
    }
    
    // Set meta fields
    if (!empty($data['options']) && is_array($data['options'])) {
        update_post_meta($post_id, '_qq_options', array_map('sanitize_text_field', $data['options']));
    }
    
    if (!empty($data['scores']) && is_array($data['scores'])) {
        update_post_meta($post_id, '_qq_option_scores', array_map('intval', $data['scores']));
    }
    
    update_post_meta($post_id, '_qq_order', intval($data['order'] ?? 0));
    update_post_meta($post_id, '_qq_active', isset($data['active']) ? ($data['active'] ? '1' : '0') : '1');
    update_post_meta($post_id, '_qq_hint', sanitize_textarea_field($data['hint'] ?? ''));
    update_post_meta($post_id, '_qq_weight', floatval($data['weight'] ?? 1.0));
    
    // Handle featured image
    if (!empty($data['image']) && filter_var($data['image'], FILTER_VALIDATE_URL)) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $image_id = media_sideload_image($data['image'], $post_id, $title, 'id');
        if (!is_wp_error($image_id)) {
            set_post_thumbnail($post_id, $image_id);
        }
    }
    
    return $action;
}

/**
 * =============================================================================
 * REST API ENDPOINTS
 * =============================================================================
 */

/**
 * Register custom REST API routes
 */
function register_quiz_rest_routes() {
    register_rest_route('quiz/v1', '/questions', array(
        'methods' => 'GET',
        'callback' => 'quiz_api_get_questions',
        'permission_callback' => '__return_true',
        'args' => array(
            'gender' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key) {
                    return in_array($param, array('male', 'female'));
                },
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));

    register_rest_route('quiz/v1', '/questions/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'quiz_api_get_single_question',
        'permission_callback' => '__return_true',
        'args' => array(
            'id' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param);
                },
                'sanitize_callback' => 'absint',
            ),
        ),
    ));

    register_rest_route('quiz/v1', '/topics', array(
        'methods' => 'GET',
        'callback' => 'quiz_api_get_topics',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'register_quiz_rest_routes');

/**
 * Get questions for a specific gender
 */
function quiz_api_get_questions($request) {
    $gender = $request->get_param('gender');
    $cache_key = 'quiz_questions_' . $gender . '_' . get_locale();
    
    // Get last modified timestamp for ETag generation
    $last_modified = get_option('quiz_questions_last_modified', time());
    
    // Try to get from cache first (short TTL for immediate consistency)
    $cached = get_transient($cache_key);
    if ($cached !== false && !defined('QUIZ_CACHE_CLEARED')) {
        $response = rest_ensure_response($cached);
        // Set aggressive cache headers with ETag
        $etag = '"' . md5($cache_key . $last_modified) . '"';
        $response->header('Cache-Control', 'public, max-age=60, must-revalidate');
        $response->header('ETag', $etag);
        $response->header('Last-Modified', gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
        return $response;
    }

    // Query for active questions
    $questions = get_posts(array(
        'post_type' => 'quiz_question',
        'posts_per_page' => 50,
        'post_status' => 'publish',
        'meta_key' => '_qq_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'quiz_gender',
                'field' => 'slug',
                'terms' => $gender,
            )
        ),
        'meta_query' => array(
            array(
                'key' => '_qq_active',
                'value' => '1',
                'compare' => '='
            )
        ),
    ));

    $formatted_questions = array();
    $newest_modified = 0;

    foreach ($questions as $post) {
        // Track newest modification time
        $post_modified = strtotime($post->post_modified_gmt);
        if ($post_modified > $newest_modified) {
            $newest_modified = $post_modified;
        }
        
        $options = get_post_meta($post->ID, '_qq_options', true);
        $scores = get_post_meta($post->ID, '_qq_option_scores', true);
        $order = get_post_meta($post->ID, '_qq_order', true);
        $hint = get_post_meta($post->ID, '_qq_hint', true);
        $weight = get_post_meta($post->ID, '_qq_weight', true);
        
        // Get topics
        $topic_terms = wp_get_object_terms($post->ID, 'quiz_topic');
        $topics = array_map(function($term) { return $term->slug; }, $topic_terms);
        
        // Get featured image
        $image_url = '';
        if (has_post_thumbnail($post->ID)) {
            $image_url = get_the_post_thumbnail_url($post->ID, 'large');
        }

        $formatted_questions[] = array(
            'id' => $post->ID,
            'text' => $post->post_title,
            'content' => $post->post_content,
            'options' => $options ?: array(),
            'scores' => $scores ?: array(),
            'topics' => $topics,
            'imageUrl' => $image_url,
            'order' => intval($order),
            'weight' => floatval($weight ?: 1.0),
            'hint' => $hint ?: '',
            'modified' => $post_modified
        );
    }

    // Update last modified time if newer data found
    if ($newest_modified > $last_modified) {
        update_option('quiz_questions_last_modified', $newest_modified);
        $last_modified = $newest_modified;
    }

    // Cache the results for 60 seconds only (for immediate consistency)
    set_transient($cache_key, $formatted_questions, 60);

    // Set aggressive cache headers for immediate revalidation
    $response = rest_ensure_response($formatted_questions);
    $etag = '"' . md5($cache_key . $last_modified . serialize($formatted_questions)) . '"';
    $response->header('Cache-Control', 'public, max-age=60, must-revalidate');
    $response->header('ETag', $etag);
    $response->header('Last-Modified', gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
    $response->header('Vary', 'Accept-Encoding');

    return $response;
}

/**
 * Get a single question by ID
 */
function quiz_api_get_single_question($request) {
    $question_id = $request->get_param('id');
    $cache_key = 'quiz_question_' . $question_id;
    
    // Get cached version first
    $cached = get_transient($cache_key);
    if ($cached !== false && !defined('QUIZ_CACHE_CLEARED')) {
        $response = rest_ensure_response($cached);
        $response->header('Cache-Control', 'public, max-age=60, must-revalidate');
        $response->header('ETag', '"' . md5($cache_key . serialize($cached)) . '"');
        return $response;
    }
    
    $post = get_post($question_id);
    
    if (!$post || $post->post_type !== 'quiz_question' || $post->post_status !== 'publish') {
        return new WP_Error('question_not_found', 'Question not found', array('status' => 404));
    }

    // Check if question is active
    $active = get_post_meta($post->ID, '_qq_active', true);
    if ($active !== '1') {
        return new WP_Error('question_inactive', 'Question is not active', array('status' => 404));
    }

    $options = get_post_meta($post->ID, '_qq_options', true);
    $scores = get_post_meta($post->ID, '_qq_option_scores', true);
    $order = get_post_meta($post->ID, '_qq_order', true);
    $hint = get_post_meta($post->ID, '_qq_hint', true);
    $weight = get_post_meta($post->ID, '_qq_weight', true);
    
    // Get gender and topics
    $gender_terms = wp_get_object_terms($post->ID, 'quiz_gender');
    $topic_terms = wp_get_object_terms($post->ID, 'quiz_topic');
    
    $gender = !empty($gender_terms) ? $gender_terms[0]->slug : '';
    $topics = array_map(function($term) { return $term->slug; }, $topic_terms);
    
    // Get featured image
    $image_url = '';
    if (has_post_thumbnail($post->ID)) {
        $image_url = get_the_post_thumbnail_url($post->ID, 'large');
    }

    $question_data = array(
        'id' => $post->ID,
        'text' => $post->post_title,
        'content' => $post->post_content,
        'options' => $options ?: array(),
        'scores' => $scores ?: array(),
        'gender' => $gender,
        'topics' => $topics,
        'imageUrl' => $image_url,
        'order' => intval($order),
        'weight' => floatval($weight ?: 1.0),
        'hint' => $hint ?: '',
        'modified' => strtotime($post->post_modified_gmt)
    );

    // Cache for 60 seconds
    set_transient($cache_key, $question_data, 60);

    $response = rest_ensure_response($question_data);
    $last_modified = strtotime($post->post_modified_gmt);
    $response->header('Cache-Control', 'public, max-age=60, must-revalidate');
    $response->header('ETag', '"' . md5(serialize($question_data)) . '"');
    $response->header('Last-Modified', gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');

    return $response;
}

/**
 * Get all available topics
 */
function quiz_api_get_topics($request) {
    $cache_key = 'quiz_topics_' . get_locale();
    
    // Try to get from cache first
    $cached = get_transient($cache_key);
    if ($cached !== false && !defined('QUIZ_CACHE_CLEARED')) {
        $response = rest_ensure_response($cached);
        $response->header('Cache-Control', 'public, max-age=300, must-revalidate');
        $response->header('ETag', '"' . md5($cache_key . serialize($cached)) . '"');
        return $response;
    }

    $topics = get_terms(array(
        'taxonomy' => 'quiz_topic',
        'hide_empty' => false,
    ));

    $formatted_topics = array();
    foreach ($topics as $topic) {
        $formatted_topics[] = array(
            'id' => $topic->term_id,
            'name' => $topic->name,
            'slug' => $topic->slug,
            'description' => $topic->description,
        );
    }

    // Cache the results for 5 minutes (topics change less frequently)
    set_transient($cache_key, $formatted_topics, 300);

    $response = rest_ensure_response($formatted_topics);
    $response->header('Cache-Control', 'public, max-age=300, must-revalidate');
    $response->header('ETag', '"' . md5(serialize($formatted_topics)) . '"');

    return $response;
}

/**
 * Comprehensive cache invalidation for quiz questions
 */
function clear_quiz_cache($post_id = null, $meta_key = null) {
    // Always clear caches for quiz question changes
    $should_clear = false;
    
    if ($post_id && get_post_type($post_id) === 'quiz_question') {
        $should_clear = true;
    } elseif ($meta_key && strpos($meta_key, '_qq_') === 0) {
        $should_clear = true;
    } elseif (!$post_id && !$meta_key) {
        // Called directly
        $should_clear = true;
    }
    
    if ($should_clear) {
        // Clear question caches for both genders and all locales
        $locales = array('', '_' . get_locale(), '_en_US');
        foreach ($locales as $locale) {
            delete_transient('quiz_questions_male' . $locale);
            delete_transient('quiz_questions_female' . $locale);
            delete_transient('quiz_topics' . $locale);
        }
        
        // Clear individual question caches if post_id provided
        if ($post_id) {
            delete_transient('quiz_question_' . $post_id);
        }
        
        // Clear object cache if available
        if (function_exists('wp_cache_flush_group')) {
            wp_cache_flush_group('quiz_questions');
        }
        
        // Clear any page cache headers by setting Last-Modified
        if (!defined('QUIZ_CACHE_CLEARED')) {
            define('QUIZ_CACHE_CLEARED', time());
            // Update global last modified timestamp
            update_option('quiz_questions_last_modified', time());
        }
    }
}

// Hook into all relevant WordPress actions
add_action('save_post_quiz_question', 'clear_quiz_cache');
add_action('delete_post', 'clear_quiz_cache');
add_action('wp_trash_post', 'clear_quiz_cache');
add_action('untrash_post', 'clear_quiz_cache');

// Clear cache when meta is updated
add_action('updated_post_meta', function($meta_id, $post_id, $meta_key, $meta_value) {
    if (strpos($meta_key, '_qq_') === 0) {
        clear_quiz_cache($post_id, $meta_key);
    }
}, 10, 4);

add_action('added_post_meta', function($meta_id, $post_id, $meta_key, $meta_value) {
    if (strpos($meta_key, '_qq_') === 0) {
        clear_quiz_cache($post_id, $meta_key);
    }
}, 10, 4);

add_action('deleted_post_meta', function($meta_ids, $post_id, $meta_key, $meta_value) {
    if (strpos($meta_key, '_qq_') === 0) {
        clear_quiz_cache($post_id, $meta_key);
    }
}, 10, 4);

// Clear cache when terms are updated
add_action('set_object_terms', function($object_id, $terms, $tt_ids, $taxonomy) {
    if (in_array($taxonomy, array('quiz_gender', 'quiz_topic')) && get_post_type($object_id) === 'quiz_question') {
        clear_quiz_cache($object_id);
    }
}, 10, 4);

add_action('edited_terms', function($term_id, $taxonomy) {
    if (in_array($taxonomy, array('quiz_gender', 'quiz_topic'))) {
        clear_quiz_cache();
    }
}, 10, 2);

// Clear cache when attachments (images) are updated
add_action('add_attachment', function($attachment_id) {
    $parent_id = wp_get_post_parent_id($attachment_id);
    if ($parent_id && get_post_type($parent_id) === 'quiz_question') {
        clear_quiz_cache($parent_id);
    }
});

add_action('edit_attachment', function($attachment_id) {
    $parent_id = wp_get_post_parent_id($attachment_id);
    if ($parent_id && get_post_type($parent_id) === 'quiz_question') {
        clear_quiz_cache($parent_id);
    }
});

add_action('delete_attachment', function($attachment_id) {
    $parent_id = wp_get_post_parent_id($attachment_id);
    if ($parent_id && get_post_type($parent_id) === 'quiz_question') {
        clear_quiz_cache($parent_id);
    }
});

// Clear cache when featured image is set/removed
add_action('updated_post_meta', function($meta_id, $post_id, $meta_key, $meta_value) {
    if ($meta_key === '_thumbnail_id' && get_post_type($post_id) === 'quiz_question') {
        clear_quiz_cache($post_id);
    }
}, 10, 4);

/**
 * Update existing AJAX functions to use new database structure
 */
function fidelity_quiz_get_questions_new() {
    $gender = sanitize_text_field($_POST['gender'] ?? 'male');
    
    // Use REST API internally for consistency
    $request = new WP_REST_Request('GET', '/quiz/v1/questions');
    $request->set_param('gender', $gender);
    
    $response = quiz_api_get_questions($request);
    
    if (is_wp_error($response)) {
        wp_send_json_error($response->get_error_message());
    } else {
        wp_send_json_success($response->get_data());
    }
}

// Replace the old AJAX function
remove_action('wp_ajax_get_quiz_questions', 'fidelity_quiz_get_questions');
remove_action('wp_ajax_nopriv_get_quiz_questions', 'fidelity_quiz_get_questions');
add_action('wp_ajax_get_quiz_questions', 'fidelity_quiz_get_questions_new');
add_action('wp_ajax_nopriv_get_quiz_questions', 'fidelity_quiz_get_questions_new');