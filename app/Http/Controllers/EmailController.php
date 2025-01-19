<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Mail\ResetPassMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Support\Facades\Password;

class EmailController extends Controller
{
    public function sendEmail(Request $request) {
        if ($request->email == null) {
            return redirect()->back()->withErrors(['email' => 'Email is required']);
        }

        $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);

        $code = rand(1000, 9999);
        $toEmail = $request->email;
        Mail::to($toEmail)->send(new VerificationCodeMail($code));
        session(['verification_code' => $code]);
        return Inertia::render('Verification', [
            'email' => $request->email,
            'code' => $code
        ]);
    }

    public function forgotPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        $status = Password::sendResetLink(
            $request->only('email')
        );
     
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);     
    }
    
    public function resetPassword (string $token) {
        return Inertia::render('ResetPassword', ['token' => $token, 
        'email' => request()->email]);
    }
}
