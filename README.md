## Yii2 Api Model Presenter

### Описание

При работе с API приходится отдавать свойства объектов в виде примиивой структуры JSON или XML.

В Yii2 заложен механизм отображения моделей, как JSON или XML.

Выглядит это так:

```php
class User extends \yii\db\ActiveRecord
{
    public $firstName;
    public $lastName;

    public function fields()
    {
        return [
            'id' => 'id',
            'name' => function(){             
                return $this->firstName . ' ' . $this->lastName;
            },
        ];
    }
}
```