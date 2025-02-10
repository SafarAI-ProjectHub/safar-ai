<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->authenticated($request, Auth::user());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Handle the authenticated user redirection based on roles.
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        // Check the user's role and redirect accordingly
        if ($user->hasRole('Admin')) {
            return redirect(RouteServiceProvider::ADMIN_HOME);
        } elseif ($user->hasRole('Teacher')) {
            return redirect(RouteServiceProvider::TEACHER_HOME);
        } elseif ($user->hasRole('Student')) {
            return redirect(RouteServiceProvider::STUDENT_HOME);
        } elseif ($user->hasRole('Super Admin')) {
            return redirect(RouteServiceProvider::SUPER_ADMIN_HOME);
        }

        // Default redirect if no role is matched
        return redirect(RouteServiceProvider::HOME);
    }
}