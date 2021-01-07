<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Models\LeaseInsurance;

class InsuranceController extends Controller
{
    protected $LeaseInsurance;

    public function __construct(LeaseInsurance $LeaseInsurance)
    {
        $this->LeaseInsurance = $LeaseInsurance;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('manage::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('manage::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Request $request)
    {
        $provinces = allUserProvinces();
        $timeType = timeType();
        return view('manage::lease.report.insurance.show',compact("provinces","timeType"));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('manage::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


    //续租列表搜索
    public function search(Request $request)
    {
        $list = $this->LeaseInsurance->getLists($request, "lists");
        $data = $list['list'];
        $count = $list['count'];

        return result("", 0,$data,$count);
    }

    //续租柱状图搜索
    public function histogram(Request $request)
    {
        $data = $this->LeaseInsurance->getHistogram($request, "lists");


        return result("", 0,$data);

    }

     //续租折线图图搜索
    public function broken(Request $request)
    {
        $data = $this->LeaseInsurance->getbroken($request, "lists");


        return result("", 0,$data);

    }



}
