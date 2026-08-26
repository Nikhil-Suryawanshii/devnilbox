<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportRequest;
use App\Repositories\SupportRepository;

use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUsMail;

class SupportController extends Controller
{
    /**
     * Store a support request and return a JSON response.
     *
     * @param  SupportRequest  $request  The request object
     * @return JSON The JSON response with the support content
     */
    public function store(SupportRequest $request)
    {
        $support = SupportRepository::storeByRequest($request);

        try {
            // Send email to admin
            Mail::to(config('mail.from.address'))->send(new ContactUsMail([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
            ]));
        } catch (\Exception $e) {
            // Log error but continue
            \Log::error('Failed to send contact us email: ' . $e->getMessage());
        }

        return $this->json('Your message has been sent successfully', [
            'content' => $support,
        ]);
    }
}
