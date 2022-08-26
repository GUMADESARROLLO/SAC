<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;

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
    //protected $redirectTo = RouteServiceProvider::HOME;
    public function redirectTo() {

        // rol de usuario
        $role = Auth::User()->activeRole();

        switch ($role) {
            case '1':
                return 'Home';
            break;
            default:
                return '/login';
            break;
        }
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

    public function username() {
        return 'username';
    }

    public function login(Request $request) {

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }


        $user = $request->username;
        $queryResult = DB::table('tbl_usuario')->where('Usuario', $user)->where('activo', 'S')->pluck('id');

        dd($this->attemptLogin($request));

        if (!$queryResult->isEmpty()) {
            if ($this->attemptLogin($request)) {
                //$rol = DB::table('usuario_rol')->where('usuario_id', $queryResult)->pluck('rol_id');
                //$request->session()->put('rol', $rol);
                return $this->sendLoginResponse($request);
            }
        }
        return $this->sendFailedLoginResponse($request);
    }

    public function showLoginForm()
    {
        return view('Usuario.loginSplit');
        
    }
}
