@extends('plugins/real-estate::account.layouts.skeleton')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card login-form">
                <div class="card-body">
                    <h4 class="text-center">{{ trans('plugins/real-estate::dashboard.verify_require_desc') }}</h4>
                    <br>
                    <form method="POST" action="{{ route('public.account.otp.verify') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <input class="form-control{{ $errors->has('otp') ? ' is-invalid' : '' }}" id="otp" name="otp" type="text" value="{{ old('otp') }}" required autofocus placeholder="********">
                            @if ($errors->has('otp'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('otp') }}</strong>
                            </span>
                            @endif
                        </div>




                        <div class="form-group mb-0">
                            <button class="btn btn-blue btn-full fw6" type="submit">
                                {{ __('save') }}
                            </button>
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
@endsection
@push('scripts')
<!-- Laravel Javascript Validation -->
<script type="text/javascript" src="{{ asset('vendor/core/core/js-validation/js/js-validation.js') }}"></script>
{!! JsValidator::formRequest(\Botble\RealEstate\Http\Requests\RegisterRequest::class) !!}
@endpush