<?php

namespace Xepozz\Yii2ApiModelPresenter;

use InvalidArgumentException;
use yii\base\Arrayable;

/**
 * Используется для конечного вывода в {@see \yii\rest\Serializer}.
 * Если возвращать массив из объектов с типом {@see \yii\base\Arrayable},
 * то {@see \yii\rest\Serializer} не конвертирует их: не прокидывает "fields", "expand" и т.п.
 * Данный класс исправляет эту проблему.
 */
class ProxyCollection implements Arrayable
{
    /**
     * @var \yii\base\Arrayable[]
     */
    private $presenters;

    /**
     * ProxyPresenterCollection constructor.
     *
     * @param \yii\base\Arrayable[] $presenters
     */
    public function __construct(array $presenters)
    {
        $this->ensurePresentersAreArrayble($presenters);
        $this->presenters = $presenters;
    }

    public function fields()
    {
    }

    public function extraFields()
    {
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return array_map(
            static function (Arrayable $presenter) use ($fields, $expand, $recursive) {
                return $presenter->toArray($fields, $expand, $recursive);
            },
            $this->presenters
        );
    }

    private function ensurePresentersAreArrayble(array $presenters)
    {
        foreach ($presenters as $presenter) {
            if (!$presenter instanceof Arrayable) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Ожидался %s, получен %s',
                        'yii\base\Arrayable',
                        get_class($presenter)
                    )
                );
            }
        }
    }
}