<?php 
function check_theme_update_from_github_json($transient) {

    // تحديد اسم الثيم
    $theme_slug = 'ub-kemo-main';

    // صفحات مسموح فيها التحقق من التحديث
    $allowed_pages = [
        'themes.php',
        'update-core.php',
        'update.php',
        'admin-ajax.php',
    ];

    $current_page = basename($_SERVER['PHP_SELF']);

    // ✅ لو مش في صفحة ضرورية، لا تعمل شيء
    if (!in_array($current_page, $allowed_pages)) {
        return $transient;
    }

    // ✅ تأكد أن هناك ثيمات مثبتة
    if (empty($transient->checked)) {
        return $transient;
    }

    $theme_data = wp_get_theme($theme_slug);
    $current_version = $theme_data->get('Version');

    // رابط ملف JSON على GitHub
    $json_url = 'https://raw.githubusercontent.com/ibrahemgit/ub-kemo/refs/heads/main/theme-update.json';

    // جلب بيانات التحديث
    $response = wp_remote_get($json_url, array(
        'headers' => array(
            'Accept' => 'application/json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
        )
    ));

    if (is_wp_error($response)) {
        return $transient;
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    if ($code !== 200 || empty($body)) {
        return $transient;
    }

    $data = json_decode($body);

    if (!isset($data->version) || !isset($data->download_url)) {
        return $transient;
    }

    // مقارنة النسخة الحالية بالجديدة
    if (version_compare($current_version, $data->version, '<')) {
        $transient->response[$theme_slug] = array(
            'theme'       => $theme_slug,
            'new_version' => $data->version,
            'url'         => isset($data->details_url) ? $data->details_url : '',
            'package'     => $data->download_url
        );
    }

    return $transient;
}

add_filter('site_transient_update_themes', 'check_theme_update_from_github_json');

