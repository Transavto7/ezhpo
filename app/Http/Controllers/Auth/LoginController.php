<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\User;
use Illuminate\Bus\Dispatcher;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Src\Notifications\Commands\CreateNotificationsByContext\ContextBuilder;
use Src\Notifications\Commands\CreateNotificationsByContext\CreateNotificationsByContextCommand;
use Src\Reminders\Enums\ReminderAction;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        attemptLogin as parentAttempt;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    // Переписал, чтобы записывать сессии для полей анкет
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        if ($this->isBlocked($request)) {
            $this->incrementLoginAttempts($request);

            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => Lang::get('auth.blocked'),
                ]);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function attemptLogin(Request $request)
    {
        return $this->parentAttempt($request);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => Lang::get('auth.failed'),
            ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isBlocked(Request $request): bool
    {
        return UserService::checksIsBlockedByLogin($request->get('login'));
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'login';
    }

    protected function authenticated(Request $request, $user)
    {
        $dispatcher = app()->make(Dispatcher::class);

        /**
         * @var User $user
         */
        if ($user->isEmployee()) {
            $employee = $user->relatedEmployee;

            $dispatcher->dispatch(new CreateNotificationsByContextCommand(
                ReminderAction::auth(),
                $user,
                ContextBuilder::create()->point($employee->pv_id)
            ));
        }
    }
}
