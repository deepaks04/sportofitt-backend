<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{

    /**
     *
     * @var string
     */
    protected $table = 'sub_categories';
    
    protected $hidden = ['created_at','updated_at'];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rootCategory()
    {
        return $this->belongsTo('App\RootCategory', 'id');
    }
}
