<?php 
// ------------------------------
// صفحة الإعدادات المخصصة
// ------------------------------
add_action('admin_menu', 'custom_settings_page');
function custom_settings_page() {
    add_menu_page(
        'صفحة الإعدادات',
        'الإعدادات الخاصة',
        'manage_options',
        'custom-settings',
        'custom_settings_page_html',
        'dashicons-admin-generic',
        100
    );
}

function custom_settings_page_html() {
    if (!current_user_can('manage_options')) return;

    // حفظ الإعدادات
    if (isset($_POST['custom_settings_save']) && check_admin_referer('custom_settings_action', 'custom_settings_nonce')) {
        $email    = isset($_POST['custom_email']) ? sanitize_email($_POST['custom_email']) : '';
        $phone    = isset($_POST['custom_phone']) ? sanitize_text_field($_POST['custom_phone']) : '';
        $whatsapp = isset($_POST['custom_whatsapp']) ? sanitize_text_field($_POST['custom_whatsapp']) : '';

        // الحقول الإضافية
        $extra_phones = isset($_POST['extra_phones']) ? array_map('sanitize_text_field', $_POST['extra_phones']) : array();
        $extra_whatsapps = isset($_POST['extra_whatsapps']) ? array_map('sanitize_text_field', $_POST['extra_whatsapps']) : array();

        update_option('custom_email', $email);
        update_option('custom_phone', $phone);
        update_option('custom_whatsapp', $whatsapp);

        update_option('extra_phones', $extra_phones);
        update_option('extra_whatsapps', $extra_whatsapps);

        echo '<div class="updated"><p>تم حفظ الإعدادات بنجاح.</p></div>';
    }

    $custom_email    = get_option('custom_email', '');
    $custom_phone    = get_option('custom_phone', '');
    $custom_whatsapp = get_option('custom_whatsapp', '');
    $extra_phones    = get_option('extra_phones', array());
    $extra_whatsapps = get_option('extra_whatsapps', array());

    echo '<div class="wrap">';
    echo '<h1>الإعدادات الخاصة</h1>';
    echo '<form method="post">';
    wp_nonce_field('custom_settings_action', 'custom_settings_nonce');
    echo '<table class="form-table">';
    echo '<tr><th scope="row"><label for="custom_email">البريد الإلكتروني</label></th>';
    echo '<td><input name="custom_email" type="email" id="custom_email" value="' . esc_attr($custom_email) . '" class="regular-text"></td></tr>';
    echo '<tr><th scope="row"><label for="custom_phone">رقم الهاتف</label></th>';
    echo '<td><input name="custom_phone" type="text" id="custom_phone" value="' . esc_attr($custom_phone) . '" class="regular-text"></td></tr>';
    echo '<tr><th scope="row"><label for="custom_whatsapp">رقم الواتساب</label></th>';
    echo '<td><input name="custom_whatsapp" type="text" id="custom_whatsapp" value="' . esc_attr($custom_whatsapp) . '" class="regular-text"></td></tr>';
    echo '</table>';

    // الحقول الإضافية
    echo '<h2>أرقام هواتف إضافية</h2>';
    echo '<div id="extra-phones-wrapper">';
    foreach ($extra_phones as $phone) {
        echo '<div class="input-group" style="margin-bottom:8px;">
                <input name="extra_phones[]" type="text" value="' . esc_attr($phone) . '" class="regular-text" style="margin-right:10px;">
                <button type="button" class="button remove-field">حذف</button>
              </div>';
    }
    echo '</div>';
    echo '<button type="button" class="button" onclick="addExtraPhone()">+ إضافة رقم هاتف</button>';

    echo '<h2 style="margin-top:30px;">أرقام واتساب إضافية</h2>';
    echo '<div id="extra-whatsapps-wrapper">';
    foreach ($extra_whatsapps as $whatsapp) {
        echo '<div class="input-group" style="margin-bottom:8px;">
                <input name="extra_whatsapps[]" type="text" value="' . esc_attr($whatsapp) . '" class="regular-text" style="margin-right:10px;">
                <button type="button" class="button remove-field">حذف</button>
              </div>';
    }
    echo '</div>';
    echo '<button type="button" class="button" onclick="addExtraWhatsapp()">+ إضافة رقم واتساب</button>';

    echo '<p style="margin-top:30px;"><input type="submit" name="custom_settings_save" class="button-primary" value="حفظ الإعدادات"></p>';
    echo '</form></div>';

    // JavaScript
    echo '
    <script>
        function addExtraPhone() {
            const wrapper = document.getElementById("extra-phones-wrapper");
            const div = document.createElement("div");
            div.className = "input-group";
            div.style.marginBottom = "8px";
            div.innerHTML = \'<input name="extra_phones[]" type="text" class="regular-text" style="margin-right:10px;">\' +
                            \'<button type="button" class="button remove-field">حذف</button>\';
            wrapper.appendChild(div);
        }

        function addExtraWhatsapp() {
            const wrapper = document.getElementById("extra-whatsapps-wrapper");
            const div = document.createElement("div");
            div.className = "input-group";
            div.style.marginBottom = "8px";
            div.innerHTML = \'<input name="extra_whatsapps[]" type="text" class="regular-text" style="margin-right:10px;">\' +
                            \'<button type="button" class="button remove-field">حذف</button>\';
            wrapper.appendChild(div);
        }

        document.addEventListener("click", function(e) {
            if (e.target && e.target.classList.contains("remove-field")) {
                e.preventDefault();
                e.target.parentElement.remove();
            }
        });
    </script>
    ';
}

// ------------------------------
// تحميل سكربتات وأسلوب للوحة التحكم
// ------------------------------
function ib_admin_script($hook){
    if (
        strpos($hook, 'toplevel_page_contact') !== false ||
        strpos($hook, 'profile.php') !== false ||
        strpos($hook, 'user-edit.php') !== false ||
        strpos($hook, 'post-new.php') !== false ||
        strpos($hook, 'post.php') !== false ||
        strpos($hook, 'term.php') !== false ||
        strpos($hook, 'edit-tags.php') !== false ||
        strpos($hook, 'toplevel_page_second_theme_options_page') !== false ||
        strpos($hook, 'toplevel_page_theme_filter_option_page') !== false
    ) {
        $theme_version = wp_get_theme()->get('Version');
        wp_enqueue_style('adminscripts', get_template_directory_uri() . '/assets/admin/adminstyle.css', array(), $theme_version);
        wp_enqueue_script('adminscripts', get_template_directory_uri() . '/assets/admin/admin.js', array('jquery'), $theme_version, true);
        wp_enqueue_media();
        wp_enqueue_editor();
        wp_localize_script('adminscripts', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }
}
add_action('admin_enqueue_scripts', 'ib_admin_script');

// ------------------------------
// صفحة عرض إحصائيات الضغطات
// ------------------------------
function custom_admin_menu() {
    add_menu_page(
        'Click Counts',
        'Click Counts',
        'manage_options',
        'click-counts',
        'display_click_counts_page_and_posts',
        'dashicons-chart-bar',
        6
    );
}
add_action('admin_menu', 'custom_admin_menu');

function display_click_counts_page_and_posts() {
    $posts = get_posts(array(
        'post_type' => array('page', 'post'),
        'posts_per_page' => -1
    ));
    ?>
    <div class="wrap">
        <h1>عدد الضغطات على الأزرار لكل صفحة أو مقال</h1>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th>الصفحة / المقالة</th>
                    <th>Submit Button</th>
                    <th>WhatsApp Button</th>
                    <th>Phone Button</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo esc_html(get_the_title($post->ID)); ?></td>
                        <td><?php echo intval(get_option('click_count_' . $post->ID . '_submit', 0)); ?></td>
                        <td><?php echo intval(get_option('click_count_' . $post->ID . '_whatsapp', 0)); ?></td>
                        <td><?php echo intval(get_option('click_count_' . $post->ID . '_phone', 0)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}








// ------------------------------
// صفحة تنبيهات النوتفكيشن
// ------------------------------
function custom_notification_messages_page() {
    add_submenu_page(
        'custom-settings',            // تربطها بقائمة "الإعدادات الخاصة"
        'تنبيهات النوتفكيشن',
        'تنبيهات النوتفكيشن',
        'manage_options',
        'notification-settings',
        'render_notification_messages_page'
    );
}
add_action('admin_menu', 'custom_notification_messages_page');

function render_notification_messages_page() {
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['notification_save']) && check_admin_referer('notification_save_action', 'notification_nonce')) {
        $messages = isset($_POST['notification_messages']) ? array_map('sanitize_text_field', $_POST['notification_messages']) : array();
        update_option('notification_messages', $messages);
        echo '<div class="updated"><p>تم حفظ التنبيهات بنجاح.</p></div>';
    }

    $messages = get_option('notification_messages', []);

    echo '<div class="wrap">';
    echo '<h1>تنبيهات النوتفكيشن</h1>';
    echo '<form method="post">';
    wp_nonce_field('notification_save_action', 'notification_nonce');

    echo '<div id="notification-wrapper">';
    foreach ($messages as $msg) {
        echo '<div class="input-group" style="margin-bottom:8px;">
                <input name="notification_messages[]" type="text" value="' . esc_attr($msg) . '" class="regular-text" style="margin-right:10px;">
                <button type="button" class="button remove-field">حذف</button>
              </div>';
    }
    echo '</div>';

    echo '<button type="button" class="button" onclick="addNotification()">+ إضافة جملة</button>';
    echo '<p style="margin-top:30px;"><input type="submit" name="notification_save" class="button-primary" value="حفظ التنبيهات"></p>';
    echo '</form>';
    echo '</div>';

    // JavaScript للحذف والإضافة
    echo '
    <script>
        function addNotification() {
            const wrapper = document.getElementById("notification-wrapper");
            const div = document.createElement("div");
            div.className = "input-group";
            div.style.marginBottom = "8px";
            div.innerHTML = \'<input name="notification_messages[]" type="text" class="regular-text" style="margin-right:10px;">\' +
                            \'<button type="button" class="button remove-field">حذف</button>\';
            wrapper.appendChild(div);
        }

        document.addEventListener("click", function(e) {
            if (e.target && e.target.classList.contains("remove-field")) {
                e.preventDefault();
                e.target.parentElement.remove();
            }
        });
    </script>
    ';
}
