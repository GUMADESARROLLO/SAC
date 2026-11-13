<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Http\Middleware\SesionUnica;
use App\Models\EstadoSac;

class LoginController extends Controller
{


    use AuthenticatesUsers;

    public function redirectTo() {

        $role = Auth::User()->id_rol;
        
        switch ($role) {
            case 1:
                return 'Home';
            case 11:
                return 'Schedule';
            case 12:
                return 'Devoluciones';
            case 14:
                return 'Controlados';
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
    public function logout (Request $request) {
        if (auth()->check()) {
            // Al cerrar sesión el SAC pasa a Desconectado
            try { if (EstadoSac::mide(session('rol'))) EstadoSac::cambiar(auth()->id(), 'off', 'logout'); } catch (\Throwable $e) { report($e); }
            SesionUnica::liberar($request, auth()->id());
        }
        auth()->logout();
        return redirect('/');
    }
    public function login(Request $request) {

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }


        $user = $request->username;
        $queryResult = DB::table('users')->where('username', $user)->where('activo', 'S')->pluck('id');
        if (!$queryResult->isEmpty()) {
            if ($this->attemptLogin($request)) {

                $Info_usuario = Usuario::find($queryResult);
                $Rutas = '';

                foreach($Info_usuario as $user)
                {
                    $request->session()->put('name_session', $user->nombre);
                    $request->session()->put('name_rol', $user->RolName->descripcion);
                    $request->session()->put('rol', $user->id_rol);
                    foreach ($user->Detalles as $Rts){
                        $Rutas .= $Rts->RUTA . ' - ';

                    }
                    /**/
                    $request->session()->put('Rutas', $Rutas);
                }
                // Una sola sesión activa: si este usuario estaba abierto en otra máquina, esa se cierra
                SesionUnica::registrar($request, Auth::id());
                //$rol = DB::table('usuario_rol')->where('usuario_id', $queryResult)->pluck('rol_id');
                //;
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
