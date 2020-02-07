<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User;

use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;

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
}