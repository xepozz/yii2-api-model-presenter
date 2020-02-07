<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User;

use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;

class EmptyPresenter extends ProxyPresenter
{
    protected function getFields()
    {
        return [
        ];
    }
}