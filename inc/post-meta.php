<?php
// 1. تسجيل post meta
function register_contact_fields_meta() {
    $post_types = array('post', 'page'); // الأنواع المستهدفة

    foreach ($post_types as $post_type) {
        register_post_meta($post_type, 'contact_email', array(
            'show_in_rest'      => false,
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_email',
            'auth_callback'     => function() {
                return current_user_can('edit_post', get_the_ID());
            },
        ));

        register_post_meta($post_type, 'contact_phone', array(
            'show_in_rest'      => false,
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can('edit_post', get_the_ID());
            },
        ));

        register_post_meta($post_type, 'contact_whatsapp', array(
            'show_in_rest'      => false,
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can('edit_post', get_the_ID());
            },
        ));

        register_post_meta($post_type, 'enable_contact', array(
            'show_in_rest'      => false,
            'single'            => true,
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'auth_callback'     => function() {
                return current_user_can('edit_post', get_the_ID());
            },
        ));
    }
}
add_action('init', 'register_contact_fields_meta');


// 2. إضافة الميتا بوكس
function add_contact_fields_metabox() {
    foreach (['post', 'page'] as $post_type) {
        add_meta_box(
            'contact_info_meta_box',
            'بيانات التواصل',
            'render_contact_fields_metabox',
            $post_type,
            'side',
            'default'
        );
    }    
}
add_action('add_meta_boxes', 'add_contact_fields_metabox');


// 3. عرض الحقول داخل الميتا بوكس
function render_contact_fields_metabox($post) {
    $email           = get_post_meta($post->ID, 'contact_email', true);
    $phone           = get_post_meta($post->ID, 'contact_phone', true);
    $whatsapp        = get_post_meta($post->ID, 'contact_whatsapp', true);
    $enable_contact  = get_post_meta($post->ID, 'enable_contact', true);

    wp_nonce_field('save_contact_fields_meta', 'contact_fields_nonce');
    ?>
    <p>
        <label for="contact_email">الإيميل:</label><br>
        <input type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr($email); ?>" style="width: 100%;" placeholder="example@email.com" />
    </p>
    <p>
        <label for="contact_phone">رقم الهاتف:</label><br>
        <input type="text" id="contact_phone" name="contact_phone" value="<?php echo esc_attr($phone); ?>" style="width: 100%;" placeholder="01000000000" />
    </p>
    <p>
        <label for="contact_whatsapp">رقم الواتساب:</label><br>
        <input type="text" id="contact_whatsapp" name="contact_whatsapp" value="<?php echo esc_attr($whatsapp); ?>" style="width: 100%;" placeholder="01000000000" />
    </p>
    <p>
        <label>
            <input type="checkbox" name="enable_contact" value="1" <?php checked($enable_contact, true); ?> />
            تفعيل التواصل في هذا البوست
        </label>
    </p>
    <?php
}


// 4. حفظ البيانات
function save_contact_fields_meta($post_id) {
    if (!isset($_POST['contact_fields_nonce']) || !wp_verify_nonce($_POST['contact_fields_nonce'], 'save_contact_fields_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // إيميل
    if (isset($_POST['contact_email'])) {
        update_post_meta($post_id, 'contact_email', sanitize_email($_POST['contact_email']));
    }

    // هاتف
    if (isset($_POST['contact_phone'])) {
        update_post_meta($post_id, 'contact_phone', sanitize_text_field($_POST['contact_phone']));
    }

    // واتساب
    if (isset($_POST['contact_whatsapp'])) {
        update_post_meta($post_id, 'contact_whatsapp', sanitize_text_field($_POST['contact_whatsapp']));
    }

    // Checkbox
    $enable_contact = isset($_POST['enable_contact']) ? true : false;
    update_post_meta($post_id, 'enable_contact', $enable_contact);
}
add_action('save_post', 'save_contact_fields_meta');
