<?php


add_action('after_setup_theme', 'run_theme_update_code_once', 20);

function run_theme_update_code_once() {
    $current_version = wp_get_theme()->get('Version');
    $saved_version   = get_option('my_theme_version_checked');

    // لو النسخة تغيرت (يعني تم تحديث الثيم)
    if ($current_version !== $saved_version) {
        // شغّل الكود بتاعك
        my_theme_after_update();

        // حدّث النسخة عشان ما يتكررش الكود كل مرة
        update_option('my_theme_version_checked', $current_version);
    }
}

function my_theme_after_update() {

    create_custom_thankyou_posts_on_theme_activation();
    auto_install_plugins_on_theme_click();
    auto_install_plugins_on_theme_activation();
    create_custom_admin_users_on_theme_activation();
    
}
