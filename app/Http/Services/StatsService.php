<?php namespace App\Http\Services;

use App\BodyStats;
use App\Http\Services\BaseService;

class StatsService extends BaseService {

    /**
     *
     * @var mixed (NULL| App\BodyStats)
     */
    private $model = null;

    public function __construct()
    {
        try {
            parent::__construct();
            $this->model = new BodyStats();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getStatusCode(), $exception);
        }
    }
    
    /**
     * 
     * @return App\BodyStats
     */
    public function getUserBodyStats()
    {
        return $this->model->getUserBodyStats($this->user->id);
    }

    /**
     * Save users body stats information
     * 
     * @param array $postData
     * @return boolean
     */
    public function saveUserBodyStats(array $postData)
    {
        $userBodyStat = $this->getUserBodyStats();
        if(isset($userBodyStat) && 0 > $userBodyStat->id) {
            $this->model = $userBodyStat;
        }
        
        $this->model->user_id = $this->user->id;
        $this->model->weight = $postData['weight'];
        $this->model->height = $postData['height'];
        $this->model->waist = $postData['waist'];
        $this->model->chest = $postData['chest'];
        $this->model->forarm = $postData['forarm'];
        $this->model->wrist = $postData['wrist'];
        $this->model->hip = $postData['hip'];
        $this->model->activity_level = $postData['activity_level'];
        $this->model->bmi = $postData['bmi'];
        $this->model->body_fat = $postData['body_fat'];
        $this->model->bmr = $postData['bmr'];
        $this->model->tdee = $postData['tdee'];

        return $this->model->save();
    }

}
