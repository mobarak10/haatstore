<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\GlAccountHead;
use App\Models\GlAccount;
use Auth;

class GLAccountHeadController extends Controller {
    private $meta = [
        'title' => 'GL Account Head',
        'menu' => 'accounting',
        'submenu' => 'gl-account-head'
    ];

    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $glHeads = GlAccountHead::paginate(15);
        return view('admin.accounting.glAccountHead.index', compact('glHeads'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $records = GlAccount::all();
        return view('admin.accounting.glAccountHead.create', compact('records'))->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // return response()->json($request, 200);

        $code = 'GLH' . str_pad(GlAccountHead::max('id') + 1, 4, '0', STR_PAD_LEFT);

        $gl_head = new GlAccountHead;
        $gl_head->code          = $code;
        $gl_head->name          = $request->name;
        $gl_head->type          = $request->type;
        $gl_head->gl_account_id = $request->gl_account_id;
        $gl_head->description   = $request->description;
        $gl_head->operator_id   = Auth::id();

        if ($gl_head->save()) {

            session()->flash('success', 'GL Head created successfully');
            return response()->json('GL Head created successfully', 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $gl_head = GlAccountHead::find($id);
        return view('admin.accounting.glAccountHead.show', compact('gl_head'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $glAccounts = GlAccount::all();
        $glStatements = config('coderill.statements');
        $record = GlAccountHead::with('glAccount')->find($id);

        return view('admin.accounting.glAccountHead.edit', compact('glAccounts', 'glStatements', 'record'))->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $data = GlAccountHead::find($id);
        $data->name          = $request->name;
        $data->type          = $request->type;
        $data->gl_account_id = $request->gl_account_id;
        $data->description   = $request->description;
        $data->status        = $request->status;
        $data->operator_id   = Auth::id();

        // update
        if ($data->save()) {
            // set flash message 
            session()->flash('success', 'GL account head details updated successfully.');

            // send response
            return response()->json('GL account head details updated successfully.', 200);
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
        $gl_head = GlAccountHead::find($id);

        if ($gl_head->delete()) {
            session()->flash('success', 'GL accound head successfully deleted!.');
            return back();
        }
    }

    public function getGLName(Request $request){
        return response(GlAccount::where('name', $request->statement_id)->get(), 200);
    }

    public function changeStatus($id){
        $gl_head = GlAccountHead::find($id);
        $gl_head->status = ($gl_head->status) ? 0 : 1;
        $gl_head->save();

        return redirect()->back()->withSuccess($gl_head->name . ' GL account head successfully ' . ($gl_head->status == true ? 'activated' : 'deactivated'));
    }
}
