<nav class="navbar navbar-expand-md navbar-light bg-white bb b--black-10">
    <div class="container">

        @if (theme_option('logo'))
        <a href="{{ route('public.index') }}"><img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" height="35"></a>
        @else
        <div class="brand-container tc mr2 br2">
            <a class="navbar-brand b white ma0 pa0 dib w-100" href="{{ route('public.index') }}" title="{{ theme_option('site_title') }}">{{ ucfirst(mb_substr(theme_option('site_title'), 0, 1, 'utf-8')) }}</a>
        </div>
        @endif

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto my-2">
                <!-- Authentication Links -->
                @if (!auth('account')->check())
                <li>
                    <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" href="{{ route('public.account.login') }}" style="text-decoration: none; line-height: 32px;">
                        <i class="fas fa-sign-in-alt"></i> {{ trans('plugins/real-estate::dashboard.login-cta') }}
                    </a>
                </li>
                <li>
                    <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" href="{{ route('public.account.register') }}" style="text-decoration: none; line-height: 32px;">
                        <i class="fas fa-user-plus"></i> {{ trans('plugins/real-estate::dashboard.register-cta') }}
                    </a>
                </li>
                @else
                <li>
                    <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" href="{{ route('public.account.dashboard') }}" title="{{ trans('plugins/real-estate::dashboard.header_profile_link') }}" style="text-decoration: none; line-height: 32px;">
                        <span>
                            <img class="br-100 v-mid mr1" src="{{ auth('account')->user()->avatar->url ? RvMedia::getImageUrl(auth('account')->user()->avatar->url, 'thumb') : auth('account')->user()->avatar_url }}" alt="{{ auth('account')->user()->name }}" style="width: 30px;">
                            <span>{{ auth('account')->user()->name }}</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" href="{{ route('public.account.settings') }}" title="{{ trans('plugins/real-estate::dashboard.header_settings_link') }}" style="text-decoration: none; line-height: 32px;">
                        <i class="fas fa-cogs mr1"></i>{{ trans('plugins/real-estate::dashboard.header_settings_link') }}
                    </a>
                </li>
                @if(auth('account')->user()->IsBrokerOrDeveloperAccount)
                @if (RealEstateHelper::isEnabledCreditsSystem())
                <li>
                    <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2 account-current-credit" href="{{ route('public.account.packages') }}" title="{{ trans('plugins/real-estate::account.credits') }}" style="text-decoration: none; line-height: 32px;">
                        <i class="far fa-credit-card mr1"></i>{{ trans('plugins/real-estate::account.buy_credits') }}
                        <span class="badge badge-info">{{ number_format(auth('account')->user()->credits) }}
                            {{ trans('plugins/real-estate::account.credits') }}</span>
                    </a>
                </li>
                @endif
                @endif

                {!! apply_filters(ACCOUNT_TOP_MENU_FILTER, null) !!}
                @if(auth('account')->user()->IsBrokerOrDeveloperAccount)
                <li>
                    <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" href="{{ route('public.account.properties.index') }}" title="{{ trans('plugins/real-estate::account-property.properties') }}" style="text-decoration: none; line-height: 32px;">
                        <i class="far fa-newspaper mr1"></i>{{ trans('plugins/real-estate::account-property.properties') }}
                    </a>
                </li>

                @if (auth('account')->user()->canPost())
                <li>
                    <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" href="{{ route('public.account.properties.create') }}" title="{{ trans('plugins/real-estate::account-property.write_property') }}" style="text-decoration: none; line-height: 32px;">
                        <i class="far fa-edit mr1"></i>{{ trans('plugins/real-estate::account-property.write_property') }}
                    </a>
                </li>
                @endif
                @endif
                <li>
                    <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" href="{{route('public.account.orders.index')}}" title="{{ trans('plugins/real-estate::account-property.properties') }}" style="text-decoration: none; line-height: 32px;">
                        <i class="fa fa-shopping-cart mr1"></i> {{ trans('plugins/real-estate::order.name') }}
                    </a>
                </li>
                <li>
                    <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" href="#" title="{{ trans('plugins/real-estate::dashboard.header_logout_link') }}" style="text-decoration: none; line-height: 32px;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr1"></i><span class="dn-ns">{{ trans('plugins/real-estate::dashboard.header_logout_link') }}</span>
                    </a>
                    <form id="logout-form" style="display: none;" action="{{ route('public.account.logout') }}" method="POST">
                        @csrf
                    </form>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>