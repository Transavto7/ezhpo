<?php

namespace Src\MedicalReference\Http\Controllers;

use App\Anketa;
use App\Driver;
use App\Http\Controllers\Controller;
use DomainException;
use Src\MedicalReference\Factories\BaseDtoFactory;
use Src\MedicalReference\Factories\RecordTargetDtoFactory;
use Src\MedicalReference\Services\MedicalReferenceExporterInterface;

class MedicalReferenceDownloadController extends Controller
{
    /**
     * @var MedicalReferenceExporterInterface
     */
    private $exporter;

    /**
     * @param MedicalReferenceExporterInterface $exporter
     */
    public function __construct(MedicalReferenceExporterInterface $exporter)
    {
        $this->exporter = $exporter;
    }


    public function __invoke($id)
    {
        $anketa = Anketa::query()->findOrFail($id);

        $driver = Driver::query()
            ->where('hash_id', '=', $anketa->driver_id)
            ->first();

        if (!$driver) {
            throw new DomainException('Водитель не найден');
        }

        $baseDtoFactory = new BaseDtoFactory();
        $baseDto = $baseDtoFactory->createDto();

//        $recordTargetDtoFactory = new RecordTargetDtoFactory();
//        $recordTargetDto = $recordTargetDtoFactory->createDto($driver);

        return $this->exporter->export($baseDto);
    }
}
