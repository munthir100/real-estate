<?php

namespace Botble\RealEstate\Models;

use Exception;
use Illuminate\Support\Str;
use Botble\Base\Supports\Avatar;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Laravel\Sanctum\HasApiTokens;
use Botble\Base\Casts\SafeContent;
use Botble\Media\Models\MediaFile;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use App\Services\ProfileCompletenessChecker;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Botble\RealEstate\Notifications\ConfirmEmailNotification;
use Botble\RealEstate\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Account extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use MustVerifyEmail;
    use HasApiTokens;
    use Notifiable;

    protected $table = 're_accounts';

    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'avatar_id',
        'dob',
        'phone',
        'description',
        'gender',
        'company',
        'country_id',
        'state_id',
        'city_id',
        'account_type_id',
        'verified_at',
        'is_featured',
        'is_public_profile',
    ];



    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'datetime',
        'verified_at' => 'datetime',
        'package_start_date' => 'datetime',
        'package_end_date' => 'datetime',
        'is_featured' => 'boolean',
        'is_public_profile' => 'boolean',
        'full_name' => SafeContent::class,
        'username' => SafeContent::class,
        'phone' => SafeContent::class,
        'description' => SafeContent::class,
        'company' => SafeContent::class,
    ];

    public function activityLogs(): HasMany
    {
        return $this->hasMany(AccountActivityLog::class, 'account_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (Account $account) {
            $account->activityLogs()->delete();
            $account->transactions()->delete();
            $account->reviews()->delete();
            $account->packages()->detach();
        });

        static::deleting(function (Account $account) {
            $folder = Storage::path($account->upload_folder);
            if (File::isDirectory($folder) && Str::endsWith($account->upload_folder, '/' . $account->username)) {
                File::deleteDirectory($folder);
            }

            $account->reviews()->delete();
        });
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new ConfirmEmailNotification());
    }

    public function avatar(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class)->withDefault();
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_name
        );
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->avatar->url) {
                    return RvMedia::url($this->avatar->url);
                }

                try {
                    return (new Avatar())->create($this->name)->toBase64();
                } catch (Exception) {
                    return RvMedia::getDefaultImage();
                }
            },
        );
    }

    /**
     * @deprecated since v2.22
     */
    protected function fullName()
    {
        $this->full_name;
    }

    protected function credits(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!RealEstateHelper::isEnabledCreditsSystem()) {
                    return 0;
                }

                return $value ?: 0;
            }
        );
    }

    public function properties(): MorphMany
    {
        return $this->morphMany(Property::class, 'author');
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'author');
    }

    public function canPost(): bool
    {
        return !RealEstateHelper::isEnabledCreditsSystem() || $this->credits > 0; // important
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 're_account_packages', 'account_id', 'package_id');
    }

    protected function uploadFolder(): Attribute
    {
        return Attribute::make(
            get: function () {
                $folder = $this->username ? 'accounts/' . $this->username : 'accounts';

                return apply_filters('real_estate_account_upload_folder', $folder, $this);
            }
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function canReview(Project|Property $model): bool
    {
        if (!auth('account')->check()) {
            return false;
        }

        return !$model
            ->reviews()
            ->whereNot('status', ReviewStatusEnum::REJECTED)
            ->where('account_id', auth('account')->id())
            ->exists();
    }

    protected function url(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->username || RealEstateHelper::isDisabledPublicProfile()) {
                return null;
            }

            return route('public.agent', $this->username);
        });
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    function broker()
    {
        return $this->hasOne(Broker::class);
    }

    function seeker()
    {
        return $this->hasOne(Seeker::class);
    }

    function getIsBrokerAccountAttribute()
    {
        return $this->account_type_id == AccountType::BROKER;
    }
    function getIsDeveloperAccountAttribute()
    {
        return $this->account_type_id == AccountType::DEVELOPER;
    }
    function getIsSeekerAccountAttribute()
    {
        return $this->account_type_id == AccountType::SEEKER;
    }
    function getRequiresLegalInformationAttribute()
    {
        return $this->getIsBrokerAccountAttribute() || $this->getIsDeveloperAccountAttribute();
    }
    function getIsBrokerOrDeveloperAccountAttribute()
    {
        return $this->getIsBrokerAccountAttribute() || $this->getIsDeveloperAccountAttribute();
    }
    function getIsCompletedProfileAttribute()
    {
        return ProfileCompletenessChecker::check($this);
    }
}
