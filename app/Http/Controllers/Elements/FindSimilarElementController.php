<?php

namespace App\Http\Controllers\Elements;

use App\Company;
use App\Enums\ElementType;
use App\Exceptions\CarWithSameGosNumberAlreadyExist;
use App\Exceptions\CarWithSameGosNumberAlreadyExistInTrash;
use App\Exceptions\CarWithSameVinAlreadyExist;
use App\Exceptions\CarWithSameVinAlreadyExistInTrash;
use App\Exceptions\CompanyWithSameINNAlreadyExist;
use App\Exceptions\CompanyWithSameINNAlreadyExistInTrash;
use App\Exceptions\CompanyWithSameNameAlreadyExist;
use App\Exceptions\CompanyWithSameNameAlreadyExistInTrash;
use App\Exceptions\DriverWithSameNameAlreadyExist;
use App\Exceptions\DriverWithSameNameAlreadyExistInTrash;
use App\Http\Controllers\Controller;
use App\Services\FindSimilarElement\FindSimilarCar\FindSimilarCarAction;
use App\Services\FindSimilarElement\FindSimilarCompany\FindSimilarCompanyAction;
use App\Services\FindSimilarElement\FindSimilarDriver\FindSimilarDriverAction;
use App\Services\FindSimilarElement\FindSimilarElementHandlerFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FindSimilarElementController extends Controller
{
    public function __invoke(string $type, Request $request, FindSimilarElementHandlerFactory $factory)
    {
        try {
            $type = ElementType::fromString($type);
            $id = $request->query('id');

            switch (true) {
                case $type->value() === ElementType::DRIVER:
                    $action = new FindSimilarDriverAction(
                        $this->getCompany($request->query('company_id')),
                        $request->query('fio'),
                        $id,
                        true
                    );
                    break;
                case $type->value() === ElementType::CAR:
                    $action = new FindSimilarCarAction(
                        $this->getCompany($request->query('company_id')),
                        $request->query('gos_number'),
                        $request->query('vin'),
                        $id,
                        true
                    );
                    break;
                case $type->value() === ElementType::COMPANY:
                    $action = new FindSimilarCompanyAction(
                        $request->query('name'),
                        $request->query('inn'),
                        $request->query('kpp'),
                        $request->query('ogrn'),
                        $id,
                        true
                    );
                    break;
                default:
                    throw new \Exception('Undefined element type '.$type->value());
            }

            $handler = $factory->make($type);

            $handler->handle($action);

            return response()->json()->setStatusCode(ResponseAlias::HTTP_OK);
        } catch (CarWithSameGosNumberAlreadyExist
            |CarWithSameVinAlreadyExist
            |DriverWithSameNameAlreadyExist
            |CompanyWithSameNameAlreadyExist
            |CompanyWithSameINNAlreadyExist $exception
        ) {
            return response()->json(['error' => $exception->getMessage()])->setStatusCode(ResponseAlias::HTTP_CONFLICT);
        } catch (CarWithSameGosNumberAlreadyExistInTrash
            |CarWithSameVinAlreadyExistInTrash
            |DriverWithSameNameAlreadyExistInTrash
            |CompanyWithSameNameAlreadyExistInTrash
            |CompanyWithSameINNAlreadyExistInTrash $exception
        ) {
            return response()->json(['error' => $exception->getMessage()])->setStatusCode(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()])->setStatusCode(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getCompany(string $company): Company
    {
        return Company::findOrFail($company);
    }
}
