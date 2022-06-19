<?php
namespace app\services;

use app\models\Order;
use app\wrappers\ArrayOrderWrapper;

class FillOrdersService
{
    private int $_totalCount = 0;
    private int $_insertionSuccessCount = 0;
    private int $_insertionFailedCount = 0;
    private int $_updationSuccessCount = 0;
    private int $_updationFailedCount = 0;

    public function execute(array $arrayOfOrders): void
    {
        $this->_totalCount = count($arrayOfOrders);
        $this->_insertionSuccessCount = 0;
        $this->_insertionFailedCount = 0;
        $this->_updationSuccessCount = 0;
        $this->_updationFailedCount  = 0;

        foreach ($arrayOfOrders as $arrayOfOrder) {
            $orderWrapper = ArrayOrderWrapper::create($arrayOfOrder);
            $order = Order::findOne($orderWrapper->getId());

            if ($order === null) {
                $this->_create($orderWrapper);
            } else {
                $this->_update($order, $orderWrapper);
            }
        }
    }

    private function _create(ArrayOrderWrapper $orderWrapper): void
    {
        try {
            Order::createOrder(
                $orderWrapper->getId(),
                $orderWrapper->getUsername(),
                $orderWrapper->getUserPhone(),
                $orderWrapper->getWarehouseId(),
                $orderWrapper->getStatus(),
                $orderWrapper->getItemsCount(),
                $orderWrapper->getCreatedAt(),
            );
            $this->_insertionSuccessCount++;
        } catch (\Throwable $e) {
            $this->_insertionFailedCount++;
        }
    }
    
    private function _update(Order $order, ArrayOrderWrapper $orderWrapper): void
    {
        try {
            $order->updateOrder(
                $orderWrapper->getUsername(),
                $orderWrapper->getUserPhone(),
                $orderWrapper->getWarehouseId(),
                $orderWrapper->getStatus(),
                $orderWrapper->getItemsCount(),
                $orderWrapper->getCreatedAt(),
            );
            $this->_updationSuccessCount++;
        } catch (\Throwable $e) {
            $this->_updationFailedCount++;
        }
    }

    public function getTotalCount()
    {
        return $this->_totalCount;
    }

    public function getInsertionCount(): int
    {
        return $this->getInsertionSuccessCount() + $this->getInsertionFailedCount();
    }

    public function getInsertionSuccessCount(): int
    {
        return $this->_insertionSuccessCount;
    }
    
    public function getInsertionFailedCount(): int
    {
        return $this->_insertionFailedCount;
    }

    public function getUpdationCount(): int
    {
        return $this->getUpdationSuccessCount() + $this->getUpdationFailedCount();
    }

    public function getUpdationSuccessCount(): int
    {
        return $this->_updationSuccessCount;
    }

    public function getUpdationFailedCount(): int
    {
        return $this->_updationFailedCount;
    }
}