<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Bank;
use Illuminate\Support\Str;


class BankController extends Controller
{

    private $meta = [
        'title' => 'Bank',
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
        $banks = Bank::paginate(15);
        return view('admin.bank.index', compact('banks'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.bank.create')->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $code = 'BNK' . str_pad(Bank::max('id') + 1, 4, '0', STR_PAD_LEFT);

        $bank = new Bank;
        $bank->code = $code;
        $bank->name = $request->name;
        $bank->slug = Str::slug($request->name);

        if ($bank->save()) {
            $request->session()->flash("success", "Bank Added Successfully.");
        }

        return redirect(route('admin.bank.index'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $bank = Bank::find($id);
        return view('admin.bank.edit', compact('bank'))->with($this->meta);
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
        $bank = Bank::find($id);

        $bank->name = $request->name;
        $bank->slug = Str::slug($request->name);
        $bank->status = $request->status;

        if($bank->save()) {
            $request->session()->flash("success", "Bank Updated Successfully.");
        }

        return redirect()->route('admin.bank.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bank = Bank::find($id);

        if ($bank->delete()) {
            session()->flash('success', 'bank delete Successfully.');
            return back();
        }
    }

    // status change
    public function changeStatus($id) {
        $bank = Bank::find($id);
        $bank->status = ($bank->status) ? 0 : 1;
        $bank->save();

        return redirect()->back()->withSuccess($bank->name . ' successfully ' . ($bank->status == true ? 'activated' : 'deactivated'));
    }

    /**
     * Get all bank accounts
     * @return \Illuminate\Http\JsonResponse
     */
    public function allBankAccounts()
    {
        return response()->json(Bank::all(), 200);
    }

    /**
     *
     */
    public function accounts(Request $request) {
        $accounts = Bank::find($request->id)->bankAccounts;
        return response()->json($accounts, 200);
    }
}
