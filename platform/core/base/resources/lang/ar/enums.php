<?php

return [
    'statuses' => [
        'draft' => 'مسودة',
        'pending' => 'قيد الانتظار',
        'published' => 'منشور',
    ],
    'system_updater_steps' => [
        'download' => 'تحميل ملفات التحديث',
        'update_files' => 'تحديث ملفات النظام',
        'update_database' => 'تحديث قواعد البيانات',
        'publish_core_assets' => 'نشر الأصول الأساسية',
        'publish_packages_assets' => 'نشر أصول الحزم',
        'clean_up' => 'تنظيف ملفات تحديث النظام',
        'done' => 'تم تحديث النظام بنجاح',

        'messages' => [
            'download' => 'جارٍ تحميل ملفات التحديث...',
            'update_files' => 'جارٍ تحديث ملفات النظام...',
            'update_database' => 'جارٍ تحديث قواعد البيانات...',
            'publish_core_assets' => 'جارٍ نشر الأصول الأساسية...',
            'publish_packages_assets' => 'جارٍ نشر أصول الحزم...',
            'clean_up' => 'جارٍ تنظيف ملفات تحديث النظام...',
            'done' => 'تم! سيتم تحديث متصفحك في غضون 30 ثانية.',
        ],

        'failed_messages' => [
            'download' => 'تعذر تحميل ملفات التحديث',
            'update_files' => 'تعذر تحديث ملفات النظام',
            'update_database' => 'تعذر تحديث قواعد البيانات',
            'publish_core_assets' => 'تعذر نشر الأصول الأساسية',
            'publish_packages_assets' => 'تعذر نشر أصول الحزم',
            'clean_up' => 'تعذر تنظيف ملفات تحديث النظام',
        ],

        'success_messages' => [
            'download' => 'تم تحميل ملفات التحديث بنجاح.',
            'update_files' => 'تم تحديث ملفات النظام بنجاح.',
            'update_database' => 'تم تحديث قواعد البيانات بنجاح.',
            'publish_core_assets' => 'تم نشر الأصول الأساسية بنجاح.',
            'publish_packages_assets' => 'تم نشر أصول الحزم بنجاح.',
            'clean_up' => 'تم تنظيف ملفات تحديث النظام بنجاح.',
        ],
    ],
];
