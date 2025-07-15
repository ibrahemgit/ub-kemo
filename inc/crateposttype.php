<?php

function create_thankyou_post_type() {
    $labels = array(
        'name'               => 'ثانكيو',
        'singular_name'      => 'ثانكيو',
        'menu_name'          => 'ثانكيو',
        'name_admin_bar'     => 'ثانكيو',
        'add_new'            => 'أضف جديد',
        'add_new_item'       => 'أضف مشاركة جديدة',
        'new_item'           => 'مشاركة جديدة',
        'edit_item'          => 'تعديل المشاركة',
        'view_item'          => 'عرض المشاركة',
        'all_items'          => 'كل المشاركات',
        'search_items'       => 'بحث في ثانكيو',
        'not_found'          => 'لا توجد مشاركات',
        'not_found_in_trash' => 'لا توجد مشاركات في سلة المهملات'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'thankyou'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-heart',
        'supports'           => array('title')
    );

    register_post_type('thankyou', $args);
}
add_action('init', 'create_thankyou_post_type');
