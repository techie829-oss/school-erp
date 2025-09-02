<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Show the home page
     */
    public function home(): View
    {
        return view('landing.home');
    }

    /**
     * Show the features page
     */
    public function features(): View
    {
        return view('landing.features');
    }

    /**
     * Show the pricing page
     */
    public function pricing(): View
    {
        return view('landing.pricing');
    }

    /**
     * Show the about page
     */
    public function about(): View
    {
        return view('landing.about');
    }

    /**
     * Show the contact page
     */
    public function contact(): View
    {
        return view('landing.contact');
    }

    /**
     * Show the color palette page
     */
    public function colorPalette(): View
    {
        return view('landing.color-palette');
    }

    /**
     * Show the multi-tenancy demo page
     */
    public function multiTenancyDemo(): View
    {
        return view('landing.multi-tenancy-demo');
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // TODO: Send email notification
        // Mail::to(config('all.company.email'))->send(new ContactFormMail($validated));

        return back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');
    }
}
