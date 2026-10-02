<?php

namespace App\Http\Controllers;

use App\Mail\ContactThankYou;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    /**
     * Save a message from the public contact form and thank the sender by email.
     */
    public function store(Request $request): RedirectResponse
    {
        $back = url()->previous().'#contact';

        // Spam is limited by the route's throttle (5 per minute). A hidden "honeypot" field was
        // removed on purpose: browser autofill kept filling it and real messages were dropped.

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9][0-9\s\-()]{6,}$/'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ], ['phone.regex' => 'Please enter a valid mobile number.'], ['phone' => 'mobile number']);

        if ($validator->fails()) {
            // back to the form itself (not the top of the page), with what was typed
            return redirect()->to($back)->withErrors($validator, 'contact')->withInput();
        }

        $message = ContactMessage::create($validator->validated());

        try {
            Mail::to($message->email)->send(new ContactThankYou($message));
            $message->update(['thanked_at' => now()]);
        } catch (Throwable $e) {
            // The message is saved either way; a mail problem must not lose it.
            Log::warning('Contact thank-you email failed: '.$e->getMessage());
        }

        return redirect()->to($back)->with('contact_sent', true);
    }

    public function index(): View
    {
        return view('admin.messages.index', [
            'messages' => ContactMessage::latest()->paginate(15),
            'unread' => ContactMessage::whereNull('read_at')->count(),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return view("admin.messages.show", ["msg" => $message]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.messages')->with('status', 'Message deleted.');
    }
}
