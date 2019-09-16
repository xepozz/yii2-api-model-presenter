<?php

namespace xepozz\ProxyPresenter;

use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\db\ActiveRecord;

/**
 * Презентер для объектов [ActiveRecord].
 * Вместо того, чтобы наследовать модели из [common\models] и описывать в них отображаемые свойства и связи,
 * нужно пользоваться композицией: унаследовать данный класс и описать, какие свойства и связи будут отображены.
 *
 * TODO написать тест на всевозможные преобразования
 */
abstract class AbstractPresenter implements Arrayable
{
    use ArrayableTrait;

    protected $record;

    public function __construct(ActiveRecord $record)
    {
        $this->record = $record;
    }

    /**
     * Позволяет быстро создать массив из прокси-объектов
     * @param array $records
     * @return static[]
     */
    public static function createMultiple(array $records): array
    {
        return array_map(static function ($record) {
            return new static($record);
        }, $records);
    }

    /**
     * Задает свойства и связи, которые будут отображены в ответе.
     *
     * @return mixed
     */
    abstract protected function getFields(): array;

    /**
     * Данный метод используется для проксирования.
     * Для определений отображения использовать [getFields]
     *
     * @return array
     * @see getFields()
     */
    final public function fields(): array
    {
        $fields = $this->getFields();

        return $this->proxyFields($fields, $this->record);
    }

    protected function getExtraFields(): array
    {
        return [];
    }

    /**
     * Данный метод используется для проксирования.
     * Для определений отображения использовать [getExtraFields]
     *
     * @return array
     * @see getExtraFields()
     */
    final public function extraFields(): array
    {
        $fields = $this->getExtraFields();

        return $this->proxyFields($fields, $this->record);
    }

    /**
     * Проксирует связи моделей.
     * Если есть предустановки в методе [setChildDefinitions], то создает объекты указанных типов данных
     * Если ключ числовой, тогда дублирует значение. Это нужно для легкого указания списка выводимых свойств
     *
     * @param $fields
     * @param \yii\db\ActiveRecord $proxyRecord
     * @return array
     */
    private function proxyFields($fields, ActiveRecord $proxyRecord): array
    {
        $presentedFields = [];

        foreach ($fields as $key => $field) {
            if (is_integer($key)) {
                $key = $field;
            }
            if (is_string($field)) {
                $childDefinitions = $this->setUpChildDefinitions();
                $field = static function () use ($proxyRecord, $field, $childDefinitions) {
                    $populatedRelation = $proxyRecord->$field;
                    if (array_key_exists($field, $childDefinitions)) {
                        $relation = $proxyRecord->getRelation($field, false);

                        if ($relation !== null) {
                            $referenceClass = $childDefinitions[$field];
                            if ($relation->multiple) {
                                /**
                                 * @var $referenceClass \api\components\proxyPresenter\ProxyPresenter
                                 */
                                return $referenceClass::createMultiple($populatedRelation);
                            }

                            return new $referenceClass($proxyRecord);
                        }
                    }

                    return $populatedRelation;
                };
            }
            $presentedFields[$key] = $field;
        }

        return $presentedFields;
    }

    /**
     * Можно задать определение для связей.
     * Вместо того, чтобы писать
     * ```
     * public function getFields()
     * {
     *      return [
     *          'id',
     *          'childItems' => function() {
     *               return ChildItem::createMultiple($this->record->childItems);
     *          },
     *      ]
     * }
     * ```
     * можно задать определение в этом методе
     * ```
     * protected function setUpChildDefinitions()
     * {
     *      return [
     *          'childItems' => ChildItem::class,
     *      ]
     * }
     * ```
     * и выводить связь коротким тегом
     * ```
     * public function getFields()
     * {
     *      return [
     *          'id',
     *          'childItems',
     *      ]
     * }
     * ```
     * Не нужно указывать тип связи, она разрулится автоматически.
     * Важно: указанный класс-референс должен быть наследован от [ProxyPresenter]
     *
     * @return array
     */
    protected function setUpChildDefinitions(): array
    {
        return [];
    }
}