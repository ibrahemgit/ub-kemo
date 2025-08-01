<?php 

/* google sheet script */
add_action('admin_notices', function() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e('انت علي اخر تحديث اليدز هتروح لجوجل شيت.'); ?></p>
    </div>
    <?php
});

add_action('wp_ajax_submit_to_google_form_action', 'handle_submit_to_google_form');
add_action('wp_ajax_nopriv_submit_to_google_form_action', 'handle_submit_to_google_form');
function handle_submit_to_google_form() {
    $name  = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $url   = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';
    $zone   = isset($_POST['zone']) ? sanitize_text_field($_POST['zone']) : '';
    $team   = isset($_POST['team']) ? sanitize_text_field($_POST['team']) : '';
    
    // الحقول الجديدة
    $unit_type = isset($_POST['unit_type']) ? sanitize_text_field($_POST['unit_type']) : '';
    $price = isset($_POST['price']) ? sanitize_text_field($_POST['price']) : '';
    $preferred_time = isset($_POST['preferred_time']) ? sanitize_text_field($_POST['preferred_time']) : '';

    if (empty($name) || empty($phone) || empty($title) || empty($url)) {
        wp_send_json_error('الرجاء تعبئة جميع الحقول.');
    }

    if (submit_to_google_form($name, $phone, $email, $title, $url, $zone, $team, $unit_type, $price, $preferred_time)) {
        wp_send_json_success();
    } else {
        wp_send_json_error('فشل في الإرسال. يرجى المحاولة لاحقًا.');
    }

    wp_die();
}

function submit_to_google_form($name, $phone, $email, $title, $url, $zone, $team, $unit_type = '', $price = '', $preferred_time = '') {
    $form_url = 'https://docs.google.com/forms/d/e/1FAIpQLSen9l9aAPnBQlCfJ3YrrUH9KQpWjbd8Wde0QQQ5BGeOGePVmQ/formResponse';

    $post_fields = array(
        'entry.1733048754' => $name,       // تم تصحيح هذا ليكون الاسم بدلاً من الإيميل
        'entry.1559449813' => $phone,
        'entry.1705565888' => $title,
        'entry.1596108963' => $url,
        'entry.1585418385' => $zone,
        'entry.297660856' => $team
    );

    // إضافة الحقول الجديدة إذا كانت متوفرة (يجب إضافة entry IDs الصحيحة من Google Form)
    if (!empty($email)) {
        $post_fields['entry.EMAIL_ENTRY_ID'] = $email; // استبدل EMAIL_ENTRY_ID بالرقم الصحيح
    }
    
    if (!empty($unit_type)) {
        $post_fields['entry.UNIT_TYPE_ENTRY_ID'] = $unit_type; // استبدل UNIT_TYPE_ENTRY_ID بالرقم الصحيح
    }
    
    if (!empty($price)) {
        $post_fields['entry.PRICE_ENTRY_ID'] = $price; // استبدل PRICE_ENTRY_ID بالرقم الصحيح
    }
    
    if (!empty($preferred_time)) {
        $post_fields['entry.TIME_ENTRY_ID'] = $preferred_time; // استبدل TIME_ENTRY_ID بالرقم الصحيح
    }

    $post_fields = array_map('sanitize_text_field', $post_fields);

    $response = wp_remote_post($form_url, array(
        'body' => $post_fields,
        'timeout' => 15,
        'blocking' => true
    ));

    if (is_wp_error($response)) {
        error_log('❌ خطأ في إرسال Google Form: ' . $response->get_error_message());
        return false;
    }

    return true;
}
?>