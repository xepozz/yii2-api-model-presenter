<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User;

use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Model\User;

class EmptyPresenter extends ProxyPresenter
{
    protected function getFields()
    {
        return [
        ];
    }

    public static function getModelClass()
    {
        return User::className();
    }
}