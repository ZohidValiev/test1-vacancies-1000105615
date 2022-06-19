<?php
namespace app\wrappers;

use DateTimeImmutable;
use InvalidArgumentException;

class ArrayOrderWrapper
{
    private array $_order;


    public static function create(array $order): self
    {
        return new self($order);
    }

    private function __construct(array $order)
    {
        if (empty($order)) {
            throw new InvalidArgumentException("Аргумент order не должен быть пустым массивом.");
        }

        $this->_order = $order;    
    }

    public function getId(): int
    {
        return $this->_order['id'];
    }

    public function getUsername(): string
    {
        return $this->_order['user_name'];
    }
    
    public function getUserPhone()
    {
        return $this->_order['user_phone'];
    }
    
    public function getStatus(): int
    {
        return $this->_order['status'];
    }
    
    public function getItemsCount(): int
    {
        return count($this->_order['items']);
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->_order['created_at']);
    }

    public function getWarehouseId(): int
    {
        return (int) $this->_order['warehouse_id'];
    }
}