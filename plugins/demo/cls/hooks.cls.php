<?php

namespace PHPEMS\plugins\demo;
use function \PHPEMS\M;
use function \PHPEMS\P;
class hooks
{
    /**
     * @return void
     * 此方法必须存在
     */
    public function register()
    {
        M('plugin')->registerHook('userRegister',array($this,'userRegister'),10);
    }

    public function userRegister($userid)
    {
        $user = M('user','user')->modifyUserInfo($userid,['usercoin' => 10]);
    }

    public function userLogin($userid)
    {
        return $userid++;
    }
}