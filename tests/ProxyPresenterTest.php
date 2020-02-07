<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests;

use PHPUnit\Framework\TestCase;
use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Model\User;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User\EmptyPresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User\KeyValuePresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User\ValuePresenter;

class ProxyPresenterTest extends TestCase
{
    /**
     * @dataProvider getSimplePresenters
     * @param ProxyPresenter $presenter
     * @param array $expectedFields
     */
    public function testEmptyFields($presenter, $expectedFields)
    {
        $this->assertEquals($expectedFields, $presenter->toArray());
    }

    public function getSimplePresenters()
    {
        $model = new User(
            [
                'id' => $id = 1,
                'name' => $name = 'Dmitriy',
                'roles' => $roles = ['guest', 15, true],
                'is_deleted' => $isDeleted = false,
                'is_online' => $isOnline = true,
            ]
        );

        return [
            [new EmptyPresenter($model), []],
            [
                new ValuePresenter($model),
                [
                    'id' => $id,
                    'name' => $name,
                    'roles' => $roles,
                    'is_deleted' => $isDeleted,
                    'is_online' => $isOnline,
                ],
            ],
            [
                new KeyValuePresenter($model),
                [
                    'id' => $id,
                    'name' => $name,
                    'roles' => $roles,
                    'is_deleted' => $isDeleted,
                    'is_online' => $isOnline,
                ],
            ],
        ];
    }
}
