<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User;

use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Model\User;

class SimpleValuePresenter extends ProxyPresenter
{
    protected function getFields()
    {
        return [
            // int
            'id',
            // string
            'name',
            // array
            'roles',
            // false
            'is_deleted',
            // true
            'is_online',
        ];
    }

    public static function getModelClass()
    {
        return User::className();
    }
}