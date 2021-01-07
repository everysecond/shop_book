<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Models\LeaseRenewal;
use Modules\Manage\Models\LeaseRentRebate;

class DataReportController extends Controller
{
    protected $LeaseRenewal;
    protected $LeaseRentRebate;
    public function __construct(LeaseRenewal $LeaseRenewal,LeaseRentRebate $LeaseRentRebate)
    {
        $this->LeaseRentRebate = $LeaseRentRebate;
        $this->LeaseRenewal = $LeaseRenewal;

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
    public function renewalCustomerView(Request $request)
    {
        $provinces = allUserProvinces();
        $timeType = timeType();
        return view('manage::lease.report.data_report.renewal_customer',compact("provinces","timeType"));
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
    public function renewalCustomerSearch(Request $request)
    {
        $list = $this->LeaseRenewal->getRenewalCustomerList($request, "lists");
        $data = $list['list'];
        $count = $list['count'];

        return result("", 0,$data,$count);
    }

    public function expireRenewalView(Request $request)
    {
        $provinces = allUserProvinces();
        $timeType = timeType();
        return view('manage::lease.report.data_report.expire_renewal',compact("provinces","timeType"));
    }

    //续租列表搜索
    public function expireRenewalSearch(Request $request)
    {
        $list = $this->LeaseRenewal->getExpireRenewalList($request, "lists");
        $data = $list['list'];
        $count = $list['count'];

        return result("", 0,$data,$count);
    }

    public function rebateRentView(Request $request)
    {
        $provinces = allUserProvinces();
        $timeType = timeType();
        return view('manage::lease.report.data_report.rebate_rent',compact("provinces","timeType"));
    }

    //续租列表搜索
    public function rebateRentSearch(Request $request)
    {
        $list = $this->LeaseRentRebate->getRebateRentList($request, "lists");
        $data = $list['list'];
        $count = $list['count'];

        return result("", 0,$data,$count);
    }

}
