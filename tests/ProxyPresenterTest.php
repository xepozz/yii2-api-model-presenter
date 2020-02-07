<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests;

use PHPUnit\Framework\TestCase;
use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Model\User;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User\CallableValuePresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User\EmptyPresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User\KeyValuePresenter;
use Xepozz\Yii2ApiModelPresenter\Tests\Stub\Presenter\User\SimpleValuePresenter;

class ProxyPresenterTest extends TestCase
{
    /**
     * @dataProvider getSimplePresenters
     * @param ProxyPresenter $presenter
     * @param array $expectedFields
     */
    public function testEmptyFields()
    {
        $presenter = new EmptyPresenter(new User());
        $this->assertEquals([], $presenter->toArray());
    }

    /**
     * @dataProvider getSimplePresenters
     * @param ProxyPresenter $presenter
     * @param array $expectedFields
     */
    public function testFields($presenter, $expectedFields)
    {
        $this->assertEquals($expectedFields, $presenter->toArray());
    }

    public function getSimplePresenters()
    {
        $data = [
            'id' => $id = 1,
            'name' => $name = 'Dmitriy',
            'roles' => $roles = ['guest', 15, true],
            'is_deleted' => $isDeleted = false,
            'is_online' => $isOnline = true,
        ];
        $model = new User($data);

        return [
            [
                new SimpleValuePresenter($model),
                $data,
            ],
            [
                new KeyValuePresenter($model),
                $data,
            ],
            [
                new CallableValuePresenter($model),
                $data,
            ],
        ];
    }
}
