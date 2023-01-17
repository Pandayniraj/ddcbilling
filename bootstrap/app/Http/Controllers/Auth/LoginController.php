<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLogin;
use App\Events\UserLogout;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Audit as Audit;
use App\Models\Organization;
use App\Providers\RouteServiceProvider;
use App\User;
use Flash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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
    protected $redirectTo = '/admin/invoice1';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $page_title = 'Login';
        $logo = Organization::find($id = 1);
        $orgs = Organization::all();
        $loginAnnouncement = Announcement::where('placement', 'login')->orderBy('announcements_id', 'DESC')
        ->first();

        return view('auth.login', compact('page_title', 'orgs', 'loginAnnouncement', 'logo'));
    }

    // Hack to have the username as a required field in the validator.
    // See https://laravel.com/docs/5.4/authentication#included-authenticating
    public function username()
    {
        return 'username';
    }

    // Overrides method to fire event.
    protected function authenticated(Request $request, $user)
    {
        if (('root' == $user->username) || ($user->enabled)) {
            Audit::log(Auth::user()->id, trans('general.audit-log.category-login'), trans('general.audit-log.msg-login-success', ['username' => $user->username]));

            Flash::success('Welcome '.Auth::user()->first_name);

            return redirect()->intended($this->redirectPath());
        } else {
            Audit::log(null, trans('general.audit-log.category-login'), trans('general.audit-log.msg-forcing-logout', ['username' => $credentials['username']]));

            Auth::logout();

            return redirect(route('login'))
                    ->withInput($request->only('username', 'remember'))
                    ->withErrors([
                        'username' => trans('admin/users/general.error.login-failed-user-disabled'),
                    ]);
        }
    }

    // Overrides method to fire event.
    public function logout(Request $request)
    {
        // Grab current user and fire event.
        $user = auth()->user();

        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/login');
    }
}
