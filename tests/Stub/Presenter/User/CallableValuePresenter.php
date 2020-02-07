<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User;

use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;

/**
 * @property \Xepozz\Yii2ApiModelPresenter\Tests\Stub\Model\User $record
 */
class CallableValuePresenter extends ProxyPresenter
{
    protected function getFields()
    {
        return [
            // int
            'id' => function () {
                return $this->record->id;
            },
            // string
            'name' => function () {
                return $this->record->name;
            },
            // array
            'roles' => function () {
                return $this->record->roles;
            },
            // false
            'is_deleted' => function () {
                return $this->record->is_deleted;
            },
            // true
            'is_online' => function () {
                return $this->record->is_online;
            },
        ];
    }
}