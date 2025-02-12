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
        try {
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
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'An error occurred while sending the email.']);
        }
    }

    public function resendCode(Request $request) {
        try {
            $code = rand(1000, 9999);
            $toEmail = $request->email;
            Mail::to($toEmail)->send(new VerificationCodeMail($code));
            session(['verification_code' => $code]);
            return Inertia::render('Verification', [
                'email' => $request->email,
            ]);
        }
         catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'An error occurred while sending the email.']);
         }
    }

    public function verifyCode(Request $request) {
        try {
            $request->validate([
                'code' => 'required|numeric|digits:4',
            ]);
            $code = (int) $request->code;
            $storedCode = session('verification_code');
            if ($code === $storedCode) {
                return Inertia::render('Setup', [
                    'email' => $request->email,
                ]);
            }  else{
                return Inertia::render('Verification', [
                    'email' => $request->email,
                    'errorMessage' => 'Invalid verification code'
                ]);
            }
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'An error occurred while sending the email.']);
        }
    }

    public function forgotPassword(Request $request) {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);
            
            $status = Password::sendResetLink(
                $request->only('email')
            );
         
            return $status === Password::RESET_LINK_SENT
                        ? back()->with(['status' => __($status)])
                        : back()->withErrors(['email' => __($status)]);   
        }catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'An error occurred while sending the email.']);
        }
    }
    
    public function resetPassword (string $token) {
        try {
            return Inertia::render('ResetPassword', ['token' => $token, 
            'email' => request()->email]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'An error occurred while sending the email.']);
        }
    }
}
