<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Validator;

abstract class Request extends FormRequest {

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @param  array  $rules
     * @return array
     */
    public function validate($data = array(), $rules = array()) {
        $errorMessages = array();
        $postData = ($data)?$data:$this->all();
        $validationRules = ($rules)?$rules:  $this->rules;
        $validator = Validator::make($postData, $validationRules);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag()->getMessages();
            $messagesKey = array_keys($messages);
            $ruleKeys = array_keys($this->rules);
            foreach ($ruleKeys as $ruleKeyName) {
                if (in_array($ruleKeyName, $messagesKey)) {
                    $errorMessages[$ruleKeyName] = $messages[$ruleKeyName][0];
                }
            }
        }
        
        return $errorMessages;
    }
    

}