<?php

namespace Xepozz\Yii2ApiModelPresenter\Tests\Stub\Model;

use yii\db\ActiveRecord;

class Post extends ActiveRecord
{
    public $id;
    public $title;
    public $user_id;
    public $created_at;
}