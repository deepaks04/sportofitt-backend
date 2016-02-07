<?php

namespace App\Http\Helpers;

use Validator;

class APIValidation extends Validator {

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @param  array  $rules
     * @return array
     */
    public static function validator(array $data, $rules) {
        $errorMessages = array();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag()->getMessages();
            $messagesKey = array_keys($messages);
            $ruleKeys = array_keys($rules);
            foreach ($ruleKeys as $ruleKeyName) {
                if (in_array($ruleKeyName, $messagesKey)) {
                    $errorMessages[$ruleKeyName] = $messages[$ruleKeyName][0];
                }
            }
        }

        return $errorMessages;
    }

}