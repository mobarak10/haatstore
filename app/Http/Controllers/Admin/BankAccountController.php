<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\BankAccount;
use App\Models\Bank;

class BankAccountController extends Controller
{

    private $meta = [
        'title' => 'Bank Account',
        'menu' => 'bank',
        'submenu' => ''
    ];

    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Bank::all();
        $bank_account = BankAccount::paginate(15);
        $total_bank_balance = BankAccount::sum('balance');

        return view('admin.bank.account.index', compact('banks','bank_account', 'total_bank_balance'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $bank_id = Bank::all();
        return view('admin.bank.account.create', compact('bank_id'))->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $bank_account = new BankAccount;
         $bank_account->account_name = $request->name;
         $bank_account->bank_id = $request->bank_id;
         $bank_account->account_number = $request->account_number;
         $bank_account->balance = $request->balance;
         $bank_account->branch = $request->branch;
         $bank_account->type = $request->kind;
         $bank_account->note = $request->note;

         if ($bank_account->save()) {
             $request->session()->flash("success", "Account Added Successfully.");
         }
         return redirect(route('admin.bankAccount.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $bank = Bank::find($id);
        return view('admin.bank.account.show', compact('bank'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bank_account = BankAccount::find($id);
        $banks        = Bank::all();

        return view('admin.bank.account.edit', compact('bank_account', 'banks'))->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bank_account = BankAccount::find($id);

        $bank_account->account_name = $request->name;
        $bank_account->bank_id = $request->bank_id;
        $bank_account->account_number = $request->account_number;
        $bank_account->balance = $request->balance;
        $bank_account->branch = $request->branch;
        $bank_account->type = $request->kind;
        $bank_account->note = $request->note;

        if ($bank_account->save()) {
            session()->flash('success', 'Account updated Successfully.');

        }
            return redirect(route('admin.bankAccount.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bank_account = BankAccount::find($id);

        if ($bank_account->delete()) {
            session()->flash('success', 'Account Delete Successfully.');
            return back();
        }
    }

    /**
     *show the transection of this account 
    **/
    public function accountDetails(Request $request) {
        $details = BankAccount::find($request->id);
        return response()->json($details, 200);
    }
}
