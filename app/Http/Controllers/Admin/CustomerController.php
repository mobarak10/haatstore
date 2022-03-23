<?php

namespace App\Http\Controllers\Admin;

use App\Models\Party;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Sale;

class CustomerController extends Controller {
    private $meta = [
        'title'   => 'Customer',
        'menu'    => 'customer',
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
    public function index() {
        $this->meta['submenu'] = 'list';

        $customers = Party::customers()->paginate(10);
        $total_balance = Party::customers()->sum('balance');

        return view('admin.customer.index', compact('customers', 'total_balance'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $this->meta['submenu'] = 'add';
        return view('admin.customer.create')->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
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

        $data['code']  = 'PRT' . str_pad(Party::max('id') +1, 8, '0', STR_PAD_LEFT);
        $data['genus'] = 'customer';

        $party = Party::create($data); //create party

        foreach ($metas as $key => $meta){
            $party->metas()->create(['meta_key' => $key, 'meta_value' => $meta]);
        }

        return redirect()->back()->withSuccess('Customer created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $customer = Party::find($id);
        $saleHasReturns = Sale::where('party_id', $id)->whereHas('saleReturns')->get();
        return view('admin.customer.show', compact('customer', 'saleHasReturns'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Party $customer) {
        return view('admin.customer.edit', compact('customer'))->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Party $customer) {
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

        $customer->update($data); //update supplier

        foreach ($metas as $key => $value){
            $customer->metas()->updateOrCreate(['meta_key' => $key], ['meta_value' => $value]);
        }

        return redirect()->route('admin.customer.index')->withSuccess('Customer updated successfully');
    }

    public function changeCustomerStatus($id){
        $customer = Party::find($id);
        $customer->active = ($customer->active) ? 0 : 1;
        $customer->save();

        return redirect()->back()->withSuccess($customer->name . ' customer successfully ' .($customer->active == true ? 'Activated' : 'Deactivated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Party $customer) {
        $customer->delete();
        return redirect()->back()->withSuccess('Customer deleted successfully');
    }

    /*------------------AJAX METHOD-------------------*/

    /**
     * All active customers
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function allActiveCustomers()
    {
        return response(Party::with('media')->customers()->active()->orderBy('created_at', 'DESC')->get(), 200);
    }

    public function createNewCustomer(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:191',
            'phone'       => 'nullable|max:45',
            'email'       => 'nullable|email',
            'address'     => 'nullable|string',
        ]);


        $data['code']  = 'PRT' . str_pad(Party::max('id') +1, 8, '0', STR_PAD_LEFT);
        $data['genus'] = 'customer';

        $customer = Party::create($data);

        return response()->json($customer, 200);
    }
}
