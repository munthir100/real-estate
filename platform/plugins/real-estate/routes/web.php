<?php

use Botble\Location\Models\City;
use Botble\Location\Models\State;
use Botble\Base\Facades\BaseHelper;
use Botble\Slug\Facades\SlugHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Project;
use Illuminate\Support\Facades\Route;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Controllers\CouponController;
use Botble\RealEstate\Http\Controllers\InvoiceController;
use Botble\RealEstate\Http\Controllers\AccountOtpController;
use Botble\RealEstate\Http\Controllers\CustomFieldController;
use Botble\RealEstate\Http\Controllers\AccountOrderController;
use Botble\RealEstate\Http\Controllers\Fronts\ReviewController;
use Botble\RealEstate\Http\Controllers\PublicAccountController;
use Botble\RealEstate\Http\Controllers\DuplicateOrderController;
use Botble\RealEstate\Http\Controllers\OrderdPropertiesController;
use Botble\RealEstate\Http\Controllers\Fronts\CouponController as CouponControllerFront;

Route::group(['namespace' => 'Botble\RealEstate\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group([
        'prefix' => BaseHelper::getAdminPrefix() . '/real-estate',
        'middleware' => 'auth',
    ], function () {
        Route::group(['prefix' => 'settings', 'as' => 'real-estate.'], function () {
            Route::get('', [
                'as' => 'settings',
                'uses' => 'RealEstateController@getSettings',
            ]);

            Route::post('', [
                'as' => 'settings.post',
                'uses' => 'RealEstateController@postSettings',
                'permission' => 'real-estate.settings',
            ]);
        });

        Route::group(['prefix' => 'properties', 'as' => 'property.'], function () {
            Route::resource('', 'PropertyController')
                ->parameters(['' => 'property']);

            Route::post('duplicate-property/{id}', [
                'as' => 'duplicate-property',
                'uses' => 'DuplicatePropertyController@__invoke',
                'permission' => 'property.edit',
            ]);
        });

        Route::group(['prefix' => 'orders', 'as' => 'order.'], function () {
            Route::resource('', OrderdPropertiesController::class)
                ->parameters(['' => 'order']);

            Route::post('duplicate-property/{id}', [
                'as' => 'duplicate-property',
                'uses' => 'DuplicatePropertyController@__invoke',
                'permission' => 'property.edit',
            ]);
            Route::post('duplicate-order/{id}', [
                'as' => 'duplicate-order',
                'uses' => DuplicateOrderController::class, '__invoke',
                'permission' => 'order.edit',
            ]);
        });

        Route::group(['prefix' => 'projects', 'as' => 'project.'], function () {
            Route::resource('', 'ProjectController')
                ->parameters(['' => 'project']);
        });

        Route::group(['prefix' => 'property-features', 'as' => 'property_feature.'], function () {
            Route::resource('', 'FeatureController')
                ->parameters(['' => 'property_feature']);
        });

        Route::group(['prefix' => 'investors', 'as' => 'investor.'], function () {
            Route::resource('', 'InvestorController')
                ->parameters(['' => 'investor']);
        });

        Route::group(['prefix' => 'consults', 'as' => 'consult.'], function () {
            Route::resource('', 'ConsultController')
                ->parameters(['' => 'consult'])
                ->except(['create', 'store']);
        });

        Route::group(['prefix' => 'categories', 'as' => 'property_category.'], function () {
            Route::resource('', 'CategoryController')
                ->parameters(['' => 'category']);
        });

        Route::group(['prefix' => 'facilities', 'as' => 'facility.'], function () {
            Route::resource('', 'FacilityController')
                ->parameters(['' => 'facility']);
        });

        Route::group(['prefix' => 'accounts', 'as' => 'account.'], function () {
            Route::resource('', 'AccountController')
                ->parameters(['' => 'account']);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'AccountController@getList',
                'permission' => 'account.index',
            ]);

            Route::post('credits/{id}', [
                'as' => 'credits.add',
                'uses' => 'TransactionController@postCreate',
                'permission' => 'account.edit',
            ])->wherePrimaryKey();
        });

        Route::group(['prefix' => 'packages', 'as' => 'package.'], function () {
            Route::resource('', 'PackageController')
                ->parameters(['' => 'package']);
        });

        Route::group(['prefix' => 'reviews', 'as' => 'review.'], function () {
            Route::resource('', 'ReviewController')->parameters(['' => 'review'])->only(['index', 'destroy']);
        });

        Route::prefix('custom-fields')->name('real-estate.custom-fields.')->group(function () {
            Route::resource('', CustomFieldController::class)->parameters(['' => 'custom-field']);

            Route::get('info', [
                'as' => 'get-info',
                'uses' => 'CustomFieldController@getInfo',
                'permission' => false,
            ]);
        });

        Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function () {
            Route::resource('', 'InvoiceController')->parameters(['' => 'invoice'])->except(['edit', 'update']);
            Route::get('{id}', [InvoiceController::class, 'show'])
                ->name('show')
                ->wherePrimaryKey();
            Route::get('{id}/generate', [InvoiceController::class, 'generate'])
                ->name('generate')
                ->wherePrimaryKey();
        });

        Route::prefix('invoice-template')->name('invoice-template.')->group(function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'InvoiceTemplateController@index',
                'permission' => 'invoice.index',
            ]);

            Route::put('/', [
                'as' => 'update',
                'uses' => 'InvoiceTemplateController@update',
                'permission' => 'invoice.index',
            ]);

            Route::post('reset', [
                'as' => 'reset',
                'uses' => 'InvoiceTemplateController@reset',
                'permission' => 'invoice.index',
            ]);

            Route::get('preview', [
                'as' => 'preview',
                'uses' => 'InvoiceTemplateController@preview',
                'permission' => 'invoice.index',
            ]);
        });

        Route::group(['prefix' => 'import/properties', 'as' => 'import-properties.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'PropertyImportController@index',
            ]);

            Route::post('/', [
                'as' => 'store',
                'uses' => 'PropertyImportController@store',
                'permission' => 'import-properties.index',
            ]);

            Route::post('download-template', [
                'as' => 'download-template',
                'uses' => 'PropertyImportController@downloadTemplate',
                'permission' => 'import-properties.index',
            ]);
        });

        Route::group(['prefix' => 'import/projects', 'as' => 'import-projects.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ProjectImportController@index',
            ]);

            Route::post('/', [
                'as' => 'store',
                'uses' => 'ProjectImportController@store',
                'permission' => 'import-projects.index',
            ]);

            Route::post('download-template', [
                'as' => 'download-template',
                'uses' => 'ProjectImportController@downloadTemplate',
                'permission' => 'import-projects.index',
            ]);
        });

        Route::group(['prefix' => 'export/properties', 'as' => 'export-properties.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ExportPropertyController@index',
                'permission' => 'export-properties.index',
            ]);

            Route::post('/', [
                'as' => 'index.post',
                'uses' => 'ExportPropertyController@export',
                'permission' => 'export-properties.index',
            ]);
        });

        Route::group(['prefix' => 'export/projects', 'as' => 'export-projects.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ExportProjectController@index',
                'permission' => 'export-projects.index',
            ]);

            Route::post('/', [
                'as' => 'index.post',
                'uses' => 'ExportProjectController@export',
                'permission' => 'export-projects.index',
            ]);
        });

        Route::group(['prefix' => 'coupons', 'as' => 'coupons.'], function () {
            Route::resource('', CouponController::class)
                ->parameters(['' => 'coupon']);

            Route::post('generate-coupon', [
                'as' => 'generate-coupon',
                'uses' => 'CouponController@generateCouponCode',
                'permission' => 'coupons.index',
            ]);

            Route::delete('deletes', [
                'as' => 'deletes',
                'uses' => 'CouponController@deletes',
                'permission' => 'coupons.destroy',
            ]);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
            Route::match(theme_option('projects_list_page_id') ? ['POST'] : ['POST', 'GET'], SlugHelper::getPrefix(Project::class, 'projects'), 'PublicController@getProjects')
                ->name('public.projects');

            Route::match(theme_option('properties_list_page_id') ? ['POST'] : ['POST', 'GET'], SlugHelper::getPrefix(Property::class, 'properties'), 'PublicController@getProperties')
                ->name('public.properties');

            Route::get(SlugHelper::getPrefix(Project::class, 'projects') . '/{slug}', 'PublicController@getProject');

            Route::get(SlugHelper::getPrefix(Property::class, 'properties') . '/{slug}', 'PublicController@getProperty');

            Route::get(SlugHelper::getPrefix(Category::class, 'property-category') . '/{slug}', 'PublicController@getPropertyCategory')
                ->name('public.property-category');

            Route::match(['POST', 'GET'], SlugHelper::getPrefix(Project::class, 'projects') . '/' . SlugHelper::getPrefix(City::class, 'city') . '/{slug?}', 'PublicController@getProjectsByCity')
                ->name('public.projects-by-city');

            Route::match(['POST', 'GET'], SlugHelper::getPrefix(Property::class, 'properties') . '/' . SlugHelper::getPrefix(City::class, 'city') . '/{slug?}', 'PublicController@getPropertiesByCity')
                ->name('public.properties-by-city');

            Route::match(['POST', 'GET'], SlugHelper::getPrefix(Project::class, 'projects') . '/' . SlugHelper::getPrefix(State::class, 'state') . '/{slug?}', 'PublicController@getProjectsByState')
                ->name('public.projects-by-state');

            Route::match(['POST', 'GET'], SlugHelper::getPrefix(Property::class, 'properties') . '/' . SlugHelper::getPrefix(State::class, 'state') . '/{slug?}', 'PublicController@getPropertiesByState')
                ->name('public.properties-by-state');

            if (!RealEstateHelper::isDisabledPublicProfile()) {
                Route::get(SlugHelper::getPrefix(Account::class, 'agents'), 'PublicController@getAgents')->name(
                    'public.agents'
                );
                Route::get(
                    SlugHelper::getPrefix(Account::class, 'agents') . '/{username}',
                    'PublicController@getAgent'
                )->name('public.agent');
            }

            Route::post('send-consult', 'PublicController@postSendConsult')
                ->name('public.send.consult');

            Route::get('currency/switch/{code?}', [
                'as' => 'public.change-currency',
                'uses' => 'PublicController@changeCurrency',
            ]);

            Route::group(['as' => 'public.account.'], function () {
                Route::group(['middleware' => ['account.guest']], function () {
                    Route::get('login', 'LoginController@showLoginForm')
                        ->name('login');
                    Route::post('login', 'LoginController@login')
                        ->name('login.post');

                    Route::get('register', 'RegisterController@showRegistrationForm')
                        ->name('register');
                    Route::post('register', 'RegisterController@register')
                        ->name('register.post');

                    Route::get('otp/verify', [AccountOtpController::class,'showOtpVerificationForm'])->name('otp.form');
                    Route::post('otp/verify', [AccountOtpController::class,'verifyOtp'])->name('otp.verify');

                    Route::get('verify', 'RegisterController@getVerify')
                        ->name('verify');

                    Route::get(
                        'password/request',
                        'ForgotPasswordController@showLinkRequestForm'
                    )
                        ->name('password.request');
                    Route::post(
                        'password/email',
                        'ForgotPasswordController@sendResetLinkEmail'
                    )
                        ->name('password.email');
                    Route::post('password/reset', 'ResetPasswordController@reset')
                        ->name('password.update');
                    Route::get(
                        'password/reset/{token}',
                        'ResetPasswordController@showResetForm'
                    )
                        ->name('password.reset');
                });

                Route::group([
                    'middleware' => [
                        setting(
                            'verify_account_email',
                            false
                        ) ? 'account.guest' : 'account',
                    ],
                ], function () {
                    Route::get(
                        'register/confirm/resend',
                        'RegisterController@resendConfirmation'
                    )
                        ->name('resend_confirmation');
                    Route::get('register/confirm/{user}', 'RegisterController@confirm')
                        ->name('confirm');
                });
            });

            Route::get('feed/properties', [
                'as' => 'feeds.properties',
                'uses' => 'PublicController@getPropertyFeeds',
            ]);

            Route::get('feed/projects', [
                'as' => 'feeds.projects',
                'uses' => 'PublicController@getProjectFeeds',
            ]);

            Route::group(['middleware' => ['account'], 'as' => 'public.account.'], function () {
                Route::group(['prefix' => 'account'], function () {
                    Route::post('logout', 'LoginController@logout')
                        ->name('logout');

                    Route::get('dashboard', [
                        'as' => 'dashboard',
                        'uses' => 'PublicAccountController@getDashboard',
                    ]);

                    Route::get('settings', [
                        'as' => 'settings',
                        'uses' => 'PublicAccountController@getSettings',
                    ]);

                    Route::post('settings', [
                        'as' => 'post.settings',
                        'uses' => 'PublicAccountController@postSettings',
                    ]);

                    Route::get('security', [
                        'as' => 'security',
                        'uses' => 'PublicAccountController@getSecurity',
                    ]);

                    Route::put('security', [
                        'as' => 'post.security',
                        'uses' => 'PublicAccountController@postSecurity',
                    ]);

                    Route::post('avatar', [
                        'as' => 'avatar',
                        'uses' => 'PublicAccountController@postAvatar',
                    ]);

                    Route::get('packages', [
                        'as' => 'packages',
                        'uses' => 'PublicAccountController@getPackages',
                    ]);

                    Route::post('account/convert/broker', [PublicAccountController::class, 'convertToBroker'])
                        ->name('convert.broker');

                    Route::post('account/convert/developer', [PublicAccountController::class, 'convertToDeveloper'])
                        ->name('convert.developer');

                    Route::get('transactions', [
                        'as' => 'transactions',
                        'uses' => 'PublicAccountController@getTransactions',
                    ]);


                    Route::prefix('coupon')->name('coupon.')->group(function () {
                        Route::post('apply', [CouponControllerFront::class, 'apply'])->name('apply');
                        Route::post('remove', [CouponControllerFront::class, 'remove'])->name('remove');
                        Route::get('refresh/{id}', [CouponControllerFront::class, 'refresh'])->name('refresh');
                    });
                });

                Route::group(['prefix' => 'account/ajax'], function () {
                    Route::get('activity-logs', [
                        'as' => 'activity-logs',
                        'uses' => 'PublicAccountController@getActivityLogs',
                    ]);

                    Route::get('transactions', [
                        'as' => 'ajax.transactions',
                        'uses' => 'PublicAccountController@ajaxGetTransactions',
                    ]);

                    Route::post('upload', [
                        'as' => 'upload',
                        'uses' => 'PublicAccountController@postUpload',
                    ]);

                    Route::post('upload-from-editor', [
                        'as' => 'upload-from-editor',
                        'uses' => 'PublicAccountController@postUploadFromEditor',
                    ]);

                    Route::get('packages', 'PublicAccountController@ajaxGetPackages')
                        ->name('ajax.packages');
                    Route::put('packages', 'PublicAccountController@ajaxSubscribePackage')
                        ->name('ajax.package.subscribe');
                });

                Route::group(['prefix' => 'account/orders', 'as' => 'orders.'], function () {
                    Route::resource('', AccountOrderController::class)->parameters(['' => 'order']);
                });
                Route::group(['prefix' => 'account/properties', 'as' => 'properties.'], function () { // properties in dashboard
                    Route::resource('', 'AccountPropertyController')
                        ->parameters(['' => 'property']);



                    Route::post('renew/{id}', [
                        'as' => 'renew',
                        'uses' => 'AccountPropertyController@renew',
                    ])->wherePrimaryKey();
                });

                Route::group(['prefix' => 'account'], function () {
                    Route::get('packages/{id}/subscribe', 'PublicAccountController@getSubscribePackage')
                        ->name('package.subscribe')
                        ->wherePrimaryKey();

                    Route::get('packages/{id}/subscribe/callback', 'PublicAccountController@getPackageSubscribeCallback')
                        ->name('package.subscribe.callback')
                        ->wherePrimaryKey();
                });

                Route::group(['prefix' => 'account/invoices', 'as' => 'invoices.', 'controller' => 'Fronts\InvoiceController'], function () {
                    Route::match(['GET', 'POST'], '/', 'index')->name('index');
                    Route::get('{id}', 'show')->name('show')
                        ->wherePrimaryKey();
                    Route::get('{id}/generate', [InvoiceController::class, 'generate'])->name('generate')
                        ->wherePrimaryKey();
                });

                Route::prefix('account/custom-fields')->name('custom-fields.')->group(function () {
                    Route::get('info', [CustomFieldController::class, 'getInfo'])->name('get-info');
                });
            });

            Route::middleware('account')->group(function () {
                Route::post('ajax/review/{slug}', [ReviewController::class, 'store'])->name('public.ajax.review.store');
            });

            Route::get('ajax/review/{slug}', [ReviewController::class, 'index'])->name('public.ajax.review.index');
        });
    }

    Route::group(['prefix' => 'payments'], function () {
        Route::post('checkout', 'CheckoutController@postCheckout')->name('payments.checkout');
    });
});
