<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User;

use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Model\User;

class KeyValuePresenter extends ProxyPresenter
{
    protected function getFields()
    {
        return [
            // int
            'id' => 'id',
            // string
            'name' => 'name',
            // array
            'roles' => 'roles',
            // false
            'is_deleted' => 'is_deleted',
            // true
            'is_online' => 'is_online',
        ];
    }

    public static function getModelClass()
    {
        return User::className();
    }
}