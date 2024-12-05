<?php

namespace App\Actions\Element\Remove;

use Exception;
use Illuminate\Database\Eloquent\Model;

class RemoveElementHandler implements RemoveElementHandlerInterface
{
    /** @var Model */
    private $model;

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

        $existModel->restore();
    }
}
