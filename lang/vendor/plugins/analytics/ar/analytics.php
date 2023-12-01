<?php

return [
    'sessions' => 'الجلسات',
    'visitors' => 'الزوار',
    'pageviews' => 'مشاهدات الصفحة',
    'bounce_rate' => 'معدل الارتداد',
    'page_session' => 'صفحات/الجلسة',
    'avg_duration' => 'المدة المتوسطة',
    'percent_new_session' => 'نسبة الجلسة الجديدة',
    'new_users' => 'زوار جدد',
    'visits' => 'الزيارات',
    'views' => 'المشاهدات',
    'property_id_not_specified' => 'يجب عليك توفير معرف عرض صحيح. يمكنك العثور على الوثائق هنا: <a href="https://docs.botble.com/cms/master/plugin-analytics" target="_blank">https://docs.botble.com/cms/master/plugin-analytics</a>',
    'credential_is_not_valid' => 'بيانات اعتماد Analytics غير صالحة. يمكنك العثور على الوثائق هنا: <a href="https://docs.botble.com/cms/master/plugin-analytics" target="_blank">https://docs.botble.com/cms/master/plugin-analytics</a>',
    'start_date_can_not_before_end_date' => 'لا يمكن أن يكون تاريخ البداية :start_date قبل تاريخ الانتهاء :end_date',
    'wrong_configuration' => 'لعرض التحليلات ، تحتاج إلى الحصول على معرف عميل Google Analytics وإضافته إلى إعداداتك. <br /> كما تحتاج إلى بيانات اعتماد JSON. <br /> يمكنك العثور على الوثائق هنا: <a href="https://docs.botble.com/cms/master/plugin-analytics" target="_blank">https://docs.botble.com/cms/master/plugin-analytics</a>',
    'property_id_is_invalid' => 'معرف الخاصية غير صالح. تحقق من هنا: https://developers.google.com/analytics/devguides/reporting/data/v1/property-id',

    'settings' => [
        'title' => 'Google Analytics',
        'description' => 'ضبط بيانات الاعتماد لـ Google Analytics',
        'google_tag_id' => 'معرف Google Tag',
        'google_tag_id_placeholder' => 'مثال: G-76NX8HY29D',
        'analytics_property_id' => 'معرف خاصية GA4',
        'analytics_property_id_description' => 'معرف خاصية Google Analytics (GA4)',
        'json_credential' => 'بيانات اعتماد حساب الخدمة',
        'json_credential_description' => 'بيانات اعتماد حساب الخدمة',
    ],

    'widget_analytics_page' => 'أعلى الصفحات المزورة',
    'widget_analytics_browser' => 'أفضل المتصفحات',
    'widget_analytics_referrer' => 'أفضل الإحالات',
    'widget_analytics_general' => 'تحليلات الموقع',
    'missing_library_warning' => 'خطأ في إضافة Analytics: نقص في مكتبات الطرف الثالث ، يرجى تشغيل "composer update" لتثبيتها.'
];
