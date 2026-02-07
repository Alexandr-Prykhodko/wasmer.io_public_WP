<?php
/**
 * Plugin Name: Custom Dashboard
 * Description: Кастомные виджеты для главной страницы Dashboard
 */

// Удаляем все стандартные виджеты Dashboard
add_action('wp_dashboard_setup', 'remove_all_dashboard_widgets', 999);

function remove_all_dashboard_widgets() {
    global $wp_meta_boxes;
    
    // Удаляем все стандартные виджеты
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_action('welcome_panel', 'wp_welcome_panel');
}

// Скрываем Welcome panel через user meta
add_action('load-index.php', 'hide_welcome_panel');

function hide_welcome_panel() {
    $user_id = get_current_user_id();
    if (get_user_meta($user_id, 'show_welcome_panel', true) != 0) {
        update_user_meta($user_id, 'show_welcome_panel', 0);
    }
}

// Добавляем кастомные виджеты
add_action('wp_dashboard_setup', 'add_custom_dashboard_widgets');

function add_custom_dashboard_widgets() {
    wp_add_dashboard_widget('custom_stats_widget', 'Statistics', 'custom_stats_widget_display');
    wp_add_dashboard_widget('custom_recent_posts_widget', 'Recently Published', 'custom_recent_posts_widget_display');
}

// Виджет статистики (Posts и Pages)
function custom_stats_widget_display() {
    $posts_count = wp_count_posts('post');
    $pages_count = wp_count_posts('page');
    
    echo '<div class="custom-stats-grid">';
    
    // Posts
    echo '<div class="stat-card stat-card-posts">';
    echo '<div class="stat-icon">📝</div>';
    echo '<div class="stat-content">';
    echo '<div class="stat-number">' . $posts_count->publish . '</div>';
    echo '<div class="stat-label">Posts</div>';
    if ($posts_count->draft > 0) {
        echo '<div class="stat-meta">' . $posts_count->draft . ' drafts</div>';
    }
    echo '</div>';
    echo '</div>';
    
    // Pages
    echo '<div class="stat-card stat-card-pages">';
    echo '<div class="stat-icon">📄</div>';
    echo '<div class="stat-content">';
    echo '<div class="stat-number">' . $pages_count->publish . '</div>';
    echo '<div class="stat-label">Pages</div>';
    if ($pages_count->draft > 0) {
        echo '<div class="stat-meta">' . $pages_count->draft . ' drafts</div>';
    }
    echo '</div>';
    echo '</div>';
    
    echo '</div>';
}

// Виджет недавних публикаций
function custom_recent_posts_widget_display() {
    $recent_posts = wp_get_recent_posts(array(
        'numberposts' => 5,
        'post_status' => 'publish'
    ));
    
    if (!empty($recent_posts)) {
        echo '<ul class="custom-recent-posts">';
        foreach ($recent_posts as $post) {
            $post_type = get_post_type_object($post['post_type']);
            $post_type_label = $post_type ? $post_type->labels->singular_name : 'Post';
            $author_id = $post['post_author'];
            $author_avatar = get_avatar($author_id, 32);
            $author_name = get_the_author_meta('display_name', $author_id);
            
            echo '<li class="recent-post-item">';
            echo '<div class="recent-post-avatar">' . $author_avatar . '</div>';
            echo '<div class="recent-post-content">';
            echo '<div class="recent-post-type">' . esc_html($post_type_label) . '</div>';
            echo '<a href="' . get_edit_post_link($post['ID']) . '" class="recent-post-title">';
            echo esc_html($post['post_title']);
            echo '</a>';
            echo '<div class="recent-post-meta">';
            echo '<span class="recent-post-author">' . esc_html($author_name) . '</span>';
            echo ' • ';
            echo '<span class="recent-post-date">' . human_time_diff(strtotime($post['post_date']), current_time('timestamp')) . ' ago</span>';
            echo '</div>';
            echo '</div>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No published posts yet.</p>';
    }
}

// Кастомные CSS стили для Dashboard
add_action('admin_head', 'custom_dashboard_styles');

function custom_dashboard_styles() {
    echo '<style>
        /* Скрываем Welcome panel */
        #welcome-panel {
            display: none !important;
        }
        
        /* Скрываем пустые drag boxes контейнеры */
        #dashboard-widgets .postbox-container .empty-container {
            display: none !important;
        }
        
        /* Стили для виджета статистики */
        .custom-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 0;
        }
        
        .stat-card {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #2271b1;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-card-posts {
            border-left-color: #2271b1;
        }
        
        .stat-card-pages {
            border-left-color: #00a32a;
        }
        
        .stat-icon {
            font-size: 48px;
            margin-right: 20px;
            line-height: 1;
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: 600;
            color: #1d2327;
            line-height: 1;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #646970;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        
        .stat-meta {
            font-size: 12px;
            color: #787c82;
            margin-top: 5px;
        }
        
        /* Стили для недавних публикаций */
        .custom-recent-posts {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .recent-post-item {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            border-bottom: 1px solid #f0f0f1;
            transition: background 0.2s ease;
        }
        
        .recent-post-item:last-child {
            border-bottom: none;
        }
        
        .recent-post-item:hover {
            background: #f8f9fa;
        }
        
        .recent-post-avatar {
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .recent-post-avatar img {
            border-radius: 50%;
            display: block;
        }
        
        .recent-post-content {
            flex: 1;
            min-width: 0;
        }
        
        .recent-post-type {
            font-size: 11px;
            color: #2271b1;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .recent-post-title {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #1d2327;
            text-decoration: none;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        
        .recent-post-title:hover {
            color: #2271b1;
        }
        
        .recent-post-meta {
            font-size: 12px;
            color: #787c82;
        }
        
        .recent-post-author {
            font-weight: 500;
        }
        
        .recent-post-date {
            color: #a7aaad;
        }
        
        /* Адаптивность для маленьких экранов */
        @media (max-width: 782px) {
            .custom-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>';
}