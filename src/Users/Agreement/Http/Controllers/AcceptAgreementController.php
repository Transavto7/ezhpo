<?php

namespace Src\Users\Agreement\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

final class AcceptAgreementController
{
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $request->user()->update([
                'accepted_agreement' => true,
            ]);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
        } finally {
            return back();
        }
    }
}
