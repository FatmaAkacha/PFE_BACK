<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeAgain;

class ContactController extends Controller
{
    public function contact()
    {
        return view("contact");
    }

    public function send(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'subject' => 'required|string',
        'message' => 'required|string',
    ]);

    Mail::to($request->input('email'))->send(
        new WelcomeAgain($request->input('subject'), $request->input('message'))
    );

    return response()->json(['message' => 'Email envoyé avec succès'], 200);
}

}
