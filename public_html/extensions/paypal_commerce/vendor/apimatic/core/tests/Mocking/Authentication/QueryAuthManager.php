<?php

namespace Core\Tests\Mocking\Authentication;

use Core\Authentication\CoreAuth;
use Core\Request\Parameters\QueryParam;

class QueryAuthManager extends CoreAuth
{
    public function __construct($token, $accessToken)
    {
        parent::__construct(
            QueryParam::init('token', $token)->requiredNonEmpty(),
            QueryParam::init('authorization', $accessToken)->requiredNonEmpty()
        );
    }
}
