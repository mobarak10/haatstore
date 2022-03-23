<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Party;
use App\Models\Cash;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\DueManage;
use Auth;

class DueManagementController extends Controller {

    private $meta = [
        'title'   => 'Due Management',
        'menu'    => 'manage-due',
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
    public function index($type){

        $holder = $type;
        $manage_dues = DueManage::paginate(15);
        return view('admin.due-management.index', compact('manage_dues', 'holder'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type) {
        $this->meta['submenu'] = $type;

        // read cash, bank and supplier/customer
        $cashes = Cash::all();
        $banks = Bank::all();
        $holders = Party::where('genus', $type)->get();

        return view('admin.due-management.create', compact('cashes', 'banks', 'holders'))->with($this->meta)->with('holder_genus', $type);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
 
     */
    public function store(Request $request) {

        // return $request;
        $due_manage = new DueManage;
        $due_manage->party_id        = $request->holder['id'];
        $due_manage->date             = $request->date;
        $due_manage->amount           = $request->amount;
        $due_manage->payment_type     = $request->type;
        $due_manage->cash_id          = $request->cash['id'];
        $due_manage->bank_id          = $request->bank['id'];
        $due_manage->bank_account_id  = $request->bank['account'];
        $due_manage->check_issue_date = $request->bank['date'];
        $due_manage->check_number     = $request->bank['check'];
        $due_manage->description      = $request->description;
        $due_manage->user_id          = 1; // next time it should be change.

        // $due_manage->save();

        if ($due_manage->save()) {
            // if holder is suplier 
            if ($request->holder_type == "supplier") {
                // if amount is payable
                if ($request->type == "paid") {
                    // update holder balance
                    Party::where('id', $request->holder['id'])->increment('balance', $request->amount);
                    // update cash balance
                    if ($request->where == "cash") {
                        Cash::where('id', $request->cash['id'])->decrement('amount', $request->amount);
                    }
                    // update bank balance
                    elseif ($request->where == "bank") {
                        $where = [
                            ['id', $request->bank['account']],
                            ['bank_id', $request->bank['id']],
                        ];
                        BankAccount::where($where)->decrement('balance', $request->amount);
                    }
                }
                elseif($request->type == "received") {
                    // update holder balance
                    Party::where('id', $request->holder['id'])->decrement('balance', $request->amount);
                    // update cash balance
                    if ($request->where == "cash") {
                        Cash::where('id', $request->cash['id'])->increment('amount', $request->amount);
                    }
                    // update bank balance
                    elseif ($request->where == "bank") {
                        $where = [
                            ['id', $request->bank['account']],
                            ['bank_id', $request->bank['id']],
                        ];
                        BankAccount::where($where)->increment('balance', $request->amount);
                    }
                }
            }
            // if holder id customer 
            elseif($request->holder_type == "customer"){
                // if amount is payable
                if ($request->type == "paid") {
                    // update holder balance
                    Party::where('id', $request->holder['id'])->increment('balance', $request->amount);
                    // update cash balance
                    if ($request->where == "cash") {
                        Cash::where('id', $request->cash['id'])->increment('amount', $request->amount);
                    }
                    // update bank balance
                    elseif ($request->where == "bank") {
                        $where = [
                            ['id', '=', $request->bank['account']],
                            ['bank_id', '=', $request->bank['id']],
                        ];
                        BankAccount::where($where)->increment('balance', $request->amount);
                    }
                }
                elseif($request->type == "received") {
                    // update holder balance
                    Party::where('id', $request->holder['id'])->decrement('balance', $request->amount);
                    // update cash balance
                    if ($request->where == "cash") {
                        Cash::where('id', $request->cash['id'])->decrement('amount', $request->amount);
                    }
                    // update bank balance
                    elseif ($request->where == "bank") {
                        $where = [
                            ['id', '=', $request->bank['account']],
                            ['bank_id', '=', $request->bank['id']],
                        ];
                        BankAccount::where($where)->decrement('balance', $request->amount);
                    }
                }
            }
        }
        session()->flash('success', 'Due manage successfully');
        
    }

}
