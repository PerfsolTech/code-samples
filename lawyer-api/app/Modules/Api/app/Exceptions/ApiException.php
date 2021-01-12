<?php

namespace Api\Exceptions;

class ApiException extends \Exception
{

    const USER_WITH_THIS_EMAIL_ALREADY_EXISTS = 1;
    const USER_IS_NOT_CONFIRMED = 2;
    const USER_WITH_THIS_EMAIL_NOT_FOUND = 3;
    const INVALID_PASSWORD = 4;
    const REVIEW_ALREADY_SUBMITTED = 5;
    const INVALID_ATTACHMENT = 6;
    const CASE_IS_CLOSED_OPERATION_NOT_ALLOWED = 7;
    const CASE_ALREADY_HAS_REQUESTED_OR_ACCEPTED_LAWYER = 8;
    const CASE_CAN_HAS_ONLY_6_SUGGESTED_LAWYERS = 9;
    const CASE_ALREADY_HAS_ACCEPTED_LAWYER = 10;
    const CASE_IS_NOT_CLOSED_OPERATION_NOT_ALLOWED = 11;
    const CASE_HAS_AlREADY_ASSIGNED_THIS_LAWYER = 12;
    const USER_ALREADY_ASSIGNED_LAWYER_TO_ANOTHER_CASE = 13;
    const OPERATION_NOT_ALLOWED_FOR_ACCEPTED_REQUEST = 14;
    const INVALID_LAWYER_ID = 15;
    const INVALID_CASE_ID = 16;
    const LAWYER_DOES_NOT_SUGGESTED_TO_CASE = 17;
    const FACEBOOK_SIGN_IN__USER_DENIED_ACCESS_TO_EMAIL = 18;
    const USERS_WHO_CHATTED_OR_OPENED_CASE_WITH_LAWYER_CAN_RATE_THE_LAWYER = 19;

    private $http_code;
    private $error_code;

    /**
     * ApiException constructor.
     * @param $http_code
     * @param $error_code
     */
    public function __construct($http_code, $error_code)
    {
        $this->http_code = $http_code;
        $this->error_code = str_pad($error_code, 3, 0, STR_PAD_LEFT);;
    }


    /**
     * @return string
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->error_code;
    }
}