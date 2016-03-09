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
        return $this->belongsTo('App\RootCategory');
    }
    
    /**
     * Get subcategory by id 
     * 
     * @param integer $id
     * @return mixed App\SubCategory | boolean
     */
    public static function getSubCategoryById($id) 
    {
        $subCategory = self::find($id);
        if(!empty($subCategory) && $subCategory->id > 0) {
            return $subCategory;
        }
        
        return false;
    }
}
