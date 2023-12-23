<?php

namespace Src\ExternalSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Log;
use Src\ExternalSystem\Services\ExternalSystemSendServiceInterface;

final class ExternalSystemSendController extends Controller
{
    /**
     * @var ExternalSystemSendServiceInterface
     */
    private $service;

    /**
     * @param ExternalSystemSendServiceInterface $service
     */
    public function __construct(ExternalSystemSendServiceInterface $service)
    {
        $this->service = $service;
    }


    public function __invoke($anketaId)
    {
        $this->service->send($anketaId);
        return redirect()->back();
    }
}
