<?php

namespace Xepozz\Yii2ApiModelPresenter;

use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\db\ActiveRecord;

/**
 * Презентер для объектов {{@see \yii\db\ActiveRecord}}.
 * Вместо того, чтобы наследовать модели и/или описывать в них отображаемые свойства и связи,
 * можно унаследовать данный класс и описать, какие свойства и связи будут отображены при сериализации модели.
 * TODO написать тест на всевозможные преобразования
 */
abstract class ProxyPresenter implements Arrayable
{
    use ArrayableTrait;

    /**
     * @var \yii\db\ActiveRecord
     */
    protected $record;

    public function __construct(ActiveRecord $record)
    {
        $this->record = $record;
    }

    /**
     * Позволяет быстро создать массив из прокси-объектов
     *
     * @param \yii\db\ActiveRecord[] $records
     * @return static[]
     */
    public static function createMultiple(array $records)
    {
        return array_map(
            static function ($record) {
                return new static($record);
            },
            $records
        );
    }

    /**
     * Создает коллекцию представлений.
     *
     * @param \yii\base\Arrayable[] $records
     * @return \Xepozz\Yii2ApiModelPresenter\ProxyCollection
     */
    public static function createCollection(array $records)
    {
        return new ProxyCollection($records);
    }

    /**
     * Задает свойства и связи, которые будут отображены в ответе.
     *
     * @return mixed
     */
    abstract protected function getFields();

    /**
     * Данный метод используется для проксирования.
     * Для определений отображения использовать {{@see getFields()}}
     *
     * @return array
     * @see getFields()
     */
    final public function fields()
    {
        $fields = $this->getFields();

        return $this->proxyFields($fields, $this->record);
    }

    /**
     * @return array
     */
    protected function getExtraFields()
    {
        return [];
    }

    /**
     * Если нужно скрывать из выдачи некоторые поля, то этот метод поможет в этом.
     * Ключом должно являться название возвращаемого поля, а значением либо bool, либо callable, который возвращает bool
     *
     * @return array
     * @example
     * ```php
     *     return [
     *         'payment_link' => PaymentService::isAnyGatewayActive(),
     *         'total_sum' => function () {
     *             return $this->invoice->isNeedToPay();
     *         }
     *     ];
     * ```
     */
    protected function getIgnoredFields()
    {
        return [];
    }

    /**
     * Данный метод используется для проксирования.
     * Для определений отображения использовать {{@see getExtraFields()}}
     *
     * @return array
     * @see getExtraFields()
     */
    final public function extraFields()
    {
        $fields = $this->getExtraFields();

        return $this->proxyFields($fields, $this->record);
    }

    /**
     * Проксирует связи моделей.
     * Если есть предустановки в методе {{@see setUpChildDefinitions()}}, то создает объекты указанных типов данных
     * Если ключ числовой, тогда дублирует значение. Это нужно для легкого указания списка выводимых свойств
     *
     * @param $fields
     * @param \yii\db\ActiveRecord $proxyRecord
     * @return array
     */
    private function proxyFields($fields, ActiveRecord $proxyRecord)
    {
        $presentedFields = [];
        $childDefinitions = $this->setUpChildDefinitions();
        $ignoredFields = $this->getIgnoredFields();

        foreach ($fields as $key => $field) {
            if (is_int($key)) {
                $key = $field;
            }

            if (is_string($field)) {
                if (array_key_exists($field, $ignoredFields)) {
                    $flag = $ignoredFields[$field];
                    if (
                        (is_bool($flag) && $flag === true) ||
                        (is_callable($flag) && (bool)$flag() === true)
                    ) {
                        continue;
                    }
                }

                $field = $this->resolveField($proxyRecord, $field, $childDefinitions);
            }

            $presentedFields[$key] = $field;
        }

        return $presentedFields;
    }

    /**
     * Можно задать определение для связей.
     * Вместо того, чтобы писать
     * ```php
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
     * Важно: указанный класс-референс должен быть наследован от {{@see \Xepozz\Yii2ApiModelPresenter\ProxyPresenter}}
     *
     * @return array
     */
    protected function setUpChildDefinitions()
    {
        return [];
    }

    /**
     * 1. Превращает поле из "user" в $proxyRecord->user.
     * 2. Оборачивает связи из {{@see setUpChildDefinitions()}} в ProxyRecord:
     * Если $user->articles это Article[], то при соответствующей строке в {{@see setUpChildDefinitions()}}
     * $user->articles будет ArticleProxyRecord[].
     *
     * @param \yii\db\ActiveRecord $proxyRecord
     * @param string $field
     * @param array $childDefinitions
     * @return \Closure|string
     */
    private function resolveField(ActiveRecord $proxyRecord, $field, array $childDefinitions)
    {
        return static function () use ($proxyRecord, $field, $childDefinitions) {
            $populatedRelation = $proxyRecord->$field;

            if (!array_key_exists($field, $childDefinitions) || $populatedRelation === null) {
                return $populatedRelation;
            }

            $relation = $proxyRecord->getRelation($field, false);

            if ($relation !== null) {
                $referenceClass = $childDefinitions[$field];

                if ($relation->multiple) {
                    /**
                     * @var $referenceClass self
                     */
                    return $referenceClass::createMultiple($populatedRelation);
                }

                return new $referenceClass($populatedRelation);
            }

            return $populatedRelation;
        };
    }
}