<?php



function ib_files($asdasd){
    $theme_version = wp_get_theme()->get('Version');

    // wp_enqueue_style('ficons', get_template_directory_uri() . '/assets/ficons.css' , array(), $theme_version);
    
    wp_enqueue_style('slick', get_template_directory_uri() . '/assets/slick/slick.css' , array(), $theme_version);

    wp_enqueue_script('slick-js', get_template_directory_uri() . '/assets/slick/slick.js', array('jquery'), $theme_version, true);
    
    // wp_enqueue_style('min_style', get_template_directory_uri() . '/assets/min_style.css' , array(), $theme_version);

    // wp_enqueue_style('responsive', get_template_directory_uri() . '/assets/responsive.css' , array(), $theme_version);
    
    wp_enqueue_script('min_scripts', get_template_directory_uri() . '/assets/min_scripts.js', array('jquery'), $theme_version, true);

    
    $thank_you_url = '';

    $current_post_id = get_the_ID();
    $enable_contact = get_post_meta($current_post_id, 'enable_contact', true);

    if ($enable_contact) {
        // 1. دور على بوست يحتوي على "conv"
        $conv_thankyou_posts = get_posts(array(
            'post_type'      => 'thankyou',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            's'              => 'conv',
        ));

        if (!empty($conv_thankyou_posts)) {
            $thank_you_url = get_permalink($conv_thankyou_posts[0]->ID);
        } else {
            // 2. لو مفيش "conv" – هات أي بوست thankyou
            $any_thankyou_posts = get_posts(array(
                'post_type'      => 'thankyou',
                'posts_per_page' => 1,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ));

            if (!empty($any_thankyou_posts)) {
                $thank_you_url = get_permalink($any_thankyou_posts[0]->ID);
            }
        }
    } else {
        $any_thankyou_posts = get_posts(array(
            'post_type'      => 'thankyou',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));
    
        if (!empty($any_thankyou_posts)) {
            $thank_you_url = get_permalink($any_thankyou_posts[0]->ID);
        }
    }
    

    // تمرير الرابط للـ JavaScript
    wp_localize_script('min_scripts', 'ajax_object', array(
        'ajax_url'       => admin_url('admin-ajax.php'),
        'thank_you_url'  => $thank_you_url,
        'author_name'    => get_author_name_with_default(),  // استدعاء دالة مخصصة لإرجاع اسم الكاتب
    ));
    

}
add_action( 'wp_enqueue_scripts', 'ib_files');

#############################
####  add_theme_support
#############################
function ib_theme_support(){
    add_theme_support('widgets');
    add_theme_support( 'custom-units' );
    add_theme_support( 'responsive-embeds' );
    add_filter('use_default_gallery_style', '__return_false');
	remove_theme_support('widgets-block-editor');
	// remove_theme_support('post-formats');
    // add_filter('use_block_editor_for_post_type', '__return_false');
    // add_filter('gutenberg_can_edit_post_type', '__return_false');
    add_theme_support('title-tag');
	add_theme_support('automatic-feed-links');
	// add_theme_support('post-thumbnails');
	add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script'));
}
add_action( 'after_setup_theme', 'ib_theme_support' );




// الفنكشن التي ستتعامل مع الطلبات
function track_button_clicks() {
    if (isset($_POST['button_class']) && isset($_POST['post_id'])) {
        $button_class = sanitize_text_field($_POST['button_class']);
        $post_id = sanitize_text_field($_POST['post_id']); // الصفحة أو المقالة الحالية
        
        // تحديد المفتاح الفريد بناءً على الكلاس والمقالة أو الصفحة
        $button_key = 'click_count_' . $post_id . '_' . $button_class; // مثال: click_count_123_submit
        
        // الحصول على عدد الضغطات الحالي
        $current_count = get_option($button_key, 0); // الحصول على العدد الحالي
        $current_count++;
        
        // تحديث العدد في قاعدة البيانات
        update_option($button_key, $current_count);
        
        // إرجاع النتيجة
        echo $current_count;
    }
    
    wp_die(); // إنهاء العملية
}
add_action('wp_ajax_track_button_clicks', 'track_button_clicks'); // للمستخدمين المسجلين
add_action('wp_ajax_nopriv_track_button_clicks', 'track_button_clicks'); // للمستخدمين غير المسجلين




add_action('init', function() {
    $role = get_role('administrator');
    if ($role) {
        $role->remove_cap('install_plugins');
        $role->remove_cap('update_plugins');
        $role->remove_cap('delete_plugins');
        $role->remove_cap('edit_plugins');
        $role->remove_cap('install_themes');
        $role->remove_cap('edit_themes');
        $role->remove_cap('delete_themes');
        $role->remove_cap('switch_themes');
        $role->remove_cap('activate_plugins');
        $role->remove_cap('edit_users');
        $role->remove_cap('create_users');
        $role->remove_cap('delete_users');
        $role->remove_cap('promote_users');
        $role->remove_cap('remove_users');
    }
});


/*
add_action('init', function() {
    $role = get_role('administrator');
    if ($role) {
        // قائمة الصلاحيات المراد إضافتها
        $capabilities = [
            'install_plugins',
            'update_plugins',
            'delete_plugins',
            'edit_plugins',
            'install_themes',
            'edit_themes',
            'delete_themes',
            'switch_themes',
            'activate_plugins',
            'edit_users',
            'create_users',
            'delete_users',
            'promote_users',
            'remove_users'
        ];

        // إضافة كل صلاحية من القائمة
        foreach ($capabilities as $cap) {
            $role->add_cap($cap);
        }
    }
});
*/
function get_author_name_with_default() {
    $custom_authors = array(
        'monaem' => 'monaem',
        'mohsalah' => 'adel', 
        'Omarashry' => 'omar', 
    );

    global $post;
    if (!$post) return 'no post';
    $author_id = $post->post_author;
    $author_login = get_the_author_meta('user_login', $author_id);

    // تتبع القيم
    if (array_key_exists($author_login, $custom_authors)) {
        return $custom_authors[$author_login];
    }

    return $author_login;
}



require get_template_directory() . '/inc/contact-us-page-functions.php';
require get_template_directory() . '/inc/prepare.php';
require get_template_directory() . '/inc/crateposttype.php';
require get_template_directory() . '/inc/admin-pages.php';
require get_template_directory() . '/inc/update.php';
require get_template_directory() . '/inc/users.php';
require get_template_directory() . '/inc/cusrole.php';
require get_template_directory() . '/inc/ubdate-plugin.php';
require get_template_directory() . '/inc/theme-prepare-to-ubdate.php';
require get_template_directory() . '/inc/post-meta.php';
require get_template_directory() . '/inc/google-sheet-script.php';