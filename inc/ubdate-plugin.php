<?php

function my_plugin_reset_button() {
    // إضافة زر إعادة تحميل البلجن
    ?>
    <!-- <form method="post" style="margin-top:20px">
        <?php # wp_nonce_field('reset_plugin_nonce', 'reset_plugin_nonce'); ?>
        <input type="submit" name="reset_plugin" value="تفعيل التحديثات" class="button button-primary"/>
    </form> -->
    <?php
}

add_action('admin_notices', 'my_plugin_reset_button');

if (isset($_POST['reset_plugin']) && isset($_POST['reset_plugin_nonce']) && wp_verify_nonce($_POST['reset_plugin_nonce'], 'reset_plugin_nonce')) {
    // مسح البلجن وإعادة تثبيته
    auto_install_plugins_on_theme_click(); // استخدم الدالة التي قمت بتعريفها مسبقًا
}

function auto_install_plugins_on_theme_click() {
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

    // التحقق إذا كان البلجن مثبتًا
    if (is_plugin_active($plugin_main_file_github)) {
        deactivate_plugins($plugin_main_file_github); // إلغاء تنشيط البلجن أولاً
        delete_plugins(array($plugin_main_file_github)); // حذف البلجن
    }

    // تحميل وتثبيت البلجن من جديد إذا لم يكن موجودًا
    if (!is_dir(WP_PLUGIN_DIR . '/' . $plugin_folder_github)) {
        $tmp_file = download_url($plugin_zip_url_github);
        if (!is_wp_error($tmp_file)) {
            $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
            $result = $upgrader->install($plugin_zip_url_github);

            if (!is_wp_error($result)) {
                // أعد تسمية فولدر البلجن لو نزل باسم مختلف
                $source_folder = WP_PLUGIN_DIR . '/' . $plugin_folder_github . '-main';
                $final_folder  = WP_PLUGIN_DIR . '/' . $plugin_folder_github;

                if (is_dir($source_folder)) {
                    rename($source_folder, $final_folder);
                }
            }
        }
    }

    // تنشيط البلجن بعد تثبيته
    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_main_file_github) && !is_plugin_active($plugin_main_file_github)) {
        activate_plugin($plugin_main_file_github);
    }
}


add_action('after_switch_theme', 'auto_install_plugins_on_theme_click');
