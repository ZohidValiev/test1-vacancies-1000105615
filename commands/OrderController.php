<?php
namespace app\commands;

use app\models\Order;
use app\services\FillOrdersService;
use yii\console\Controller;
use yii\console\ExitCode;

class OrderController extends Controller
{
    public function actionUpdateLocale(string $filename)
    {   
        $arrayOfOrders = $this->_loadOrdersFormFile($filename);
        $fillOrdersService = new FillOrdersService();
        $fillOrdersService->execute($arrayOfOrders);

        echo 'Файл заказов успешно обработан.', PHP_EOL;
        echo 'Общее количество заказов: ' . $fillOrdersService->getTotalCount(), PHP_EOL;
        echo '------------------------------------------------------------', PHP_EOL;
        $insertOut = <<<INSERT
            Общее количество вставок: {$fillOrdersService->getInsertionCount()}
            Успешно добавлено:        {$fillOrdersService->getInsertionSuccessCount()}
            Ошибки добавления:        {$fillOrdersService->getInsertionFailedCount()}
        INSERT;
        echo $insertOut, PHP_EOL;

        echo '------------------------------------------------------------', PHP_EOL;
        $updatedOut = <<<UPDATE
            Общее количество обновлений: {$fillOrdersService->getUpdationCount()}
            Успешно обновлено:           {$fillOrdersService->getUpdationSuccessCount()}
            Ошибки обновления:           {$fillOrdersService->getUpdationFailedCount()}
        UPDATE;
        echo $updatedOut, PHP_EOL;
        
        return ExitCode::OK;
    }

    public function actionInfo($orderId)
    {
        if (!is_numeric($orderId)) {
            echo "Введите числовое значение.", PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $orderId = (int) $orderId;

        if ($orderId < 1) {
            echo 'Введите значение больше 0(нуля).', PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        try {
            $order = Order::findOne($orderId);
        } catch (\Throwable $th) {
            echo "Произошла ошибка во время чтения заказа из базы данных.", PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }
        

        if ($order === null) {
            echo "Закас с id = $orderId не найден.", PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        
        $this->_printAsJson($order);
        return ExitCode::OK;
    }

    private function _loadOrdersFormFile(string $filename): array
    {
        if (!is_file($filename)) {
            echo "Введенный файл '$filename' не существует.";
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return json_decode(file_get_contents($filename), true)['orders'];
    }

    private function _printAsJson(Order $order): void
    {
        $data = <<<JSON
        {
            "id": $order->id
            "user_name": "$order->user_name"
            "user_phone": "$order->user_phone"
            "warehouse_id": "$order->warehouse_id"
            "created_at": "$order->formattedCreatedAt"
            "updated_at": "$order->formattedUpdatedAt"
            "status": $order->status
            "items_count": $order->items_count
        }
        JSON;

        echo $data, PHP_EOL;
    }
}