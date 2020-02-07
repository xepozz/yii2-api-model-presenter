<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests\Stub\Model;

use yii\db\ActiveRecord;

class User extends ActiveRecord
{
    public $id;
    public $name;
    public $surname;
    public $is_deleted;
    public $is_online;
    public $created_at;
    public $roles;
}