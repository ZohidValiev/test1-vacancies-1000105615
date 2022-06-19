<?php
namespace app\models;

use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


/**
 * @property int $id
 * @property string $user_name
 * @property string $user_phone
 * @property int $warehouse_id
 * @property DateTimeImmutable $created_at
 * @property DateTimeImmutable $updated_at
 * @property int $status
 * @property int $items_count
 */
class Order extends ActiveRecord
{   
    public static function tableName()
    {
        return '{{order}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new DateTimeImmutable(),
            ],
            'typecastSave' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'created_at' => function ($value) {
                        return $value instanceof DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value;
                    },
                    'updated_at' => function ($value) {
                        return $value instanceof DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value;
                    },
                ],
                'typecastAfterValidate' => false,
                'typecastBeforeSave' => true,
            ],
            'typecastFind' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'created_at' => function ($value) {
                        return new DateTimeImmutable($value);
                    },
                    'updated_at' => function ($value) {
                        return new DateTimeImmutable($value);
                    },
                ],
                'typecastAfterValidate' => false,
                'typecastAfterFind' => true,
            ],
        ];   
    }

    public function rules()
    {
        return [
            [['id', 'user_name', 'user_phone', 'warehouse_id', 'created_at', 'status', 'items_count'], 'required'],
            [['id', 'warehouse_id', 'status', 'items_count'], 'integer', 'min' => 1],
        ];
    }

    public static function createOrder(
        string|int $id, 
        string $username, 
        string $userPhone, 
        int $warehouseId,
        int $status,
        int $itemsCount,
        DateTimeImmutable $createdAt,
    ): self
    {
        $order = new Order();
        $order->id = $id;
        $order->user_name = $username;
        $order->user_phone = $userPhone;
        $order->warehouse_id = $warehouseId;
        $order->status = $status;
        $order->items_count = $itemsCount;
        $order->created_at = $createdAt;
    
        if (!$order->insert()) {
            throw new DomainException("Невозможно добавить заказ с id = $id");
        }

        return $order;
    }
    
    public function updateOrder(
        string $username,
        string $userPhone,
        int $warehouseId,
        int $status,
        int $itemsCount,
        DateTimeImmutable $createdAt,
    )
    {
        $this->user_name = $username;
        $this->user_phone = $userPhone;
        $this->warehouse_id = $warehouseId;
        $this->status = $status;
        $this->items_count = $itemsCount;
        $this->created_at = $createdAt;
    
        if (!$this->update()) {
            throw new DomainException("Невозможно обновить заказ с id = $this->id");
        }
    }

    public function getFormattedCreatedAt(string $format = 'Y-m-d H:i:s')
    {
        return $this->created_at->format($format);
    }
    
    public function getFormattedUpdatedAt(string $format = 'Y-m-d H:i:s')
    {
        return $this->updated_at->format($format);
    }
}