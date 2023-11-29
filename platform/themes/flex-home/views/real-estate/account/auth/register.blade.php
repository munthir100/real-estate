<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 auth-form-wrapper">
            <div class="card login-form">
                <div class="card-body">
                    <h4 class="text-center">{{ trans('plugins/real-estate::dashboard.register-title') }}</h4>
                    <br>
                    @include(Theme::getThemeNamespace() . '::views.real-estate.account.auth.includes.messages')
                    <br>
                    <form method="POST" action="{{ route('public.account.register') }}">
                        @csrf

                        <div class="form-group">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="registerByEmail" name="registrationType" class="custom-control-input" value="email" data-type="email" checked>
                                <label class="custom-control-label" for="registerByEmail">{{ trans('plugins/real-estate::dashboard.email') }}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="registerByPhone" name="registrationType" class="custom-control-input" value="phone" data-type="phone">
                                <label class="custom-control-label" for="registerByPhone">{{ trans('plugins/real-estate::dashboard.phone') }}</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control{{ $errors->has('full_name') ? ' is-invalid' : '' }}" name="full_name" value="{{ old('full_name') }}" placeholder="{{__('Full Name')}}">
                            @if ($errors->has('full_name'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('full_name') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group" id="emailField">
                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                            @if ($errors->has('email'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group mb-3" id="phoneField" style="display: none;">
                            <input id="phone" type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" placeholder="+966********">
                            @if ($errors->has('phone'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="********">
                            @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="********">
                        </div>

                        <div class="form-group">
                            <select id="account_type_id" class="form-control{{ $errors->has('account_type') ? ' is-invalid' : '' }}" name="account_type_id" required>
                                <option value="" selected>{{__('select account type')}}</option>
                                <option value="1">{{__('Broker')}}</option>
                                <option value="2">{{__('Seeker')}}</option>
                                <option value="3">{{__('Developer')}}</option>
                            </select>
                            @if ($errors->has('account_type'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('account_type') }}</strong>
                            </span>
                            @endif
                        </div>


                        @if (is_plugin_active('captcha') && setting('enable_captcha') && setting('real_estate_enable_recaptcha_in_register_page', 0))
                        <div class="form-group mb-3">
                            {!! Captcha::display() !!}
                        </div>
                        @endif

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-full fw6">
                                {{ trans('plugins/real-estate::dashboard.register-cta') }}
                            </button>
                        </div>

                        <div class="form-group text-center">
                            <p>{{ __('Have an account already?') }} <a href="{{ route('public.account.login') }}" class="d-block d-sm-inline-block text-sm-left text-center">{{ __('Login') }}</a></p>
                        </div>

                        <div class="text-center">
                            {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\RealEstate\Models\Account::class) !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var emailField = document.getElementById('emailField');
        var phoneField = document.getElementById('phoneField');
        var registrationType = document.getElementsByName('registrationType');

        registrationType.forEach(function(radio) {
            radio.addEventListener('change', function() {
                emailField.style.display = (this.dataset.type === 'email') ? 'block' : 'none';
                phoneField.style.display = (this.dataset.type === 'phone') ? 'block' : 'none';
            });
        });

        // Initial state
        if (document.querySelector('input[name="registrationType"]:checked')) {
            var initialType = document.querySelector('input[name="registrationType"]:checked').dataset.type;
            emailField.style.display = (initialType === 'email') ? 'block' : 'none';
            phoneField.style.display = (initialType === 'phone') ? 'block' : 'none';
        }
    });
</script>