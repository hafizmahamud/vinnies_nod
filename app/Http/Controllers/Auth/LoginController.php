<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\App;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username()      => 'required|string',
            'password'             => 'required|string',

        ];

        // if (!App::isLocal()) {
        //     $rules = array_merge($rules, ['g-recaptcha-response' => 'required|captcha']);
        // }
        
        $this->validate($request, $rules);
    }

    public function showLoginForm()
    {
        return view('auth.login')->with(['body_class' => 'login']);
    }

    protected function credentials(Request $request)
    {
        return [
            'email'     => $request->email,
            'password'  => $request->password,
            'is_active' => 1
        ];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::withTrashed()->where('email', $request->email)->first();

        if ($user && !$user->is_active) {
            $error = 'auth.inactive';
        } else {
            $error = 'auth.failed';
        }

        $errors = [$this->username() => trans($error)];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }
}
