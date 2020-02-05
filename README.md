# Yii2 Api Model Presenter

## Установка

```
composer req xepozz/yii2-api-model-presenter
```

## Описание

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
            'name' => function() {             
                return $this->firstName . ' ' . $this->lastName;
            },
        ];
    }
}
```

## Проблемы

1. Первая проблема навязанного подхода в том, что модель засоряется знаниями о том, в какую структуру она будет преобразована при сериализации.
2. Вторая проблема появляется тогда, когда есть наследование моделей. \
    Пример: \
    Существует `common\models\User`, `api\modules\chat\models\User`, `api\modules\forum\models\User` (крайне не советую разводить такой зоопарк)
    Все хорошо, если отдавать непосредственно ту модель, которую хотите отобразить. 
    Но если какой-то модуль отдает `common\models\User`, вместо `api\modules\chat\models\User`, 
    то приходится идти на хитрости, чтобы отдать нужную структуру в апи. Хитрость обычно такая:
    ```php
       /** @var $model \common\models\User */
       $model = $someService->getMyUser();
       
       return \api\modules\chat\models\User::findOne($model->id);
    ```
   Или наоборот, когда нужно вывести структуру описанную в `common\models\User`, вместо той, что заложена в 
   `api\modules\chat\models\User`.
   ```php
      /** @var $model \api\modules\chat\models\User */
      $model = $someService->getMyUser();
      
      return \common\models\User::findOne($model->id);
   ```
   
   Выглядит, как костыль на костыле, если честно. 
3. Третья проблема вытекает из предыдущей. \
    Если используются модели из пунка №2, то плохой программист захочет выводить `api\modules\chat\models\Message` 
    (которая унаследована от `common\models\Message`), при выводе самой модели `api\modules\chat\models\User` как связь. \
    Но в `common\models\User` связь прописана на `common\models\Message`, как быть?
    Одним пальцем руки происходит **override** модели в связи на ту, что лежит в `api/...`. 

## Решение

Способ борьбы с таким предлагаю такой:

Есть класс, в котором описывается всё поведение, которое будет отдаваться в **API**. \
В данный класс можно загружать любой зоопарк моделей. Структура на выходе будет всегда одинаковая.

## Пример

##### Пример простого использования проксирования объектов.
```php
use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;

/**
 * @property \common\models\User $record
 */
class UserPresenter extends ProxyPresenter
{
    protected function getFields(): array
    {
        return [
            'first_name',
            'last_name',
            'full_name' => function() {
                return sprintf('%s %s', $this->record->first_name, $this->record->last_name);        
            }
        ];
    }
}
```

##### Пример более продвинутого использования.
Используем презентер связи для ее вывода.
```php
use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;

/**
 * @property \common\models\User $record
 */
class UserPresenter extends ProxyPresenter
{
    protected function getFields(): array
    {
        return [
            // ...
        ];
    }

    public function getExtraFields(): array
    {
        return [
            'messages' => 'chatMessages',
        ];
    }

    protected function setUpChildDefinitions(): array
    {
        return [
            'chatMessages' => ChatMessagePresenter::class,
        ];
    }
}
```

##### Пример с вариантивным скрытие поля

```php
use Xepozz\Yii2ApiModelPresenter\ProxyPresenter;

/**
 * @property \common\models\User $record
 */
class UserPresenter extends ProxyPresenter
{
    protected function getFields(): array
    {
        return [
            'id',
            'status',
            'last_visited_at',
        ];
    }

    protected function getIgnoredFields(): array
    {
        return [
            /**
             * Скрываем на "prod" откружении поле email. Используется только для разработки
             */
            'id' => YII_ENV_PROD,
            'last_visited_at' => function() {
                /**
                 * Если статус online, тогда не показываем это поле.
                 */
                return (bool) $this->record->status;
            },
        ];
    }
}
```

## Развитие

Буду рад всевозможным исправлениям, замечаниям и всевозможным дискуссиям по дальнейшему развитию библиотеки. \
На текущий момент есть план со следующими задачами:

- Нужно написать тесты
- Написать более внятную документацию
- Перевести документацию на английский язык