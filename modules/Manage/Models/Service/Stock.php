<?php

namespace Modules\Manage\Models\Service;

use Illuminate\Database\Eloquent\Model;
use Modules\Manage\Models\Report\LeaseServiceStockCancel;
use Modules\Manage\Models\Report\LeaseServiceStockLog;

class Stock extends Model
{
    protected $table = "bl_service_stock_logs";
    protected $table_cancels = "bl_service_stock_cancels";
    
    public $timestamps = false;
    
    //获取补货/退货/回收 列表
    public function getLists($request, $stocktype)
    {
        
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $type = isset($request->type) ? $request->type : 1;
        $renewal_date = request('datetime');
        
        if (!empty($renewal_date)) {
            $time = explode(' - ', $renewal_date);
            $where[] = ['date', '>=', $time[0]];
            $where[] = ['date', '<', $time[1]];
        }
        $where['type'] = $type;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
        }
        
        if ($stocktype == 1) {
            $count = LeaseServiceStockCancel::where($where)->where('systemtype',1)->count('id');
            $list = LeaseServiceStockCancel::select('date', 'total_num', 'num_65', 'num_78', 'num_85', 'num_91', 'num_110',
                'num_118', 'num_129', 'num_132', 'num_145', 'num_209', 'num_214')->where('systemtype',1)
                ->where($where)
                ->orderBy('date','DESC')
                ->offset($page)
                ->limit($limit)
                ->get()
                ->toArray();
        } elseif ($stocktype == 2) {
            $count = LeaseServiceStockCancel::where($where)->where('systemtype', 2)->count('id');
            $list = LeaseServiceStockCancel::select('date', 'total_num', 'num_65', 'num_78', 'num_85', 'num_91',
                'num_110', 'num_118', 'num_129', 'num_132', 'num_145', 'num_209', 'num_214')->where('systemtype', 2)
                ->where($where)
                ->orderBy('date','DESC')
                ->offset($page)
                ->limit($limit)
                ->get()
                ->toArray();
        } elseif ($stocktype == 3) {
            $count = LeaseServiceStockCancel::where($where)->where('systemtype', 3)->count('id');
            $list = LeaseServiceStockCancel::select('date', 'total_num', 'num_65', 'num_78', 'num_85', 'num_91',
                'num_110', 'num_118', 'num_129', 'num_132', 'num_145', 'num_209', 'num_214')->where('systemtype', 3)
                ->where($where)
                ->orderBy('date','DESC')
                ->offset($page)
                ->limit($limit)
                ->get()
                ->toArray();
        }
        $data['list'] = $list;
        $data['count'] = $count;
        
        return $data;
    }
    
    //获取库存列表
    public function getStatisticsLists($request)
    {
        
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        
        $renewal_date = request('datetime');
        $where = [] ;
        if (!empty($renewal_date)) {
            $time = explode(' - ', $renewal_date);
            $where[] = ['date', '>=', $time[0]];
            $where[] = ['date', '<', $time[1]];
        }
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
        }
        
        $count = LeaseServiceStockLog::where($where)->count('id');
        $list = LeaseServiceStockLog::select('date', 'total_num', 'num_65', 'num_78', 'num_85', 'num_91', 'num_110',
            'num_118', 'num_129', 'num_132', 'num_145', 'num_209', 'num_214')
            ->where($where)
            ->orderBy('date','DESC')
            ->offset($page)
            ->limit($limit)
            ->get()
            ->toArray();
        
        $data['list'] = $list;
        $data['count'] = $count;
        
        return $data;
    }
    
}
