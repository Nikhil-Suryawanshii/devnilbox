<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankAccountRequest;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of bank accounts.
     */
    public function index()
    {
        $shop = generaleSetting('shop');
        $user = auth()->user();

        $bankAccounts = BankAccount::where('user_id', $user->id)
            ->orderBy('is_primary', 'desc')
            ->latest('id')
            ->paginate(20);

        return view('shop.bank-account.index', compact('bankAccounts'));
    }

    /**
     * Show the form for creating a new bank account.
     */
    public function create()
    {
        $countries = [
            'TR' => 'Turkey',
            'MK' => 'North Macedonia',
            'UA' => 'Ukraine',
            'XK' => 'Kosovo',
        ];

        return view('shop.bank-account.create', compact('countries'));
    }

    /**
     * Store a newly created bank account in storage.
     */
    public function store(BankAccountRequest $request)
    {
        $user = auth()->user();

        // Check if user has any bank accounts
        $hasAccounts = BankAccount::where('user_id', $user->id)->exists();

        $bankAccount = BankAccount::create([
            'user_id' => $user->id,
            'country_code' => $request->country_code,
            'recipient_name' => $request->recipient_name,
            'bank_name' => $request->bank_name,
            'iban' => $request->iban,
            'swift_bic' => $request->swift_bic,
            'purpose_of_payment' => $request->purpose_of_payment,
            'is_primary' => !$hasAccounts, // First account is primary by default
        ]);

        return $this->json(__('Bank account added successfully!'), [
            'bank_account' => $bankAccount,
        ]);
    }

    /**
     * Display the specified bank account.
     */
    public function show(BankAccount $bankAccount)
    {
        // Ensure user can only view their own bank accounts
        if ($bankAccount->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('shop.bank-account.show', compact('bankAccount'));
    }

    /**
     * Show the form for editing the specified bank account.
     */
    public function edit(BankAccount $bankAccount)
    {
        // Ensure user can only edit their own bank accounts
        if ($bankAccount->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $countries = [
            'TR' => 'Turkey',
            'MK' => 'North Macedonia',
            'UA' => 'Ukraine',
            'XK' => 'Kosovo',
        ];

        return view('shop.bank-account.edit', compact('bankAccount', 'countries'));
    }

    /**
     * Update the specified bank account in storage.
     */
    public function update(BankAccountRequest $request, BankAccount $bankAccount)
    {
        // Ensure user can only update their own bank accounts
        if ($bankAccount->user_id !== auth()->id()) {
            return $this->json(__('Unauthorized action.'), [], 403);
        }

        $bankAccount->update([
            'country_code' => $request->country_code,
            'recipient_name' => $request->recipient_name,
            'bank_name' => $request->bank_name,
            'iban' => $request->iban,
            'swift_bic' => $request->swift_bic,
            'purpose_of_payment' => $request->purpose_of_payment,
        ]);

        return $this->json(__('Bank account updated successfully!'), [
            'bank_account' => $bankAccount,
        ]);
    }

    /**
     * Remove the specified bank account from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        // Ensure user can only delete their own bank accounts
        if ($bankAccount->user_id !== auth()->id()) {
            return back()->withError(__('Unauthorized action.'));
        }

        $bankAccount->delete();

        return back()->withSuccess(__('Bank account deleted successfully.'));
    }

    /**
     * Make the specified bank account primary.
     */
    public function makePrimary(BankAccount $bankAccount)
    {
        // Ensure user can only update their own bank accounts
        if ($bankAccount->user_id !== auth()->id()) {
            return $this->json(__('Unauthorized action.'), [], 403);
        }

        // Remove primary status from all other accounts
        BankAccount::where('user_id', auth()->id())
            ->where('id', '!=', $bankAccount->id)
            ->update(['is_primary' => false]);

        // Set this account as primary
        $bankAccount->update(['is_primary' => true]);

        return $this->json(__('Bank account set as primary successfully!'));
    }

    /**
     * Get bank account details (API endpoint).
     */
    public function getBankAccount(Request $request)
    {
        $user = auth()->user();
        
        $bankAccounts = BankAccount::where('user_id', $user->id)
            ->orderBy('is_primary', 'desc')
            ->latest('id')
            ->get();

        return $this->json(__('Bank accounts retrieved successfully.'), [
            'bank_accounts' => $bankAccounts,
        ]);
    }
}
