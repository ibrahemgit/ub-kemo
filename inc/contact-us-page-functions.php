<?php 

#############################
####  AJAX
#############################
add_action('wp_ajax_submit_contact_form', 'handle_contact_form');
add_action('wp_ajax_nopriv_submit_contact_form', 'handle_contact_form'); 
function handle_contact_form() {

if (empty($_POST['name']) || empty($_POST['phone'])) {
    wp_die(); // أو اعمل wp_send_json_error قبلها لو حابب
}


        $name = sanitize_text_field($_POST['name'] ?? "");
        $phone = sanitize_text_field($_POST['phone']?? "") ;
        $email = sanitize_email($_POST['email'] ?? "");
        $preferred_time = sanitize_text_field($_POST['preferred_time']?? "") ;
        $message = sanitize_textarea_field($_POST['message']?? "") ;
        $timeZone  = sanitize_textarea_field($_POST['timeZone']?? "") ;
        $pageTitle  = sanitize_textarea_field($_POST['pageTitle']?? "") ;
        $contact_methods = !empty($_POST['contact']) ? implode(", ", array_map('sanitize_text_field', $_POST['contact'])) : 'لم يتم اختيار طريقه للتواصل';
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        $job  = sanitize_text_field($_POST['job']  ?? "");
$area = sanitize_text_field($_POST['area'] ?? "");


        // الحقول الجديدة
        $unit_type = sanitize_text_field($_POST['unit_type'] ?? "");
        $price = sanitize_text_field($_POST['price'] ?? "");
        
        // الحقول الجديدة
        $unit_type = sanitize_text_field($_POST['unit_type'] ?? "");
        $price = sanitize_text_field($_POST['price'] ?? "");

        $enable_contact = get_post_meta($post_id, 'enable_contact', true);

        if ($enable_contact) {
            $pageTitle = $pageTitle . " - Conv ";
        }

        $subject = 'رسالة جديدة من الموقع';
        $body = "الاسم: $name\n";

        if(isset($_POST['is_prshorshort']) && $_POST['is_prshorshort'] === "1"){
            $body .= "رقم الهاتف: $phone\n";
            $body .= "الدولة: $timeZone\n";
            $body .= "اسم الصفحة: $pageTitle\n";
            // إضافة الحقول الجديدة للفورم المختصر
            
    if (!empty($unit_type)) $body .= "نوع الوحدة: $unit_type\n";
    if (!empty($price))     $body .= "السعر: $price\n";
    // NEW
    if (!empty($job))  $body .= "الوظيفة: $job\n";
    if (!empty($area)) $body .= "المنطقة: $area\n";

        } elseif(isset($_POST['is_unitform']) && $_POST['is_unitform'] === "1"){
            $body .= "رقم الهاتف: $phone\n";
            $body .= "الدولة: $timeZone\n";
            $body .= "اسم الصفحة: $pageTitle\n";
            // إضافة الحقول الجديدة لفورم الوحدات
    if (!empty($unit_type)) $body .= "نوع الوحدة: $unit_type\n";
    if (!empty($price))     $body .= "السعر: $price\n";
    // NEW
    if (!empty($job))  $body .= "الوظيفة: $job\n";
    if (!empty($area)) $body .= "المنطقة: $area\n";

        }else{
            $body .= "رقم الهاتف: $phone\n";
            $body .= "الدولة: $timeZone\n";
            $body .= "اسم الصفحة: $pageTitle\n";
            if (!empty($_POST['contact'])) {
                $body .= "طرق التواصل المفضلة: $contact_methods\n";
            } else {
                $body .= "طرق التواصل المفضلة: لم يتم اختيار طرق\n";
            }
            $body .= "البريد الإلكتروني: $email\n";
            $body .= "الوقت المفضل للتواصل: $preferred_time\n";
            // إضافة الحقول الجديدة للفورم العادي
    if (!empty($unit_type)) $body .= "نوع الوحدة: $unit_type\n";
    if (!empty($price))     $body .= "السعر: $price\n";

    if (!empty($job))  $body .= "الوظيفة: $job\n";
    if (!empty($area)) $body .= "المنطقة: $area\n";

            $body .= "الرسالة: \n$message\n";
        }

        global $wpdb;
		$table_name = esc_sql($wpdb->prefix . 'ib_contact_form_data');
		$result = $wpdb->insert(
			$table_name,
			array(
				'name' => $name,
				'phone' => $phone,
				'email' => $email,
				'preferred_time' => $preferred_time,
				'message' => $message,
				'time_zone' => $timeZone,
				'page_title' => $pageTitle,
				'contact_methods' => $contact_methods,
				'unit_type' => $unit_type,
				'price' => $price,
				'contacted' => 0, 
			),
			array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d')
		);

		if ($result === false) {
			error_log("خطأ في إدخال البيانات: " . $wpdb->last_error);
		}

        $post_email   = get_post_meta($post_id, 'contact_email', true);
        $custom_email = get_option('custom_email');
        
        // الترتيب: meta ➤ option ➤ fallback
        if (!empty($post_email) && is_email($post_email)) {
            $final_email = $post_email;
        } elseif (!empty($custom_email) && is_email($custom_email)) {
            $final_email = $custom_email;
        } else {
            $final_email = 'boldd.routes@gmail.com';
        }
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        if (is_email($final_email)) {
            error_log($final_email); // للديباج فقط
            wp_mail($final_email, $subject, $body, $headers);
        }
	
    // تحقق إذا لم يتم العثور على رابط
	echo json_encode(array(
		'status' => 'success',
		'message' => ' <i class="fa fa-check" aria-hidden="true"></i> تم إرسال الرسالة بنجاح. شكرًا لتواصلك معنا!',
	));
    
    wp_die();
}

#############################
####  crate new tb in db
#############################

function create_contact_form_table_on_theme_switch() {
    global $wpdb;

    $table_name = esc_sql($wpdb->prefix . 'ib_contact_form_data');

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        // التحقق من وجود الأعمدة الجديدة وإضافتها إذا لم تكن موجودة
        $columns = $wpdb->get_col("DESCRIBE $table_name");
        
        if (!in_array('unit_type', $columns)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN unit_type varchar(100) DEFAULT NULL");
        }
        
        if (!in_array('price', $columns)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN price varchar(50) DEFAULT NULL");
        }
        
        return;
    }

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL DEFAULT '',
        phone varchar(20) NOT NULL DEFAULT '',
        email varchar(100) NOT NULL DEFAULT '',
        preferred_time varchar(100) DEFAULT NULL,
        message text NOT NULL,
        time_zone varchar(100) DEFAULT NULL,
        page_title varchar(255) DEFAULT NULL,
        contact_methods text NOT NULL,
        unit_type varchar(100) DEFAULT NULL,
        price varchar(50) DEFAULT NULL,
        submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        contacted tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);

    if ($wpdb->last_error) {
        error_log(__('DB Error: ', 'aqarround') . $wpdb->last_error);
        wp_die(__('There was an error creating the database table.', 'aqarround'));
    }
}
add_action('after_switch_theme', 'create_contact_form_table_on_theme_switch');

#############################
####  admin page
#############################
function add_contact_form_admin_page() {
    add_menu_page(
        'Contact Form Submissions',
        __('Contact Form' , 'aqarround'),
        'manage_options',
        'contact-form-data',
        'render_contact_form_admin_page',
        'dashicons-list-view',
        26
    );
}
add_action('admin_menu', 'add_contact_form_admin_page');

function render_contact_form_admin_page() {
    global $wpdb;

    $table_name = esc_sql($wpdb->prefix . 'ib_contact_form_data');
    $per_page = 10;
    $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    $offset = ($current_page - 1) * $per_page;

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY submission_date DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items / $per_page);

    echo '<div class="wrap">';
    echo '<h1>الناس الي تواصلت من اي فورم في الموقع</h1>';

    echo '<a href="' . admin_url('admin-ajax.php?action=export_contact_form_data') . '" class="button button-primary" style="margin-bottom: 20px;">حمل الداتا</a>';

    echo '<table class="widefat fixed striped" style="margin-top: 20px;">';
    echo '<thead>
            <tr>
                <th>تحديد</th>
                <th>الاسم</th>
                <th>التايم زون</th>
                <th>عنوان الصفحة</th>
                <th>نوع الوحدة</th>
                <th>السعر</th>
                <th>تم التواصل</th>
                <th>عرض الليد كامل</th>
                <th>حذف</th>
            </tr>
        </thead>';
    echo '<tbody>';
    
    if (!empty($results)) {
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td><input type="checkbox" class="contacted-checkbox" data-id="' . esc_attr($row->id) . '" ' . checked($row->contacted, 1, false) . '></td>';
            echo '<td>' . esc_html($row->name) . '</td>';
            echo '<td>' . esc_html($row->time_zone) . '</td>';
            echo '<td>' . esc_html($row->page_title) . '</td>';
            echo '<td>' . esc_html($row->unit_type ?? 'غير محدد') . '</td>';
            echo '<td>' . esc_html($row->price ?? 'غير محدد') . '</td>';
            echo '<td>' . ($row->contacted ? 'نعم' : 'لا') . '</td>';
            echo '<td><button class="open-popup" data-row=\'' . json_encode($row) . '\'>عرض</button></td>';
            echo '<td><button class="delete-lead" data-id="' . esc_attr($row->id) . '">حذف</button></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="9">No submissions found.</td></tr>';
    }
    
    echo '</tbody>';
    echo '</table>';

?>

<div id="popup" style="display: none; position: fixed; top: 20%; left: 50%; transform: translate(-50%, -20%); width: 50%; background: #fff; border: 1px solid #ccc; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); z-index: 1000;">
    <button id="close-popup">X</button>
    <div id="popup-content"></div>
</div>
<div id="popup-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999;"></div>

<?php
    // الترقيم
    if ($total_pages > 1) {
        echo '<div class="paginate">';
        $base = add_query_arg('paged', '%#%');
        echo paginate_links([
            'base'      => $base,
            'format'    => '',
            'current'   => $current_page,
            'total'     => $total_pages,
            'prev_text' => __('&laquo; Previous', 'aqarround'),
            'next_text' => __('Next &raquo;', 'aqarround'),
        ]);
        echo '</div>';
    }

    echo '</div>';
}
add_action('wp_ajax_update_contacted_status', 'update_contacted_status');

// AJAX handler لحذف الليد
add_action('wp_ajax_delete_lead', 'delete_lead_callback');

function delete_lead_callback() {
    // التحقق من صلاحيات المستخدم
    if (!current_user_can('manage_options')) {
        wp_send_json_error('غير مصرح لك بهذا الإجراء');
    }

    // التحقق من وجود الـ ID
    if (!isset($_POST['lead_id']) || empty($_POST['lead_id'])) {
        wp_send_json_error('معرّف الليد غير صالح');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'ib_contact_form_data';
    $lead_id = intval($_POST['lead_id']);

    // تنفيذ عملية الحذف
    $result = $wpdb->delete(
        $table_name,
        ['id' => $lead_id],
        ['%d']
    );

    if ($result === false) {
        wp_send_json_error('فشل في حذف الليد');
    }

    wp_send_json_success('تم الحذف بنجاح');
}

function update_contacted_status() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'You do not have permission to perform this action.']);
    }

    if (!isset($_POST['id']) || !isset($_POST['contacted'])) {
        wp_send_json_error(['message' => 'Invalid request.']);
    }

    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'ib_contact_form_data');

    $id = absint($_POST['id']);
    $contacted = absint($_POST['contacted']);

    $updated = $wpdb->update(
        $table_name,
        ['contacted' => $contacted],
        ['id' => $id],
        ['%d'],
        ['%d']
    );

    if ($updated === false) {
        wp_send_json_error(['message' => 'Database update failed.']);
    }

    wp_send_json_success(['message' => 'Status updated successfully.']);
}

function export_contact_form_data() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'ib_contact_form_data');

    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY submission_date DESC"), ARRAY_A);

    if (empty($results)) {
        wp_die('No data available for export.');
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=contact_form_data.csv');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    $output = fopen('php://output', 'w');

    fputcsv($output, array(
        'ID', 
        'Name', 
        'Phone', 
        'Email', 
        'Preferred Time', 
        'Message', 
        'Time Zone', 
        'Page Title', 
        'Contact Methods',
        'Unit Type',
        'Price', 
        'Submission Date', 
        'Contacted'
    ));

    foreach ($results as $row) {
        $row['contacted'] = $row['contacted'] ? 'Yes' : 'No';
        $sanitized_row = array_map('sanitize_text_field', $row);
        fputcsv($output, $sanitized_row);
    }

    fclose($output);
    exit;
}

add_action('wp_ajax_export_contact_form_data', 'export_contact_form_data');