<?php

namespace Botble\RealEstate\Providers;

use Botble\Api\Facades\ApiHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Facades\MacroableModels;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Language\Facades\Language;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Location\Facades\Location;
use Botble\Location\Models\City;
use Botble\Location\Models\State;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Commands\RenewPropertiesCommand;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Middleware\RedirectIfAccount;
use Botble\RealEstate\Http\Middleware\RedirectIfNotAccount;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\AccountActivityLog;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\CustomField;
use Botble\RealEstate\Models\CustomFieldOption;
use Botble\RealEstate\Models\CustomFieldValue;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Investor;
use Botble\RealEstate\Models\Invoice;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\Review;
use Botble\RealEstate\Models\Transaction;
use Botble\RealEstate\Repositories\Eloquent\AccountActivityLogRepository;
use Botble\RealEstate\Repositories\Eloquent\AccountRepository;
use Botble\RealEstate\Repositories\Eloquent\CategoryRepository;
use Botble\RealEstate\Repositories\Eloquent\ConsultRepository;
use Botble\RealEstate\Repositories\Eloquent\CurrencyRepository;
use Botble\RealEstate\Repositories\Eloquent\CustomFieldRepository;
use Botble\RealEstate\Repositories\Eloquent\FacilityRepository;
use Botble\RealEstate\Repositories\Eloquent\FeatureRepository;
use Botble\RealEstate\Repositories\Eloquent\InvestorRepository;
use Botble\RealEstate\Repositories\Eloquent\InvoiceRepository;
use Botble\RealEstate\Repositories\Eloquent\PackageRepository;
use Botble\RealEstate\Repositories\Eloquent\ProjectRepository;
use Botble\RealEstate\Repositories\Eloquent\PropertyRepository;
use Botble\RealEstate\Repositories\Eloquent\ReviewRepository;
use Botble\RealEstate\Repositories\Eloquent\TransactionRepository;
use Botble\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\ConsultInterface;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\RealEstate\Repositories\Interfaces\CustomFieldInterface;
use Botble\RealEstate\Repositories\Interfaces\FacilityInterface;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Repositories\Interfaces\InvestorInterface;
use Botble\RealEstate\Repositories\Interfaces\InvoiceInterface;
use Botble\RealEstate\Repositories\Interfaces\PackageInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RealEstate\Repositories\Interfaces\ReviewInterface;
use Botble\RealEstate\Repositories\Interfaces\TransactionInterface;
use Botble\RssFeed\Facades\RssFeed;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Slug\Facades\SlugHelper;
use Botble\SocialLogin\Facades\SocialService;
use Botble\Theme\Facades\SiteMapManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RealEstateServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->singleton(PropertyInterface::class, function () {
            return new PropertyRepository(new Property());
        });

        $this->app->singleton(ProjectInterface::class, function () {
            return new ProjectRepository(new Project());
        });

        $this->app->singleton(FeatureInterface::class, function () {
            return new FeatureRepository(new Feature());
        });

        $this->app->bind(InvestorInterface::class, function () {
            return new InvestorRepository(new Investor());
        });

        $this->app->bind(CurrencyInterface::class, function () {
            return new CurrencyRepository(new Currency());
        });

        $this->app->bind(ConsultInterface::class, function () {
            return new ConsultRepository(new Consult());
        });

        $this->app->bind(CategoryInterface::class, function () {
            return new CategoryRepository(new Category());
        });

        $this->app->bind(FacilityInterface::class, function () {
            return new FacilityRepository(new Facility());
        });

        $this->app->bind(CustomFieldInterface::class, function () {
            return new CustomFieldRepository(new CustomField());
        });

        $this->app->bind(ReviewInterface::class, function () {
            return new ReviewRepository(new Review());
        });

        $this->app->bind(InvoiceInterface::class, function () {
            return new InvoiceRepository(new Invoice());
        });

        $this->app->bind(AccountInterface::class, function () {
            return new AccountRepository(new Account());
        });

        $this->app->bind(AccountActivityLogInterface::class, function () {
            return new AccountActivityLogRepository(new AccountActivityLog());
        });

        $this->app->bind(PackageInterface::class, function () {
            return new PackageRepository(new Package());
        });

        $this->app->singleton(TransactionInterface::class, function () {
            return new TransactionRepository(new Transaction());
        });

        config([
            'auth.guards.account' => [
                'driver' => 'session',
                'provider' => 'accounts',
            ],
            'auth.providers.accounts' => [
                'driver' => 'eloquent',
                'model' => Account::class,
            ],
            'auth.passwords.accounts' => [
                'provider' => 'accounts',
                'table' => 're_account_password_resets',
                'expire' => 60,
            ],
        ]);

        $router = $this->app['router'];

        $router->aliasMiddleware('account', RedirectIfNotAccount::class);
        $router->aliasMiddleware('account.guest', RedirectIfAccount::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('RealEstateHelper', RealEstateHelper::class);
    }

    public function boot(): void
    {
        SlugHelper::registerModule(Property::class, 'Real Estate Properties');
        SlugHelper::registerModule(Category::class, 'Real Estate Property Categories');
        SlugHelper::registerModule(Project::class, 'Real Estate Projects');
        SlugHelper::registerModule(Account::class, 'Real Estate Agents');
        SlugHelper::setPrefix(Project::class, 'projects');
        SlugHelper::setPrefix(Property::class, 'properties');
        SlugHelper::setPrefix(Category::class, 'property-category');
        SlugHelper::setPrefix(Account::class, 'agents');

        $this->setNamespace('plugins/real-estate')
            ->loadAndPublishConfigurations(['permissions', 'email', 'real-estate', 'general'])
            ->loadMigrations()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadHelpers()
            ->loadRoutes()
            ->publishAssets();

        $this->app->booted(function () {
            if (is_plugin_active('location')) {
                SeoHelper::registerModule([City::class, State::class]);
            }
        });

        $this->app['events']->listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-plugins-real-estate',
                    'priority' => 5,
                    'parent_id' => null,
                    'name' => 'plugins/real-estate::real-estate.name',
                    'icon' => 'fa fa-bed',
                    'permissions' => ['projects.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-property',
                    'priority' => 0,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::property.name',
                    'icon' => null,
                    'url' => route('property.index'),
                    'permissions' => ['property.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-orders',
                    'priority' => 0,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::property.orders',
                    'icon' => null,
                    'url' => route('order.index'),//temp
                    'permissions' => ['order.index'],//temp
                ])
                ->registerItem([
                    'id' => 'cms-plugins-project',
                    'priority' => 1,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::project.name',
                    'icon' => null,
                    'url' => route('project.index'),
                    'permissions' => ['project.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-re-feature',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::feature.name',
                    'icon' => null,
                    'url' => route('property_feature.index'),
                    'permissions' => ['property_feature.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-facility',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::facility.name',
                    'icon' => null,
                    'url' => route('facility.index'),
                    'permissions' => ['facility.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-investor',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::investor.name',
                    'icon' => null,
                    'url' => route('investor.index'),
                    'permissions' => ['investor.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-real-estate-settings',
                    'priority' => 999,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::real-estate.settings',
                    'icon' => null,
                    'url' => route('real-estate.settings'),
                    'permissions' => ['real-estate.settings'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-consult',
                    'priority' => 6,
                    'parent_id' => null,
                    'name' => 'plugins/real-estate::consult.name',
                    'icon' => 'fas fa-headset',
                    'url' => route('consult.index'),
                    'permissions' => ['consult.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-real-estate-category',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::category.name',
                    'icon' => null,
                    'url' => route('property_category.index'),
                    'permissions' => ['property_category.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-real-estate-account',
                    'priority' => 22,
                    'parent_id' => null,
                    'name' => 'plugins/real-estate::account.name',
                    'icon' => 'fa fa-users',
                    'url' => route('account.index'),
                    'permissions' => ['account.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-real-estate-invoice',
                    'priority' => 7,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::invoice.name',
                    'url' => route('invoices.index'),
                    'permissions' => ['invoice.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-real-estate-invoice-template',
                    'priority' => 8,
                    'parent_id' => 'cms-plugins-real-estate',
                    'name' => 'plugins/real-estate::invoice.template.name',
                    'url' => route('invoice-template.index'),
                    'permissions' => ['invoice.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-real-estate-coupons',
                    'priority' => 14,
                    'parent_id' => null,
                    'name' => 'plugins/real-estate::coupon.name',
                    'icon' => 'fas fa-percent',
                    'url' => route('coupons.index'),
                    'permissions' => ['real-estate.coupons.index'],
                ]);

            if (RealEstateHelper::isEnabledCustomFields()) {
                dashboard_menu()
                    ->registerItem([
                        'id' => 'cms-plugins-real-estate-custom-fields',
                        'priority' => 13,
                        'parent_id' => 'cms-plugins-real-estate',
                        'name' => 'plugins/real-estate::custom-fields.name',
                        'icon' => null,
                        'url' => route('real-estate.custom-fields.index'),
                        'permissions' => ['real-estate.custom-fields.index'],
                    ]);
            }

            if (RealEstateHelper::isEnabledCreditsSystem()) {
                dashboard_menu()
                    ->registerItem([
                        'id' => 'cms-plugins-package',
                        'priority' => 23,
                        'parent_id' => null,
                        'name' => 'plugins/real-estate::package.name',
                        'icon' => 'fas fa-money-check-alt',
                        'url' => route('package.index'),
                        'permissions' => ['package.index'],
                    ]);
            }

            if (RealEstateHelper::isEnabledReview()) {
                dashboard_menu()
                    ->registerItem([
                        'id' => 'cms-plugins-real-estate-review',
                        'priority' => 5,
                        'parent_id' => 'cms-plugins-real-estate',
                        'name' => 'plugins/real-estate::review.name',
                        'icon' => null,
                        'url' => route('review.index'),
                        'permissions' => ['review.index'],
                    ]);
            }

            add_filter(IS_IN_ADMIN_FILTER, [$this, 'setInAdmin'], 128);
        });

        if (class_exists('ApiHelper')) {
            ApiHelper::setConfig([
                'model' => Account::class,
                'guard' => 'account',
                'password_broker' => 'accounts',
                'verify_email' => setting('verify_account_email', false),
            ]);
        }

        $this->app->register(CommandServiceProvider::class);

        SiteMapManager::registerKey([
            'properties-((?:19|20|21|22)\d{2})-(0?[1-9]|1[012])',
            'projects-((?:19|20|21|22)\d{2})-(0?[1-9]|1[012])',
            'property-categories',
            'agents',
            'properties-city',
            'projects-city',
        ]);

        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            if (
                defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME') &&
                $this->app['config']->get('plugins.real-estate.real-estate.use_language_v2')
            ) {
                $this->loadRoutes(['language-advanced']);

                LanguageAdvancedManager::registerModule(Property::class, [
                    'name',
                    'description',
                    'content',
                    'location',
                ]);

                LanguageAdvancedManager::registerModule(Project::class, [
                    'name',
                    'description',
                    'content',
                    'location',
                ]);

                LanguageAdvancedManager::registerModule(Category::class, [
                    'name',
                    'description',
                ]);

                LanguageAdvancedManager::registerModule(Feature::class, [
                    'name',
                ]);

                LanguageAdvancedManager::registerModule(Facility::class, [
                    'name',
                ]);

                LanguageAdvancedManager::registerModule(Package::class, [
                    'name',
                ]);

                LanguageAdvancedManager::registerModule(CustomField::class, [
                    'name',
                    'type',
                ]);

                LanguageAdvancedManager::registerModule(CustomFieldOption::class, [
                    'label',
                    'value',
                ]);

                LanguageAdvancedManager::registerModule(CustomFieldValue::class, [
                    'name',
                    'value',
                ]);

                LanguageAdvancedManager::addTranslatableMetaBox('custom_fields_box');

                add_action(LANGUAGE_ADVANCED_ACTION_SAVED, function ($data, $request) {
                    switch (get_class($data)) {
                        case Property::class:
                        case Project::class:
                            $options = $request->input('custom_fields', []) ?: [];

                            if (! $options) {
                                return;
                            }

                            foreach ($options as $value) {
                                $newRequest = new Request();

                                $newRequest->replace([
                                    'language' => $request->input('language'),
                                    'ref_lang' => Language::getRefLang(),
                                ]);

                                if (! $value['id']) {
                                    continue;
                                }

                                $optionValue = CustomFieldValue::query()->find($value['id']);

                                if ($optionValue) {
                                    $newRequest->merge([
                                        'name' => $value['name'],
                                        'value' => $value['value'],
                                    ]);

                                    LanguageAdvancedManager::save($optionValue, $newRequest);
                                }
                            }

                            break;
                        case CustomField::class:

                            $customFieldOptions = $request->input('options', []) ?: [];

                            if (! $customFieldOptions) {
                                return;
                            }

                            $newRequest = new Request();

                            $newRequest->replace([
                                'language' => $request->input('language'),
                                'ref_lang' => $request->input('ref_lang'),
                            ]);

                            foreach ($customFieldOptions as $option) {
                                if (empty($option['id'])) {
                                    continue;
                                }

                                $customFieldOption = CustomFieldOption::query()->find($option['id']);

                                if ($customFieldOption) {
                                    $newRequest->merge([
                                        'label' => $option['label'],
                                        'value' => $option['value'],
                                    ]);

                                    LanguageAdvancedManager::save($customFieldOption, $newRequest);
                                }
                            }

                            break;
                    }
                }, 1234, 2);
            } else {
                Language::registerModule([
                    Property::class,
                    Project::class,
                    Feature::class,
                    Investor::class,
                    Category::class,
                    Facility::class,
                ]);
            }
        }

        if (is_plugin_active('location')) {
            Location::registerModule(Property::class);
            Location::registerModule(Project::class);
            Location::registerModule(Account::class);
        } else {
            MacroableModels::addMacro(Property::class, 'getFullAddressAttribute', function () {
                return $this->address;
            });

            MacroableModels::addMacro(Project::class, 'getFullAddressAttribute', function () {
                return $this->address;
            });
        }

        $this->app->booted(function () {
            if (defined('SOCIAL_LOGIN_MODULE_SCREEN_NAME') && Route::has('public.account.login')) {
                SocialService::registerModule([
                    'guard' => 'account',
                    'model' => Account::class,
                    'login_url' => route('public.account.login'),
                    'redirect_url' => route('public.account.dashboard'),
                ]);
            }
        });

        $this->app->booted(function () {
            SeoHelper::registerModule([
                Property::class,
                Project::class,
            ]);

            EmailHandler::addTemplateSettings(REAL_ESTATE_MODULE_SCREEN_NAME, config('plugins.real-estate.email', []));
        });

        $this->app->register(HookServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        if (is_plugin_active('rss-feed') && Route::has('feeds.properties')) {
            RssFeed::addFeedLink(route('feeds.properties'), 'Properties feed');
        }

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule
                ->command(RenewPropertiesCommand::class)
                ->dailyAt('23:30');
        });
    }

    public function setInAdmin(bool $isInAdmin): bool
    {
        $isInAdmin = in_array('account', Route::current()->middleware()) || $isInAdmin;

        if ($isInAdmin) {
            OptimizerHelper::disable();
        }

        return $isInAdmin;
    }
}
