<?php
namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * App\User
 *
 * @property integer $id
 * @property string $fname
 * @property string $lname
 * @property string $profile_picture
 * @property string $email
 * @property string $username
 * @property string $password
 * @property boolean $is_active
 * @property integer $status_id
 * @property integer $role_id
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereProfilePicture($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereStatusId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     * While $fillable serves as a "white list" of attributes that should be mass assignable,
     *
     * @var array
     */
    // protected $fillable = ['fname', 'lname', 'email', 'username', 'password', 'is_active', 'status_id', 'role_id', 'remember_token'];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The $guarded property should contain an array of
     * attributes that you do not want to be mass assignable.
     * All other attributes not in the array will be mass assignable.
     * So, $guarded functions like a "black list".
     * Of course, you should use either $fillable or $guarded - not both:
     *
     * @var array
     */
    protected $guarded = [
        'email',
        'is_active',
        'status_id',
        'role_id',
        'remember_token'
    ];

    /**
     * One User Can have One Vendor Only
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendor()
    {
        return $this->hasOne('App\Vendor');
    }

    public function customer()
    {
        return $this->hasOne('App\Customer');
    }
}
