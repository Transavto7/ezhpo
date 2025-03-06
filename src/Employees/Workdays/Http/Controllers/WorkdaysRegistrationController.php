<?php
declare(strict_types=1);

namespace Src\Employees\Workdays\Http\Controllers;

use App\Enums\BlockActionReasonsEnum;
use App\ValueObjects\ForeignDevice\Alcometer;
use App\ValueObjects\ForeignDevice\Pulse;
use App\ValueObjects\ForeignDevice\Temperature;
use App\ValueObjects\ForeignDevice\Tonometer;
use Exception;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\JsonResponse;
use Src\Employees\Workdays\Commands\WorkdaysRegistration\WorkdaysRegistrationCommand;
use Src\Employees\Workdays\Commands\WorkdaysRegistration\WorkdaysRegistrationHandler;
use Src\Employees\Workdays\SmartEnum\TypeAnketaSmartEnum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class WorkdaysRegistrationController
{
    public function __invoke(WorkdaysRegistrationHandler $handler, Request $request): JsonResponse
    {
        try {
            /** @var User $terminal */
            $terminal = $request->user('api');
            if ($terminal->blocked) {
                throw new Exception(BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::TERMINAL_BLOCK), 400);
            }

            $command = (new WorkdaysRegistrationCommand())
                ->setTerminal($terminal)
                ->setDate($request->date)
                ->setEmployeeId($request->employee_id)
                ->setProbaAlko($request->proba_alko)
                ->setTypeAnketa(TypeAnketaSmartEnum::create($request->type_anketa))
                ->setTestNarko($request->test_narko)
                ->setPhoto($request->photo)
                ->setVideo($request->video);

            if ($request->t_people) {
                $command->setPeopleThermometer(new Temperature((float)$request->t_people));
            }
            if ($request->tonometer) {
                $command->setTonometer(Tonometer::fromString($request->tonometer));
            }
            if ($request->pulse) {
                $command->setPulse(new Pulse((int)$request->pulse));
            }
            if ($request->alcometer_result) {
                $command->setAlcometer(new Alcometer((float)$request->alcometer_result, (int)$request->alcometer_mode));
            }

            $response = $handler->handle($command);

            return response()->json($response->toArray());
        } catch (Throwable $exception) {
            $code = $exception->getCode();
            if ($code < 400 || $code >= 600) {
                $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            return response()->json([
                'message' => $exception->getMessage()
            ], $code);
        }
    }
}
