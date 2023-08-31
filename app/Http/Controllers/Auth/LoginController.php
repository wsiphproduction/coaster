<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Services\AuditService;
use App\User;
use Illuminate\Support\Facades\Auth;


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
    // protected $redirectTo = '/admin/booking';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        AuditService $auditService)
    {
        $this->auditService = $auditService;
        // $this->middleware('guest')->except('logout');
    }

     public function index(Request $request)
    {
		if ($request->get('username') == null) {
            abort(403);
        }
        $checker = auth()->attempt([
            'username' => $request->username,
            'password' => $request->password,
        ]);

        if ($checker) {
            User::where('id', Auth::id())->update(['isLoggedIn' => 1]);
            $saveLogs = $this->auditService->create($request,"Login User : ". auth()->user()->username,"Login");
            return redirect()->route('admin.booking.index');
        } else {
            return redirect()->back()->withErrors('Invalid login credentials.');
        }
        // return auth()->check() ? redirect()->route('admin.booking.index') : view('auth.login');
    }

    public function login(Request $request)
    {
        $checker = auth()->attempt([
            'username' => $request->username,
            'password' => $request->password,
            'active'   => 1,
            'locked'   => 0
        ]);

        if ($checker) {
            
            $saveLogs = $this->auditService->create($request,"Login User : ". auth()->user()->username,"Login");
            return redirect()->route('admin.booking.index');
        } else {
            return redirect()->back()->withErrors('Invalid login credentials.');
        }
    }
    public function logout(Request $request)
    {
        $saveLogs = $this->auditService->create($request,"Logout User : ". auth()->user()->username,"Logout");
        return auth()->logout() ?? redirect()->route('login');
    }
    public function adminLogin()
    {
        return view('auth.adminlogin');
    }
    /*
    public function adminSubmit(Request $request)
    {
        // Authentication via eloquent
        $admin = User::where('username', $request->username)->where('active', 1)->where('locked', 0)->first();

        if ($admin) {
            // Authenticate the admin without a password
            auth()->login($admin);

            if (auth()->check() && auth()->user()->role == "ADMIN") {
                return redirect()->route('admin.booking.index'); //admin.application.index
            } else {
                abort(503);
            }
        } else {
            return redirect()->back()->withErrors('Invalid login credentials.');
        }
    }
    */
    public function adminSubmit(Request $request)
    {
        $checker = auth()->attempt([
            'username' => $request->username,
            'password' => $request->password,
            'active'   => 1,
            'locked'   => 0
        ]);
        if ($checker) {
            if(auth()->user()->role == "ADMIN"){
                return redirect()->route('admin.application.index');
            }
            else
            {
                abort(503);
            }
        } else {
            //return redirect()->route('admin.application.index');
            return redirect()->back()->withErrors('Invalid login credentials.');
        }
    }
}