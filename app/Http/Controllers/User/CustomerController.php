<?php

namespace App\Http\Controllers\User;

use App\Models\Party;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use Auth;

class CustomerController extends Controller
{
    private $meta = [
        'title'   => 'Customer',
        'menu'    => 'customer',
        'submenu' => ''
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->meta['submenu'] = 'list';

        $business_id = Auth::user()->business_id;
        $customers = Party::where('business_id', $business_id)->customers()->paginate(30);
        $print_customers = Party::where('business_id', $business_id)->customers()->get();
        $total_balance = Party::where('business_id', $business_id)->customers()->sum('balance');

        if (request()->search) {
        //    return \request()->all();

            // set conditions
            $where = [
                ['business_id', '=', $business_id]
            ];

            foreach (request()->condition as $input => $value) {
                // return $input;
                if ($value != null) {
                    if ($input == 'name') {
                        $where[] = [$input, 'like', '%' . $value . '%'];
                    } else {
                        $where[] = [$input, '=', $value];
                    }
                }
            }

            $customer = Party::customers()->where($where);

            if (request()->balance_status){
                if (\request()->balance_status === 'receivable'){
                    $customer->where('balance', '<=', -1);
                }else{
                    $customer->where('balance', '>', 0);
                }
            }
            $print_customers = $customer->get();
            $customers = $customer->paginate(30);
            
        }

        return view('user.customer.index', compact('customers','print_customers', 'total_balance'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->meta['submenu'] = 'add';
        return view('user.customer.create')->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // $customers = Party::all();
        $request->validate([
            'balance_status' => 'required'
        ]);

        // foreach ($customers as $customer){
        //     if ($request->phone == $customer->phone){
        //         return back()->with('error', 'Customer allready exist for this mobile number!!!');
        //         // return session()->flash('error', 'Supplier allready exist for this mobile number!!!');
        //     }
        // }

        $data = $request->validate([
            'name'           => 'required|string|max:191',
            'phone'          => 'required|unique:parties',
            'email'          => 'nullable|email',
            'balance'        => 'nullable',
            'address'        => 'nullable|string',
            'division'       => 'nullable|string',
            'district'       => 'nullable|string',
            'thana'          => 'nullable|string',
            'description'    => 'nullable|string',
            'thumbnail'      => 'nullable|integer',
            'category'       => 'nullable|string',
        ]);

        $metas = $request->validate([
            'contact_person'       => 'nullable|string',
            'contact_person_phone' => 'nullable|string'
        ]);

        $data['code']  = 'PRT' . str_pad(Party::withTrashed()->max('id') + 1, 8, '0', STR_PAD_LEFT);
        $data['genus'] = 'customer';
        $data['business_id'] = Auth::user()->business_id;

        if ($request->balance_status == 'payable') {
            $data['balance'] = 0 + abs($data['balance']);
        } elseif ($request->balance_status == 'receivable') {
            $data['balance'] = 0 - abs($data['balance']);
        }

        if ($request->category == 'platinum'){
            $data['discount'] = 20;
        }elseif ($request->category == 'gold'){
            $data['discount'] = 10;
        }elseif ($request->category == 'silver'){
            $data['discount'] = 5;
        }

        $party = Party::create($data); //create party

        foreach ($metas as $key => $meta) {
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
    public function show($id)
    {
        $business_id = Auth::user()->business_id;
        $customer = Party::where('business_id', $business_id)->find($id);
        $saleHasReturns = Sale::where('business_id', $business_id)->where('party_id', $id)->whereHas('saleReturns')->get();
        return view('user.customer.show', compact('customer', 'saleHasReturns'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business_id = Auth::user()->business_id;
        $customer = Party::where('business_id', $business_id)->find($id);
        return view('user.customer.edit', compact('customer'))->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Party $customer)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:191',
            'phone'          => 'required|unique:parties,phone,'.$customer->id,
            'email'          => 'nullable|email',
            'balance'        => 'nullable',
            'address'        => 'nullable|string',
            'division'       => 'nullable|string',
            'district'       => 'nullable|string',
            'thana'          => 'nullable|string',
            'description'    => 'nullable|string',
            'thumbnail'      => 'nullable|integer',
            'category'       => 'nullable|string',
        ]);

        $metas = $request->validate([
            'contact_person'       => 'nullable|string',
            'contact_person_phone' => 'nullable|string'
        ]);


        if ($request->category == 'platinum'){
            $data['discount'] = 20;
        }elseif ($request->category == 'gold'){
            $data['discount'] = 10;
        }elseif ($request->category == 'silver'){
            $data['discount'] = 5;
        }

        $customer->update($data); //update supplier

        foreach ($metas as $key => $value) {
            $customer->metas()->updateOrCreate(['meta_key' => $key], ['meta_value' => $value]);
        }

        return redirect()->route('customer.index')->withSuccess('Customer updated successfully');
    }

    public function changeCustomerStatus($id)
    {
        $customer = Party::find($id);
        $customer->active = ($customer->active) ? 0 : 1;
        $customer->save();

        return redirect()->back()->withSuccess($customer->name . ' customer successfully ' . ($customer->active == true ? 'Activated' : 'Deactivated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Party $customer)
    {
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
        $business_id = Auth::user()->business_id;
        $data['party'] = Party::where('business_id', $business_id)->with('media')->customers()->active()->orderBy('created_at', 'DESC')->get();
        $data['selectedCustomer'] = Party::where('code', 'PRT00000008')->first();
        return response($data, 200);
    }

    public function createNewCustomer(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:191',
            'phone'       => 'nullable|max:45',
            'email'       => 'nullable|email',
            'address'     => 'nullable|string',
        ]);


        $data['code']  = 'PRT' . str_pad(Party::max('id') + 1, 8, '0', STR_PAD_LEFT);
        $data['genus'] = 'customer';
        $data['business_id'] = Auth::user()->business_id;

        $customer = Party::create($data);

        return response()->json($customer, 200);
    }
}
