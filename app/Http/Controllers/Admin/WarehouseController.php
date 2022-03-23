<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Warehouse;
use App\Models\User\User;

class WarehouseController extends Controller
{

    private $meta = [
        'title' => 'Warehouse',
        'menu' => 'warehouse',
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
        $warehouses = Warehouse::orderBy('id', 'asc')->paginate(15);
        return view('admin.warehouse.index', compact('warehouses'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $employees = User::all();
        return view('admin.warehouse.create', compact('employees'))->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $code = 'WAH' . str_pad(Warehouse::max('id') + 1, 4, '0', STR_PAD_LEFT);

        $warehouse = new Warehouse;
        $warehouse->code = $code;
        $warehouse->title = $request->title;
        $warehouse->address = $request->address;
        $warehouse->user_id = $request->user_id;
        $warehouse->description = $request->description;

        if($warehouse->save()) {
            $request->session()->flash("success", "Warehouse Added Successfully");
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse){
        return view('admin.warehouse.show', compact('warehouse'))->with($this->meta);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $warehouse = Warehouse::find($id);
        $employees = User::all();
        return view('admin.warehouse.edit', compact('warehouse', 'employees'))->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $warehouse = Warehouse::find($id);

        $warehouse->title = $request->title;
        $warehouse->address = $request->address;
        $warehouse->user_id = $request->user_id;
        $warehouse->description = $request->description;
        $warehouse->status = $request->status;

        if($warehouse->save()) {
            $request->session()->flash("success", "Warehouse Updated Successfully");
        }

        return redirect()->route('admin.warehouse.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $warehouse = Warehouse::find($id);

        if ($warehouse->delete()) {

            session()->flash('success', 'Warehouse delete Successfully');
            return back();
        }
    }

    // status change
    public function changeStatus($id){
        $warehouse = Warehouse::find($id);
        $warehouse->status = ($warehouse->status) ? 0 : 1;
        $warehouse->save();

        return redirect()->back()->withSuccess($warehouse->name . ' successfully ' . ($warehouse->status == true ? 'activated' : 'deactivated'));
    }


    /*----AJAX Request Methods Start----*/

    /**
     * All active warehouses
     * @return \Illuminate\Http\JsonResponse
     */
    public function allActiveWarehouses()
    {
        return response()->json(Warehouse::active()->get(), 200);
    }
    /*----AJAX Request Methods End----*/
}
