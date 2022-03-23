<?php

namespace App\Http\Controllers\Admin\PayrollManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Models\AdvancedSalary;
use App\Models\AdvancedSalaryDetails;

class AdvancedSalaryController extends Controller
{
    private $meta = [
        'title' => 'Advanced Salary',
        'menu' => 'payroll',
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
        $users = User::all();
        $advanced_salary = AdvancedSalary::with('advancedSalaryDetails.advancedSalaryPaidDetails')->paginate(15);
        $total_advanced = $advanced_salary->sum('adv_amount');
        return view('admin.payroll-management.advanced-salary.index', compact('users', 'advanced_salary', 'total_advanced'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        return view('admin.payroll-management.advanced-salary.create', compact('users'))->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $exist = AdvancedSalary::where('user_id', $request->user_id)->first();
        // return $exist->id;
        if ($exist){
            $advanced_salary_details_data = $request->validate([
                'adv_amount' => 'required|numeric',
                'installment_amount' => 'required|numeric',
                'note' => 'nullable|string',
            ]);

            $advanced_salary_details_data['advanced_salary_id'] = $exist->id;
            AdvancedSalaryDetails::create($advanced_salary_details_data);
            return redirect()->back()->withSuccess('Advanced given successfully');
        }else{
            $advanced_salary_data = $request->validate([
                'user_id' => 'required',
            ]);
            $advanced_salary = AdvancedSalary::create($advanced_salary_data);

            $advanced_salary_details_data = $request->validate([
                'adv_amount' => 'required|numeric',
                'installment_amount' => 'required|numeric',
                'note' => 'nullable|string',
            ]);

            $advanced_salary_details_data['advanced_salary_id'] = $advanced_salary->id;
            AdvancedSalaryDetails::create($advanced_salary_details_data);
            return redirect()->back()->withSuccess('Advanced given successfully');
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
        $advanced_salary_details = AdvancedSalaryDetails::where('advanced_salary_id', $id)->get();
        $user = User::where('id', $advanced_salary_details->first()->advancedSalary->user->id)->first();
        return view('admin.payroll-management.advanced-salary.show', compact('advanced_salary_details', 'user'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $advanced_salary_details = AdvancedSalaryDetails::find($id);
        // $users = User::all();
        return view('admin.payroll-management.advanced-salary.edit', compact('advanced_salary_details'))->with($this->meta);
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
        $advanced_salary_details = AdvancedSalaryDetails::find($id);
        // return $request->all();
        $advanced_salary_details_data = $request->validate([
                'adv_amount' => 'required|numeric',
                'installment_amount' => 'required|numeric',
                'note' => 'nullable|string',
            ]);

        $advanced_salary_details->update($advanced_salary_details_data);
        session()->flash('success', 'Advanced salary updated successfully');
        return redirect(route('admin.advancedSalary.show', $advanced_salary_details->advancedSalary->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
