<?php

namespace App\Http\Controllers\Admin;

use App\Entities\ResourceApiClient;
use App\Http\Controllers\Controller;
use App\Models\EmailMessage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Facades\AdminTheme as Theme;
use App\Models\EmailHistory;
use Illuminate\Http\Request;
use App\Models\User;

class EmailsController extends Controller
{

    public function configure()
    {
        return Theme::view('emails.configure');
    }

    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required',
            'subject' => 'required',
            'body' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if($user) {
            $user->email([
                'subject' => $request->input('subject'),
                'content' => $request->input('body'),
            ]);

            return redirect()->back()->withSuccess('Email has been sent to '. $user->username);
        }

        // email the contact submission to administrator
        $email = EmailHistory::query()->create([
            'user_id' => null,
            'sender' => config('mail.from.address'),
            'receiver' => $request->input('email'),
            'subject' => $request->input('subject'),
            'content' => $request->input('body'),
            'button' => null,
            'attachment' => NULL,
        ]);

        return redirect()->back()->withSuccess('Email has been sent');
    }

    public function testMail()
    {
        Auth::user()->email([
            'subject' => 'Test email',
            'content' => 'This is a test email sent from admin side',
        ]);

        Artisan::call('cron:emails:send');

        return redirect()->back()->with('success',
            trans('responses.test_email_success', ['default' => 'Test email was sent successfully.'])
        );
    }

    public function messages()
    {
        $lang = request()->input('lang', 'en');
        $defaultMessages = EmailMessage::getAllMessages();
        $messages = EmailMessage::where('language', $lang)->get()->pluck('content', 'key');
        $messages = array_merge($defaultMessages, $messages->toArray());
        return Theme::view('emails.messages', compact('messages', 'lang'));
    }

    public function updateMessages(Request $request)
    {
        $messages = $request->input('messages');
        $lang = $request->input('lang', 'en');

        foreach ($messages as $key => $content) {
            $message = EmailMessage::where('key', $key)->where('language', $lang)->first();
            if ($message) {
                $message->update(['content' => $content]);
            } else {
                EmailMessage::create([
                    'key' => $key,
                    'language' => $lang,
                    'content' => $content,
                ]);
            }
        }
        return redirect()->back()->with('success', __('admin.success'));
    }

    public function history()
    {
        return Theme::view('emails.history');
    }

    public function resend(EmailHistory $email)
    {
        $email->resend();
        return redirect()->back()->with('success',
            trans('responses.resent_email_success', ['default' => 'Email was resent successfully.']));
    }

    public function destroy(EmailHistory $email)
    {
        $email->delete();
        return redirect()->back()->with('success',
            trans('admin.email_deleted_success', ['default' => 'Email was deleted successfully.']));
    }

    // return login page view
    public function templates()
    {
        $api = new ResourceApiClient;
        $marketplace = $api->getAllResources('Templates', 'email');
        if (array_key_exists('error', $marketplace)) {
            $marketplace = [];
        }
        return Theme::view('emails.templates', compact('marketplace'));
    }
}
