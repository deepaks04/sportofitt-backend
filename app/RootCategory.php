<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RootCategory extends Model
{
    /**
     * @var string
     */
    protected $table = 'root_categories';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategory(){
        return $this->hasMany('App\SubCategory','root_category_id');
    }
}
