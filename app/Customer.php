<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Customer
 *
 * @property integer $id
 * @property string $birthdate
 * @property string $gender
 * @property integer $pincode
 * @property string $phone_no
 * @property integer $area_id
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Customer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Customer whereBirthdate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Customer whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Customer wherePincode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Customer wherePhoneNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Customer whereAreaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Customer whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Customer whereUpdatedAt($value)
 */
class Customer extends Model
{
    protected $table = 'customers';
}
