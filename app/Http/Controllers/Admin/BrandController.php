<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Party;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    private $meta = [
        'title'   => 'Brand',
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
    public function index()
    {
        $this->meta['submenu'] = 'brand-list';

        $suppliers  = Party::suppliers()->active()->get(['id','name']);
        $brands     = Brand::paginate(15);
        $categories = Category::active()->get();

        return view('admin.suppliers.brand.index', compact('brands', 'suppliers', 'categories'))
            ->with($this->meta);
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
        $request['slug'] = Str::slug($request->name);
        $request->validate([
            'name'       => 'required|string|max:191',
            'party_id'   => 'required|integer',
            'categories' => 'required'
        ]);

        $request['code'] = 'BRA' . str_pad(Brand::max('id') + 1, 6, '0',STR_PAD_LEFT);

        Brand::create($request->only(['party_id', 'code', 'name', 'slug']))->categories()->sync($request->categories);

        return redirect()->back()->withSuccess('Brand created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        $this->meta['submenu'] = 'brand-list';

        $suppliers = Party::suppliers()->active()->get(['id','name']);
        $categories = Category::all();

        return view('admin.suppliers.brand.edit', compact('brand', 'suppliers', 'categories'))
            ->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $request['slug'] = Str::slug($request->name);

        $request->validate([
            'name'       => 'required|string|max:191',
            'party_id'   => 'required|integer',
            'slug'       => 'required|unique:brands,slug,' . $brand->id,
            'active'     => 'required|boolean',
            'categories' => 'required'
        ]);

        $brand->update($request->only(['name', 'party_id', 'slug', 'active']));

        $brand->categories()->sync($request->categories);

        return redirect()->route('admin.brand.index')->withSuccess('Brand updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->back()->withSuccess('Brand deleted successfully');
    }

    /**
     * Toggle Active
     * @param Brand $brand
     * @return mixed
     */
    public function toggleActive(Brand $brand)
    {
        $brand->update(['active' => !$brand->active]);

        return redirect()->back()->withSuccess($brand->name . ' brand successfully ' . ($brand->active === true ? 'activated' : 'deactivated'));
    }

    /*------------------------AJAX Request Methods Start------------------------*/

    /**
     * Get Categories from Brand
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(Request $request)
    {
        // $categories = Category::all();
        $categories = Brand::active()->where('id', $request->brandId)->first()->categories->filter(function ($item){
            return $item->active == 1;
        });
        return response()->json($categories, 200);
    }


    /*-------------------------AJAX Request Methods End------------------------*/

}
