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
    public function testFieldsConfigurations($presenter, $expectedFields)
    {
        $this->assertEquals($expectedFields, $presenter->toArray());
    }

    /**
     * @dataProvider getPresentersWithSpecificFields
     * @param ProxyPresenter $presenter
     * @param callable $expectedFieldsCallback
     */
    public function testSpecificFields($presenter, $expectedFieldsCallback)
    {
        $fields = ['id'];
        $expand = [];
        $expectedFields = $expectedFieldsCallback($fields, $expand);
        $this->assertEquals($expectedFields, $presenter->toArray($fields));
    }

    public function getSimplePresenters()
    {
        $data = [
            'id' => $id = 1,
            'name' => $name = 'Dmitrii',
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

    public function getPresentersWithSpecificFields()
    {
        $data = [
            'id' => $id = 1,
            'name' => $name = 'Dmitrii',
            'roles' => $roles = ['guest', 15, true],
            'is_deleted' => $isDeleted = false,
            'is_online' => $isOnline = true,
        ];
        $model = new User($data);

        $callback = static function ($fields) use ($data) {
            return array_intersect_key($data, array_flip($fields));
        };

        return [
            [
                new SimpleValuePresenter($model),
                $callback,
            ],
            [
                new KeyValuePresenter($model),
                $callback,
            ],
            [
                new CallableValuePresenter($model),
                $callback,
            ],
        ];
    }
}
