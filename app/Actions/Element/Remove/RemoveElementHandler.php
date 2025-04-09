<?php

namespace App\Actions\Element\Remove;

use App\Enums\ElementType;
use App\Services\FindSimilarElement\FindSimilarCar\FindSimilarCarAction;
use App\Services\FindSimilarElement\FindSimilarDriver\FindSimilarDriverAction;
use App\Services\FindSimilarElement\FindSimilarElementHandlerFactory;
use Exception;
use Illuminate\Database\Eloquent\Model;

class RemoveElementHandler implements RemoveElementHandlerInterface
{
    /** @var Model */
    private $model;

    /**
     * @var FindSimilarElementHandlerFactory
     */
    private $findSimilarHandlerFactory;

    /**
     * @throws Exception
     */
    public function __construct(string $type)
    {
        $model = app("App\\$type");
        if (!$model) {
            throw new Exception('Попытка обновления несуществующего элемента CRM');
        }

        $this->model = $model;
        $this->findSimilarHandlerFactory = new FindSimilarElementHandlerFactory();
    }

    /**
     * @throws Exception
     */
    public function handle($id, bool $deleting)
    {
        if ($deleting) {
            $this->delete($id);
        } else {
            $this->restore($id);
        }
    }

    /**
     * @throws Exception
     */
    private function delete($id)
    {
        $existModel = $this->model::query()->find($id);
        if (!$existModel) {
            throw new Exception("Модель $this->model с ID $id не найдена");
        }

        $existModel->delete();
    }

    /**
     * @throws Exception
     */
    private function restore($id)
    {
        $existModel = $this->model::onlyTrashed()->find($id);
        if (!$existModel) {
            throw new Exception("Модель $this->model с ID $id не найдена в корзине");
        }

        switch (true) {
            case $existModel->getType()->value() === ElementType::DRIVER:
                $action = new FindSimilarDriverAction(
                    $existModel->company,
                    $existModel->fio,
                );
                break;
            case $existModel->getType()->value() === ElementType::CAR:
                $action = new FindSimilarCarAction(
                    $existModel->company,
                    $existModel->gos_number,
                    $existModel->vin
                );
                break;
            default:
                $action = null;
        }

        if ($action) {
            $handler = $this->findSimilarHandlerFactory->make($existModel->getType());
            $handler->handle($action);
        }

        $existModel->restore();
    }
}
