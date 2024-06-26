@extends('plugins/real-estate::account.layouts.skeleton')
@section('content')
<div class="settings crop-avatar">
    <div class="container">
        <div class="row">
            @include('plugins/real-estate::account.settings.sidebar')
            <div class="col-12 col-md-9">
                <div class="main-dashboard-form">
                    <!-- Setting Title -->
                    <div class="row">
                        <div class="col-12">
                            <h4 class="with-actions">{{ trans('plugins/real-estate::dashboard.account_field_title') }}
                            </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 order-lg-12">
                            <form id="avatar-upload-form" enctype="multipart/form-data" action="javascript:void(0)" onsubmit="return false">
                                <div class="avatar-upload-container">
                                    <div class="form-group mb-3">
                                        <label for="account-avatar">{{ trans('plugins/real-estate::dashboard.profile-picture') }}</label>
                                        <div id="account-avatar">
                                            <div class="profile-image">
                                                <div class="avatar-view mt-card-avatar">
                                                    <img class="br2" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 200px;">
                                                    <div class="mt-overlay br2">
                                                        <span><i class="fa fa-edit"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Print messages -->
                                    <div class="alert dn" id="print-msg"></div>
                                </div>
                            </form>
                            <div class="col-12 mb-2">
                                <div class="row">
                                    @if(!$user->isBrokerAccount)
                                    <div class="col-6">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#become-a-broker" class="btn btn-primary btn-sm">{{__('Become a Broker')}}</button>
                                    </div>
                                    @endif
                                    @if(!$user->isDeveloperAccount)
                                    <div class="col-6">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#become-a-developer" class="btn btn-primary btn-sm">{{__('Become a Developer')}}</button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8 order-lg-0">
                            @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button class="btn-close" data-bs-dismiss="alert" type="button" aria-label="Close">
                                </button>
                            </div>
                            @endif
                            <form id="setting-form" action="{{ route('public.account.post.settings') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <!-- Name -->
                                <div class="form-group mb-3">
                                    <label for="full_name">{{ trans('plugins/real-estate::dashboard.full_name') }}</label>
                                    <input class="form-control" id="full_name" name="full_name" type="text" value="{{ old('full_name') ?? $user->full_name }}" required>
                                </div>
                                <!-- Name -->

                                <div class="form-group mb-3">
                                    <label for="username">{{ trans('plugins/real-estate::dashboard.username') }}</label>
                                    <input class="form-control" id="username" name="username" type="text" value="{{ old('username') ?? $user->username }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="company">{{ trans('plugins/real-estate::dashboard.company') }}</label>
                                    <input class="form-control" id="company" name="company" type="text" value="{{ old('company') ?? $user->company }}" required>
                                </div>
                                <!-- Phone -->
                                <div class="form-group mb-3">
                                    <label for="phone">{{ trans('plugins/real-estate::dashboard.phone') }}</label>
                                    <input class="form-control" id="phone" name="phone" type="text" value="{{ old('phone') ?? $user->phone }}" required>
                                </div>
                                <!--Short description-->
                                <div class="form-group mb-3">
                                    <label for="description">{{ trans('plugins/real-estate::dashboard.description') }}</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" maxlength="300" placeholder="{{ trans('plugins/real-estate::dashboard.description_placeholder') }}">{{ old('description') ?? $user->description }}</textarea>
                                </div>
                                <!-- Email -->
                                <div class="form-group mb-3">
                                    <label for="email">{{ trans('plugins/real-estate::dashboard.email') }}</label>
                                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') ?? $user->email }}" placeholder="{{ trans('plugins/real-estate::dashboard.email_placeholder') }}" required>
                                    @if (setting('verify_account_email', false))
                                    @if ($user->confirmed_at)
                                    <small class="f7 green">{{ trans('plugins/real-estate::dashboard.verified') }}<i class="ml1 far fa-check-circle"></i></small>
                                    @else
                                    <small class="f7">{{ trans('plugins/real-estate::dashboard.verify_require_desc') }}<a class="ml1" href="{{ route('public.account.resend_confirmation', ['email' => $user->email]) }}">{{ trans('plugins/real-estate::dashboard.resend') }}</a></small>
                                    @endif
                                    @endif
                                </div>
                                <!-- Birthday -->
                                <div class="form-group mb-3">
                                    <label for="dob">{{ trans('plugins/real-estate::dashboard.birthday') }}</label>
                                    <div class="birthday-box">
                                        <select class="form-control{{ $errors->has('year') ? ' is-invalid' : '' }}" id="year" name="year" style="width: 74px!important; display: inline-block!important;" onchange="changeYear(this)"></select>
                                        <select class="form-control{{ $errors->has('month') ? ' is-invalid' : '' }}" id="month" name="month" style="width: 90px!important; display: inline-block!important;" onchange="changeMonth(this)"></select>
                                        <select class="form-control{{ $errors->has('day') ? ' is-invalid' : '' }}" id="day" name="day" style="width: 74px!important; display: inline-block!important;"></select>
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->has('dob') ? $errors->first('dob') : '' }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <!-- Gender -->
                                <div class="form-group mb-3">
                                    <label for="gender">{{ trans('plugins/real-estate::dashboard.gender') }}</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>{{ trans('plugins/real-estate::dashboard.gender_male') }}</option>
                                        <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>{{ trans('plugins/real-estate::dashboard.gender_female') }}</option>
                                        <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>{{ trans('plugins/real-estate::dashboard.gender_other') }}</option>
                                    </select>
                                </div>
                                @if (is_plugin_active('location'))
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="country_id">{{ trans('plugins/real-estate::dashboard.country') }}</label>
                                            {!! Form::customSelect('country_id', $countries, $user->country_id, ['data-type' => 'country']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="state_id">{{ trans('plugins/real-estate::dashboard.state') }}</label>
                                            {!! Form::customSelect('state_id', $states, $user->state_id, [
                                            'data-type' => 'state',
                                            'data-url' => route('ajax.states-by-country'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="city_id">{{ trans('plugins/real-estate::dashboard.city') }}</label>
                                            {!! Form::customSelect('city_id', $cities, $user->city_id, [
                                            'data-type' => 'city',
                                            'data-url' => route('ajax.cities-by-state'),
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                                @if($user->requires_legal_information)

                                <div class="form-group mb-3">
                                    <label for="val_license_number">{{ trans('plugins/real-estate::dashboard.val_license_number') }}</label>
                                    <input class="form-control" id="val_license_number" name="val_license_number" type="text" value="{{ old('val_license_number') ?? $user->broker->val_license_number }}" required>
                                </div>


                                <div class="form-group mb-3">
                                    <label for="commercial_registration">{{ trans('plugins/real-estate::dashboard.commercial_registration') }}</label>
                                    <input class="form-control" id="commercial_registration" name="commercial_registration" type="text" value="{{ old('val_license_number') ?? $user->broker->commercial_registration }}" required>
                                </div>


                                <div class="form-group mb-3">
                                    <label for="license_number">{{ trans('plugins/real-estate::dashboard.license_number') }}</label>
                                    <input class="form-control" id="license_number" name="license_number" type="text" value="{{ old('license_number') ?? $user->broker->license_number }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="commercial_registration_file">
                                        {{ trans('plugins/real-estate::dashboard.commercial_registration_file') }} (PDF)
                                    </label>

                                    @if ($user->broker->commercial_registration_file)
                                    <a href="{{ asset('storage/' . $user->broker->commercial_registration_file) }}" target="_blank">{{__('View')}}</a>
                                    @endif

                                    <input accept=".pdf" class="form-control" id="commercial_registration_file" name="commercial_registration_file" type="file" value="{{ old('commercial_registration_file') ?? $user->broker->commercial_registration_file }}" {{ $user->broker->commercial_registration_file ? '' : 'required' }}>
                                </div>



                                @endif
                                @endif
                                {!! apply_filters('account_settings_page', null, $user) !!}
                                <button class="btn btn-primary fw6" type="submit">{{ trans('plugins/real-estate::dashboard.save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('plugins/real-estate::account.modals.avatar')
</div>




@php
if($user->IsBrokerOrDeveloperAccount){
$val_license_number = $user->broker->val_license_number;
$commercial_registration = $user->broker->commercial_registration;
$license_number = $user->broker->license_number;
}else{
$val_license_number = null;
$commercial_registration = null;
$license_number = null;
}
@endphp
@if(!$user->isBrokerAccount)
<div class="modal fade" id="become-a-broker" tabindex="-1" aria-labelledby="become-a-brokerLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="avatar-modal-label"><i class="til_img"></i>
                    <strong> {{__('Become a Broker')}}</strong>
                </h4>
                <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <form method="post" action="{{route('public.account.convert.broker')}}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="val_license_number">{{ trans('plugins/real-estate::dashboard.val_license_number') }}</label>
                        <input class="form-control" id="val_license_number" name="val_license_number" type="text" value="{{ old('val_license_number') ?? $val_license_number }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="commercial_registration">{{ trans('plugins/real-estate::dashboard.commercial_registration') }}</label>
                        <input class="form-control" id="commercial_registration" name="commercial_registration" type="text" value="{{ old('commercial_registration') ?? $commercial_registration }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="license_number">{{ trans('plugins/real-estate::dashboard.license_number') }}</label>
                        <input class="form-control" id="license_number" name="license_number" type="text" value="{{ old('license_number') ?? $license_number }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@if(!$user->isDeveloperAccount)
<div class="modal fade" id="become-a-developer" tabindex="-1" aria-labelledby="become-a-developerLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="avatar-modal-label"><i class="til_img"></i>
                    <strong> {{__('Become a Developer')}} </strong>
                </h4>
                <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <form method="post" action="{{route('public.account.convert.developer')}}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="val_license_number">{{ trans('plugins/real-estate::dashboard.val_license_number') }}</label>
                        <input class="form-control" id="val_license_number" name="val_license_number" type="text" value="{{ old('val_license_number') ?? $val_license_number }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="commercial_registration">{{ trans('plugins/real-estate::dashboard.commercial_registration') }}</label>
                        <input class="form-control" id="commercial_registration" name="commercial_registration" type="text" value="{{ old('commercial_registration') ?? $commercial_registration }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="license_number">{{ trans('plugins/real-estate::dashboard.license_number') }}</label>
                        <input class="form-control" id="license_number" name="license_number" type="text" value="{{ old('license_number') ?? $license_number }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif




@endsection
@push('scripts')
<!-- Laravel Javascript Validation -->
<script type="text/javascript" src="{{ asset('vendor/core/core/js-validation/js/js-validation.js') }}"></script>
{!! JsValidator::formRequest(\Botble\RealEstate\Http\Requests\SettingRequest::class) !!}
<script type="text/javascript">
    'use strict';
    // index => month [0-11]
    let numberDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    $(document).ready(function() {
        // Init form select
        initSelectBox();
    });

    function initSelectBox() {
        let oldBirthday = '{{ $user->dob }}';
        let selectedDay = '';
        let selectedMonth = '';
        let selectedYear = '';

        if (oldBirthday !== '') {
            selectedDay = parseInt(oldBirthday.substr(8, 2));
            selectedMonth = parseInt(oldBirthday.substr(5, 2));
            selectedYear = parseInt(oldBirthday.substr(0, 4));
        }

        let dayOption = `<option value="">{{ trans('plugins/real-estate::dashboard.day_lc') }}</option>`;
        for (let i = 1; i <= numberDaysInMonth[0]; i++) { //add option days
            if (i === selectedDay) {
                dayOption += `<option value="${i}" selected>${i}</option>`;
            } else {
                dayOption += `<option value="${i}">${i}</option>`;
            }
        }
        $('#day').append(dayOption);

        let monthOption = `<option value="">{{ trans('plugins/real-estate::dashboard.month_lc') }}</option>`;
        for (let j = 1; j <= 12; j++) {
            if (j === selectedMonth) {
                monthOption += `<option value="${j}" selected>${j}</option>`;
            } else {
                monthOption += `<option value="${j}">${j}</option>`;
            }
        }
        $('#month').append(monthOption);

        let d = new Date();
        let yearOption = `<option value="">{{ trans('plugins/real-estate::dashboard.year_lc') }}</option>`;
        for (let k = d.getFullYear(); k >= 1918; k--) { // years start k
            if (k === selectedYear) {
                yearOption += `<option value="${k}" selected>${k}</option>`;
            } else {
                yearOption += `<option value="${k}">${k}</option>`;
            }
        }
        $('#year').append(yearOption);
    }

    function isLeapYear(year) {
        year = parseInt(year);
        if (year % 4 !== 0) {
            return false;
        }

        if (year % 400 === 0) {
            return true;
        }

        return year % 100 !== 0;
    }

    function changeYear(select) {
        if (isLeapYear($(select).val())) {
            // Update day in month of leap year.
            numberDaysInMonth[1] = 29;
        } else {
            numberDaysInMonth[1] = 28;
        }

        // Update day of leap year.
        let monthSelectedValue = parseInt($("#month").val());
        if (monthSelectedValue === 2) {
            let day = $('#day');
            let daySelectedValue = parseInt($(day).val());
            if (daySelectedValue > numberDaysInMonth[1]) {
                daySelectedValue = null;
            }

            $(day).empty();

            let option = `<option value="">{{ trans('plugins/real-estate::dashboard.day_lc') }}</option>`;
            for (let i = 1; i <= numberDaysInMonth[1]; i++) { //add option days
                if (i === daySelectedValue) {
                    option += `<option value="${i}" selected>${i}</option>`;
                } else {
                    option += `<option value="${i}">${i}</option>`;
                }
            }

            $(day).append(option);
        }
    }

    function changeMonth(select) {
        let day = $('#day');
        let daySelectedValue = parseInt($(day).val());
        let month = 0;

        if ($(select).val() !== '') {
            month = parseInt($(select).val()) - 1;
        }

        if (daySelectedValue > numberDaysInMonth[month]) {
            daySelectedValue = null;
        }

        $(day).empty();

        let option = `<option value="">{{ trans('plugins/real-estate::dashboard.day_lc') }}</option>`;

        for (let i = 1; i <= numberDaysInMonth[month]; i++) { //add option days
            if (i === daySelectedValue) {
                option += `<option value="${i}" selected>${i}</option>`;
            } else {
                option += `<option value="${i}">${i}</option>`;
            }
        }

        $(day).append(option);
    }
</script>
@endpush