<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Messenger display name
    |--------------------------------------------------------------------------
    |
    | الإسم الذي سيظهر كعنوان للمسنجر أو اسم الصفحة
    |
    */
    'name' => env('CHATIFY_NAME', 'Admin'),

    /*
    |--------------------------------------------------------------------------
    | The disk on which to store added files
    | and derived images by default.
    |--------------------------------------------------------------------------
    |
    | تأكد أن القرص (disk) هنا هو "public"
    | حتى يتم حفظ وقراءة الملفات من مجلد storage/app/public
    | مع ضرورة تنفيذ:
    | php artisan storage:link
    |
    */
    'storage_disk_name' => env('CHATIFY_STORAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Routes configurations
    |--------------------------------------------------------------------------
    |
    | تعديل المسارات والخدمات الوسطى (middleware) التي تستخدمها Chatify
    |
    */
    'routes' => [
        'custom' => env('CHATIFY_CUSTOM_ROUTES', false),
        'prefix' => env('CHATIFY_ROUTES_PREFIX', 'chatify'),
        'middleware' => env('CHATIFY_ROUTES_MIDDLEWARE', ['web', 'auth']),
        'namespace' => env('CHATIFY_ROUTES_NAMESPACE', 'Chatify\Http\Controllers'),
    ],
    'api_routes' => [
        'prefix' => env('CHATIFY_API_ROUTES_PREFIX', 'chatify/api'),
        'middleware' => env('CHATIFY_API_ROUTES_MIDDLEWARE', ['api']),
        'namespace' => env('CHATIFY_API_ROUTES_NAMESPACE', 'Chatify\Http\Controllers\Api'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pusher API credentials
    |--------------------------------------------------------------------------
    |
    | معلومات الـPusher الخاصة بالتطبيق، تأكد من إكمالها بملف .env
    |
    */
    'pusher' => [
        'debug' => env('APP_DEBUG', false),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
            'host' => env('PUSHER_HOST') ?: 'api-' . env('PUSHER_APP_CLUSTER', 'mt1') . '.pusher.com',
            'port' => env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
            'encrypted' => true,
            'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Avatar
    |--------------------------------------------------------------------------
    |
    | إعدادات مسار صور البروفايل
    |
    */
    'user_avatar' => [
        'folder' => 'users-avatar',
        'default' => 'avatar.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Gravatar
    |--------------------------------------------------------------------------
    |
    | للتحكم في استخدام صور Gravatar وعرضها
    |
    */
    'gravatar' => [
        'enabled' => true,
        'image_size' => 200,
        'imageset' => 'identicon'
    ],

    /*
    |--------------------------------------------------------------------------
    | Attachments
    |--------------------------------------------------------------------------
    |
    | الإعدادات المتعلقة بالمرفقات (الصور/الملفات) في الدردشة
    |
    | ـ folder: يجب أن يكون مسارًا داخل public disk
    |           مثل: attachments => سيُخزَّن في storage/app/public/attachments
    |
    | ـ allowed_images: أنواع الصور المسموحة
    | ـ allowed_files: أنواع الملفات الأخرى المسموحة
    | ـ max_upload_size: الحد الأعلى بالميغابايت
    */
    'attachments' => [
        'folder' => 'attachments',  // => storage/app/public/attachments
        'download_route_name' => 'attachments.download',
        'allowed_images' => ['png', 'jpg', 'jpeg', 'gif'],
        'allowed_files' => ['zip', 'rar', 'txt'],
        'max_upload_size' => env('CHATIFY_MAX_FILE_SIZE', 150), // بالميغابايت
    ],

    /*
    |--------------------------------------------------------------------------
    | Messenger's colors
    |--------------------------------------------------------------------------
    |
    | الألوان المتاحة لاختيار واجهة المسنجر
    |
    */
    'colors' => [
        '#2180f3',
        '#2196F3',
        '#00BCD4',
        '#3F51B5',
        '#673AB7',
        '#4CAF50',
        '#FFC107',
        '#FF9800',
        '#ff2522',
        '#9C27B0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sounds
    |--------------------------------------------------------------------------
    |
    | للتحكم بصوت الإشعارات عند وصول رسالة جديدة
    |
    */
    'sounds' => [
        'enabled' => true,
        'public_path' => 'sounds/chatify',
        'new_message' => 'new-message-sound.mp3',
    ],
];
