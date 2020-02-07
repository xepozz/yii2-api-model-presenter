<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User;

use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;

class SimpleValuePresenter extends ProxyPresenter
{
    protected function getFields(): array
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
}