<?php

return [

    /**
     *
     * Shared translations.
     *
     */
    'title' => 'المثبت',
    'next' => 'الخطوة التالية',
    'back' => 'السابق',
    'finish' => 'تثبيت',
    'installation' => 'التثبيت',
    'forms' => [
        'errorTitle' => 'حدثت الأخطاء التالية:',
    ],

    /**
     *
     * Home page translations.
     *
     */
    'welcome' => [
        'templateTitle' => 'مرحبًا',
        'title' => 'مرحبًا',
        'message' => 'قبل البدء، نحتاج إلى بعض المعلومات حول قاعدة البيانات. ستحتاج إلى معرفة العناصر التالية قبل المتابعة.',
        'next' => 'لنذهب',
    ],

    /**
     *
     * Requirements page translations.
     *
     */
    'requirements' => [
        'templateTitle' => 'الخطوة 1 | متطلبات الخادم',
        'title' => 'متطلبات الخادم',
        'next' => 'فحص الصلاحيات',
    ],

    /**
     *
     * Permissions page translations.
     *
     */
    'permissions' => [
        'templateTitle' => 'الخطوة 2 | الصلاحيات',
        'title' => 'الصلاحيات',
        'next' => 'تكوين البيئة',
    ],

    /**
     *
     * Environment page translations.
     *
     */
    'environment' => [
        'wizard' => [
            'templateTitle' => 'إعدادات البيئة',
            'title' => 'إعدادات البيئة',
            'form' => [
                'name_required' => 'اسم البيئة مطلوب.',
                'app_name_label' => 'عنوان الموقع',
                'app_name_placeholder' => 'عنوان الموقع',
                'app_url_label' => 'الرابط',
                'app_url_placeholder' => 'الرابط',
                'db_connection_label' => 'اتصال قاعدة البيانات',
                'db_connection_label_mysql' => 'MySQL',
                'db_connection_label_sqlite' => 'SQLite',
                'db_connection_label_pgsql' => 'PostgreSQL',
                'db_host_label' => 'مضيف قاعدة البيانات',
                'db_host_placeholder' => 'مضيف قاعدة البيانات',
                'db_port_label' => 'منفذ قاعدة البيانات',
                'db_port_placeholder' => 'منفذ قاعدة البيانات',
                'db_name_label' => 'اسم قاعدة البيانات',
                'db_name_placeholder' => 'اسم قاعدة البيانات',
                'db_username_label' => 'اسم مستخدم قاعدة البيانات',
                'db_username_placeholder' => 'اسم مستخدم قاعدة البيانات',
                'db_password_label' => 'كلمة مرور قاعدة البيانات',
                'db_password_placeholder' => 'كلمة مرور قاعدة البيانات',
                'buttons' => [
                    'install' => 'تثبيت',
                ],
                'db_host_helper' => 'إذا كنت تستخدم Laravel Sail، قم بتغيير DB_HOST إلى DB_HOST=mysql. في بعض خدمات الاستضافة، قد يكون DB_HOST localhost بدلاً من 127.0.0.1',
                'db_connections' => [
                    'mysql' => 'MySQL',
                    'sqlite' => 'SQLite',
                    'pgsql' => 'PostgreSQL',
                ],
            ],
        ],
        'success' => 'تم حفظ إعدادات ملف .env الخاص بك.',
        'errors' => 'تعذر حفظ ملف .env، يرجى إنشاؤه يدويًا.',
    ],

    'install' => 'تثبيت',

    'final' => [
        'title' => 'انتهى التثبيت',
        'templateTitle' => 'انتهى التثبيت',
        'finished' => 'تم تثبيت التطبيق بنجاح.',
        'exit' => 'انقر هنا للخروج',
    ],
    'create_account' => 'إنشاء حساب',
    'first_name' => 'الاسم الأول',
    'last_name' => 'الاسم الأخير',
    'username' => 'اسم المستخدم',
    'email' => 'البريد الإلكتروني',
    'password' => 'كلمة المرور',
    'password_confirmation' => 'تأكيد كلمة المرور',
    'create' => 'إنشاء',
    'install_success' => 'تم التثبيت بنجاح!',
];