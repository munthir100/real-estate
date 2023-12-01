<?php

return [
    'webhook_secret' => 'سر الويبهوك',
    'webhook_setup_guide' => [
        'title' => 'دليل إعداد ويبهوك سترايب',
        'description' => 'اتبع هذه الخطوات لإعداد ويبهوك سترايب',
        'step_1_label' => 'تسجيل الدخول إلى لوحة التحكم في سترايب',
        'step_1_description' => 'قم بزيارة :link وانقر على زر "Add Endpoint" في قسم "Webhooks" ضمن علامة "Developers".',
        'step_2_label' => 'تحديد الحدث وتكوين النقطة النهائية',
        'step_2_description' => 'حدد حدث "payment_intent.succeeded" وأدخل الرابط التالي في حقل "Endpoint URL": :url',
        'step_3_label' => 'إضافة النقطة النهائية',
        'step_3_description' => 'انقر على زر "Add Endpoint" لحفظ ويبهوك.',
        'step_4_label' => 'نسخ السر التوقيعي',
        'step_4_description' => 'انسخ قيمة "Signing Secret" من قسم "Webhook Details" والصقها في حقل "Stripe Webhook Secret" في قسم "Stripe" ضمن علامة "Payment" في صفحة "Settings".',
    ]
];
