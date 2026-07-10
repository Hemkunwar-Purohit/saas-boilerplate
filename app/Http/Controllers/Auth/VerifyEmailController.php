<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    // Email verify notice page
    public function notice()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->redirectAfterVerify();
        }
        return view('auth.verify-email', ['user' => auth()->user()]);
    }

    // Verify link click karne par
    public function verify(EmailVerificationRequest $request)
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->redirectAfterVerify();
        }

        $request->fulfill();

        // Activity log
        activity()
            ->causedBy(auth()->user())
            ->log('Email verified');

        return $this->redirectAfterVerify()
            ->with('success', '✅ Email verified successfully! Welcome aboard.');
    }

    // Resend verification email
    public function resend(Request $request)
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->redirectAfterVerify();
        }

        auth()->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email sent! Please check your inbox.');
    }

    private function redirectAfterVerify()
    {
        // Tenant context check
        if (tenant()) {
            return redirect()->route('tenant.dashboard');
        }
        return redirect()->route('admin.dashboard');
    }
}
