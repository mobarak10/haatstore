<?php

namespace App\Http\Controllers\Admin;

use App\Models\Party;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    private $meta = [
        'title'   => 'Supplier',
        'menu'    => 'supplier',
        'submenu' => ''
    ];

    public function __construct()
    {
       $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $this->meta['submenu'] = 'list';

        $suppliers = Party::suppliers()->paginate(15);
        $total = Party::suppliers()->sum('balance');

        return view('admin.suppliers.index', compact('suppliers', 'total'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->meta['submenu'] = 'add';

        return view('admin.suppliers.create')
            ->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'balance_status' => 'required'
        ]);

        $data = $request->validate([
            'name'           => 'required|string|max:191',
            'phone'          => 'required|max:45',
            'email'          => 'nullable|email',
            'balance'        => 'nullable',
            'address'        => 'nullable|string',
            'description'    => 'nullable|string',
            'thumbnail'      => 'nullable|integer'
        ]);

        $metas = $request->validate([
            'contact_person'       => 'nullable|string',
            'contact_person_phone' => 'nullable|string'
        ]);

        $data['code']  = 'PRT' . str_pad(Party::max('id') +1, 8, '0', STR_PAD_LEFT);
        $data['genus'] = 'supplier';

        if ($request->balance_status == 'payable'){
            $data['balance'] = -abs($data['balance']);
        }elseif ($request->balance_status == 'receivable'){
            $data['balance'] = abs($data['balance']);
        }

        $party = Party::create($data); //create party

        foreach ($metas as $key => $meta){
            $party->metas()->create(['meta_key' => $key, 'meta_value' => $meta]);
        }

        return redirect()->back()->withSuccess('Suppliers created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Party $supplier) {
        $this->meta['aside'] = false;
        $this->meta['header'] = false;

        return view('admin.suppliers.show', compact('supplier'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Party $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'))
            ->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Party $supplier)
    {
        $request->validate([
            'balance_status' => 'required'
        ]);

        $data = $request->validate([
            'name'        => 'required|string|max:191',
            'phone'       => 'required|max:45',
            'email'       => 'nullable|email',
            'balance'     => 'nullable',
            'address'     => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|integer'
        ]);

        $metas = $request->validate([
            'contact_person'       => 'nullable|string',
            'contact_person_phone' => 'nullable|string'
        ]);

        if ($request->balance_status == 'payable'){
            $data['balance'] = -abs($data['balance']);
        }elseif ($request->balance_status == 'receivable'){
            $data['balance'] = abs($data['balance']);
        }

        $supplier->update($data); //update supplier

        foreach ($metas as $key => $value){
            $supplier->metas()->updateOrCreate(['meta_key' => $key], ['meta_value' => $value]);
        }

        return redirect()->route('admin.supplier.index')->withSuccess('Supplier updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Party $supplier)
    {
        $supplier->delete();

        return redirect()->back()->withSuccess('Supplier deleted successfully');
    }

    /*------------------------AJAX Request Methods Start------------------------*/

    /**
     * Get Party details
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function partyDetails(Request $request)
    {
        return response(Party::find($request->id), 200);
    }

    /**
     * Get Brands from Suppliers
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function brands(Request $request)
    {
        return response("Joy", 200);
        $brands = Party::suppliers()->where('id', $request->supplierId)->first()->brands;
        return response()->json($brands, 200);
    }

    /**
     * Get all supplier
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function allActiveSuppliers()
    {
        return response(Party::with('media')->suppliers()->active()->orderBy('created_at', 'DESC')->get(), 200);
    }

    // status change
    public function changeSuppliersStatus($id){
        $party = Party::find($id);
        $party->active = ($party->active) ? 0 : 1;
        $party->save();

        return redirect()->back()->withSuccess($party->name . ' Supplier successfully ' .($party->active == true ? 'Activated' : 'Deactivated'));
    }


    /**
     * Get all active product of supplier
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allActiveProducts(Request $request)
    {
        return response()->json(Party::find($request->id)->products()->with('warehouses')->active()->get(), 200);
    }


    /*-------------------------AJAX Request Methods End------------------------*/
}
