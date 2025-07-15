<?php 
add_action('after_switch_theme', 'auto_install_plugins_on_theme_activation');

function auto_install_plugins_on_theme_activation() {
    include_once ABSPATH . 'wp-admin/includes/file.php';
    include_once ABSPATH . 'wp-admin/includes/misc.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    // =======================
    // 1. بلجن GitHub (Ibrahem)
    // =======================
    $plugin_folder_github    = 'page_builder_br';
    $plugin_main_file_github = 'page_builder_br/plugin.php';
    $plugin_zip_url_github   = 'https://github.com/ibrahemgit/page_builder_br/archive/refs/heads/main.zip';

    if (!is_dir(WP_PLUGIN_DIR . '/' . $plugin_folder_github)) {
        $tmp_file = download_url($plugin_zip_url_github);
        if (!is_wp_error($tmp_file)) {
            $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
            $result = $upgrader->install($plugin_zip_url_github);

            if (!is_wp_error($result)) {
                $source_folder = WP_PLUGIN_DIR . '/' . $plugin_folder_github . '-main';
                $final_folder  = WP_PLUGIN_DIR . '/' . $plugin_folder_github;

                if (is_dir($source_folder)) {
                    rename($source_folder, $final_folder);
                }
            }
        }
    }

    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_main_file_github) && !is_plugin_active($plugin_main_file_github)) {
        activate_plugin($plugin_main_file_github);
    }



    // ===================================================
    // 2. Header Footer Code Manager Plugin (HFCM)
    // ===================================================
    $hfcm_slug      = 'header-footer-code-manager';
    $hfcm_main_file = 'header-footer-code-manager/99robots-header-footer-code-manager.php';

    if (!is_dir(WP_PLUGIN_DIR . '/' . $hfcm_slug)) {
        $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
        $upgrader->install('https://downloads.wordpress.org/plugin/' . $hfcm_slug . '.latest-stable.zip');
    }

    if (file_exists(WP_PLUGIN_DIR . '/' . $hfcm_main_file) && !is_plugin_active($hfcm_main_file)) {
        activate_plugin($hfcm_main_file);
    }

	
    // =======================================
    // 3. PixelYourSite Plugin
    // =======================================
    $pys_slug      = 'pixelyoursite';
    $pys_main_file = 'pixelyoursite/facebook-pixel-master.php';

    if (!is_dir(WP_PLUGIN_DIR . '/' . $pys_slug)) {
        $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
        $upgrader->install('https://downloads.wordpress.org/plugin/' . $pys_slug . '.latest-stable.zip');
    }

    if (file_exists(WP_PLUGIN_DIR . '/' . $pys_main_file) && !is_plugin_active($pys_main_file)) {
        activate_plugin($pys_main_file);
    }


// // ===================================================
// // Duplicate Page Plugin
// // ===================================================
// $duplicate_slug      = 'duplicate-page';
// $duplicate_main_file = 'duplicate-page/duplicatepage.php';

// if (!is_dir(WP_PLUGIN_DIR . '/' . $duplicate_slug)) {
//     include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
//     $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
//     $upgrader->install('https://downloads.wordpress.org/plugin/' . $duplicate_slug . '.latest-stable.zip');
// }

// if (file_exists(WP_PLUGIN_DIR . '/' . $duplicate_main_file) && !is_plugin_active($duplicate_main_file)) {
//     include_once ABSPATH . 'wp-admin/includes/plugin.php';
//     activate_plugin($duplicate_main_file);
// }

// ===================================================
// Google Site Kit Plugin
// ===================================================
$sitekit_slug      = 'google-site-kit';
$sitekit_main_file = 'google-site-kit/google-site-kit.php';

if (!is_dir(WP_PLUGIN_DIR . '/' . $sitekit_slug)) {
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
    $upgrader->install('https://downloads.wordpress.org/plugin/' . $sitekit_slug . '.latest-stable.zip');
}

if (file_exists(WP_PLUGIN_DIR . '/' . $sitekit_main_file) && !is_plugin_active($sitekit_main_file)) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    activate_plugin($sitekit_main_file);
}

    
}

function create_custom_thankyou_posts_on_theme_activation() {
    // نستخدم option لتتبع هل البوستات اتعملت قبل كده
    if (get_option('custom_thankyou_posts_created_thx')) {
        return; // خلاص اتعملت قبل كده
    }

    // ✅ حذف كل البوستات القديمة من نوع thankyou
    $existing_posts = get_posts(array(
        'post_type'      => 'thankyou',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ));

    foreach ($existing_posts as $post) {
        wp_delete_post($post->ID, true); // true = حذف نهائي
    }

    // 1. إنشاء بوست conv
    $conv_post = array(
        'post_title'   => 'thankyou-conv',
        'post_status'  => 'publish',
        'post_type'    => 'thankyou',
    );
    wp_insert_post($conv_post);

    // 2. إنشاء بوست عادي
    $normal_post = array(
        'post_title'   => 'thankyou-norm',
        'post_status'  => 'publish',
        'post_type'    => 'thankyou',
    );
    wp_insert_post($normal_post);

    // حفظ إنهم اتعملوا خلاص
    update_option('custom_thankyou_posts_created_thx', true);
}

add_action('after_switch_theme', 'create_custom_thankyou_posts_on_theme_activation');






function send_domain_to_sheet_on_theme_activation_once() {
    $option_key = 'domain_sent_to_google_sheet';
    if (get_option($option_key)) {
        return; 
    }
    $domain = parse_url(home_url(), PHP_URL_HOST);
    $domain = preg_replace('/^www\./', '', $domain);
    $url = 'https://script.google.com/macros/s/AKfycbwgdNW0JnBqJYMrqpZV9cutFqAPXLJqR4WIS62dVw8eqjDnBojXH4Yi2hHb5GawT4OS/exec?value=' . urlencode($domain);
    wp_remote_get($url);
    update_option($option_key, true);
}
add_action('after_switch_theme', 'send_domain_to_sheet_on_theme_activation_once');
