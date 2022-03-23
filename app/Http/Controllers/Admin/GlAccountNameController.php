<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\GlAccount;
use Auth;

class GlAccountNameController extends Controller
{

    private $meta = [
        'title' => 'GL Account',
        'menu' => 'accounting',
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
        $gl_accounts = GlAccount::paginate(15);
        return view('admin.accounting.glAccount.index', compact('gl_accounts'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //insert
        $code = 'GL' . str_pad(GlAccount::max('id') + 1, 4, '0', STR_PAD_LEFT);

        $gl_account = new GlAccount;
        $gl_account->gl_code = $code;
        $gl_account->gl_name = $request->name;
        $gl_account->gl_type = $request->type;
        $gl_account->gl_details = $request->description;
        $gl_account->operator_id = Auth::id();

        if ($gl_account->save()) {
            session()->flash('success', 'GL Account successfully Added.');
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $gl_account = GlAccount::find($id);
        return view('admin.accounting.glAccount.show', compact('gl_account'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $gl_account = GlAccount::find($id);

        return view('admin.accounting.glAccount.edit', compact('gl_account'))->with($this->meta);
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
        $gl_account = GlAccount::find($id);

        $gl_account->gl_name     = $request->name;
        $gl_account->gl_type     = $request->type;
        $gl_account->gl_details  = $request->description;
        $gl_account->operator_id = Auth::id();

        if ($gl_account->save()) {
            session()->flash('success', 'GL Account successfully Updated.');
            return redirect(route('admin.glAccountName.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gl_account = GlAccount::find($id);

        if ($gl_account->delete()) {
            session()->flash('success', 'GL Account successfully Deleted.');
            return back();
        }
    }

    public function changeStatus($id){
        $gl_account = GlAccount::find($id);
        $gl_account->status = ($gl_account->status) ? 0 : 1;
        $gl_account->save();

        return redirect()->back();

    }
}
