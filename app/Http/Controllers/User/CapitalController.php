<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cash;
use App\Models\Capital;
use Illuminate\Http\Request;

class CapitalController extends Controller
{

        private $meta = [
        'title' => 'Capital',
        'menu' => 'capital',
        'submenu' => ''
    ];

    public function __construct() {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $capitals = Capital::with('cash')->paginate(25);

      if (request()->search){

        // set conditions
            $where = [];
            $date =[];
            foreach (request()->condition as $input => $value) {
                // return $input;
                if ($value != null) {
                    $where[] = [$input, 'like', '%' . $value . '%'];
                }
            }

            $capitals = Capital::where($where);

             // set date
            if (request()->from_date != null) {
                $date[] = date(request()->from_date);
            }

            if (request()->to_date!= null) {
                $date[] = date(request()->to_date);
            } else {
                if (request()->from_date != null) {
                    $date[] = date('Y-m-d');
                }
            }

            if (count($date) > 0) {
                $capitals = $capitals->whereBetween('date', $date);
            }

            // get data
            $capitals = $capitals->paginate(25);
        }

        return view('user.capital.index',compact('capitals'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cashes = Cash::get();

        return view('user.capital.create', compact('cashes'))->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'date'         =>   'required',
            'capital_name' =>   'required | max:100',
            'amount'       =>   'required | numeric',
            'cash_id'      =>   'required|integer',
            'description'  =>   'nullable',

        ]);

        //increament with cash
        Cash::findOrFail($request->cash_id)->increment('amount', $request->amount);

        // insert
        Capital::create($data);

        // message
        $request->session()->flash("success", "Capital added has been successfully.");

        // view
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get the specified data
         $capital = Capital::findOrFail($id);

        return view('user.capital.show', compact('capital'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         // get the specified data
            $capital = Capital::findOrFail($id);
           $cashes = Cash::get();

         return view('user.capital.edit' , compact('capital','cashes'))->with($this->meta);

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
         // get the specified data
         $capital = Capital::findOrFail($id);

         $data = $request->validate([
            'date'         =>   'required',
            'capital_name' =>   'required | max:100',
            'amount'       =>   'required | numeric',
            'cash_id'      =>   'required|integer',
            'description'  =>   'nullable',

        ]);

        if($request->amount > $capital->amount){

        //increament with cash
        Cash::findOrFail($request->cash_id)->increment('amount',($request->amount-$capital->amount));

        }

        elseif($request->amount < $capital->amount){

        //decreament with cash
        Cash::findOrFail($capital->cash_id)->decrement('amount', ($capital->amount-$request->amount));

        }

        // update
        $capital->update($data);

        // message
        $request->session()->flash("success", "Capital updated successfully.");

        // view
        return back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         // get the specified data
         $capital = Capital::findOrFail($id);

           //decreament with cash
        Cash::findOrFail($capital->cash_id)->decrement('amount', $capital->amount);

         // delete from db
        if($capital->delete()) {

            // flash message
            session()->flash('success', "Data has been deleted successfully!");
        }

        // view
        return back();
    }
}
