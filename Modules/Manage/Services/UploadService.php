<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/9/11 16:52
 */

namespace Modules\Manage\Services;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Excel;
use Modules\Manage\Models\Crm\CrmImage;
use Modules\Manage\Models\Crm\CrmTeamList;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\PositionStaff;
use Ramsey\Uuid\Uuid;

class UploadService
{
    //上传图片
    public function upload(Request $request, $absolutePath, $table)
    {
        $file = $request->file('file');
        if (!$file) {
            return;
        }
        $oldName = $file->getClientOriginalName();

        $fileSize = $file->getSize();
        $ext = $file->getClientOriginalExtension();

        $exts = config('upload.allow_image_ext');
        if (!in_array($ext, $exts)) {
            return retArr('图片格式不正确', [], Response::HTTP_BAD_REQUEST);
        }

        $maxSize = config('upload.max_size');
        if (!$fileSize || $fileSize > MB2Bytes($maxSize)) {
            return retArr('文件超出大小', [], Response::HTTP_BAD_REQUEST);
        }

        $fileName = sprintf('%s.%s', Uuid::uuid4(), $ext);
        // 分散文件，避免单目录文件上限
        $savePath = storage_path(sprintf('app/public/uploads/%s/%s', substr($fileName, 0, 2), substr($fileName, 2, 2)));
        $file = $file->move($savePath, $fileName);

        $storageFilepath = str_replace(storage_path('app/public'), '', $file->getPathname());

        $record = [
            'relative_path' => config("upload.prefix") . $storageFilepath,
            'absolute_path' => $absolutePath . $storageFilepath,
            'file_size'     => $file->getSize(),
            'file_name'     => $oldName,
            'mime_type'     => $file->getMimeType(),
            'ext_type'      => $ext,
            'created_by'    => getUserId(),
            'created_at'    => time(),
            'related_table' => $table
        ];
        $upload_id = app(CrmImage::class)->insertGetId($record);
        if ($upload_id) {
            return [
                'relative_path' => config("upload.prefix") . $storageFilepath,
                'absolute_path' => $absolutePath . $storageFilepath,
                'file_size'     => $file->getSize(),
                'file_name'     => $oldName,
                'upload_id'     => $upload_id,
                'mime_type'     => $file->getMimeType(),
                'code'          => 200
            ];
        } else {
            return retArr('系统错误', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //导出客户excel
    public function downSeaCusExcel(Request $request)
    {
        $query = CrmUser::query();

        $query->where('charger_id','=', 0);
        if ($k = $request->appid) $query->where('sea_type', $k);
        if ($k = $request->cus_level) $query->where('cus_level', $k);
        if ($k = $request->cus_source) $query->where('cus_source', $k);
        if ($k = $request->history_deal) $query->where('history_deal', $k);
        if ($k = $request->province_id) $query->where('province_id', $k);
        if ($k = $request->created_at) {
            $dateArr = explode(' - ', $k);
            if (isset($dateArr[1])) {
                $dateArr[1] = $dateArr[1] . ' 23:59:59';
            } else {
                $dateArr[1] = now();
            }
            $query->whereBetween('created_at', $dateArr);
        };
        if ($search = $request->name) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");

            });
        }

        $cellData = [array('客户名称(手机号)', '客户类型', '客户来源', '客户号码', '所在地区', '创建时间')];
        $cusTypeArr = CrmUser::CUS_TYPE_ARR;
        $cusSourceArr = CrmUser::CUS_SOURCE_ARR;
        $query->orderByRaw('FIELD(is_top,1,0),FIELD(is_mark,1,0),id desc,created_at desc')
            ->select('name', 'short_name', 'cus_type', 'cus_source', 'mobile', 'charger_name', 'created_at', 'province_name', 'city_name');
        $count = $query->count();
        $query->chunk('1000', function ($customers) use (&$cellData, $cusTypeArr, $cusSourceArr) {
            foreach ($customers as $customer) {
                $cellData[] = [
                    $customer->name ? $customer->name : $customer->mobile,
                    isset($cusTypeArr[$customer->cus_type]) ? $cusTypeArr[$customer->cus_type] : '',
                    isset($cusSourceArr[$customer->cus_source]) ? $cusTypeArr[$customer->cus_source] : '',
                    $customer->mobile,
                    $customer->province_name . $customer->city_name,
                    $customer->created_at
                ];
            }
        });
        foreach ($cellData as &$cell) {
            $cell = filterEmoji($cell);
        }
        try {
            ini_set('memory_limit', '500M');
            set_time_limit(0);//设置超时限制为0分钟
            app(Excel::class)->create('客户列表信息', function ($excel) use ($cellData, $count) {
                $excel->sheet('score', function ($sheet) use ($cellData, $count) {
                    $sheet->rows($cellData);
                    $sheet->cells("A1:G1", function ($cells) {
                        $cells->setAlignment('center');//字体水平居中
                        $cells->setFontWeight('bold');
                    });
                    $sheet->cells("A1:G" . ($count + 1), function ($cells) {
                        $cells->setAlignment('center');//字体水平居中
                    });
                    $sheet->setWidth(array(
                        'A' => 25,
                        'B' => 15,
                        'C' => 10,
                        'D' => 20,
                        'E' => 20,
                        'F' => 20,
                    ));
                });
            })->export('xls');
        } catch (\Exception $e) {
            throw $e;
        }
    }


    //导出客户excel
    public function downCusExcel(Request $request)
    {
        $query = CrmUser::query();
        if ($k = $request->cus_type) $query->where('cus_type', $k);
        if ($k = $request->cus_level) $query->where('cus_level', $k);
        if ($k = $request->cus_source) $query->where('cus_source', $k);
        if ($k = $request->history_deal) $query->where('history_deal', $k);
        if ($k = $request->province_id) $query->where('province_id', $k);
        if ($k = $request->date1) {
            $dateArr = explode(' - ', $k);
            if (isset($dateArr[1])) {
                $dateArr[1] = $dateArr[1] . ' 23:59:59';
            } else {
                $dateArr[1] = now();
            }
            $query->whereBetween('created_at', $dateArr);
        };
        if ($search = $request->searchStr) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%")
                    ->orWhere('charger_name', 'like', "%$search%");
            });
        }
        $type = $request->type ?? 'myself';
        if ($type == 'myself') {//自己客户
            $query->where('charger_id', getUserId());
        } elseif ($type == 'under') {//下属客户
            //下属职员ids
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
            if (!empty($underStaffIds)) {
                $query->whereIn('charger_id', array_unique($underStaffIds));
            } else {
                $query->where("id", "<", 0);
            }
        } elseif ($type == 'myteam') {
            $teamCus = CrmTeamList::query()->where([
                'user_id'   => getUserId(),
                'team_role' => CrmTeamList::ROLE_TWO
            ])->pluck('customer_id')->toArray();
            $query->whereIn('id', array_unique($teamCus))->where('charger_id', '!=', getUserId());
        } elseif ($type == 'underteam') {
            //下属职员ids
            $underStaffIds = array_unique(app(PositionStaff::class)->allUnderStaffIds(getUserId()));
            if (!empty($underStaffIds)) {
                $teamCus = CrmTeamList::query()->where('team_role', CrmTeamList::ROLE_TWO)
                    ->whereIn('user_id', $underStaffIds)->pluck('customer_id')->toArray();
                $query->whereIn('id', array_unique($teamCus));
            } else {
                $query->where("id", "<", 0);
            }
        }
        $cellData = [array('客户名称(手机号)', '客户类型', '客户来源', '客户号码', '所在地区', '负责人', '创建时间')];
        $cusTypeArr = CrmUser::CUS_TYPE_ARR;
        $cusSourceArr = CrmUser::CUS_SOURCE_ARR;
        $query->orderByRaw('FIELD(is_top,1,0),FIELD(is_mark,1,0),id desc,created_at desc')
            ->select('name', 'short_name', 'cus_type', 'cus_source', 'mobile', 'charger_id', 'created_at', 'province_name', 'city_name');
        $count = $query->count();
        $allUsers = allUsersArr();
        $query->chunk('1000', function ($customers) use (&$cellData, $cusTypeArr, $cusSourceArr, $allUsers) {
            foreach ($customers as $customer) {
                $cellData[] = [
                    $customer->name ? $customer->name : $customer->mobile,
                    isset($cusTypeArr[$customer->cus_type]) ? $cusTypeArr[$customer->cus_type] : '',
                    isset($cusSourceArr[$customer->cus_source]) ? $cusTypeArr[$customer->cus_source] : '',
                    $customer->mobile,
                    $customer->province_name . $customer->city_name,
                    isset($allUsers[$customer->charger_id]) ? $allUsers[$customer->charger_id]['name'] : '',
                    $customer->created_at
                ];
            }
        });

        foreach ($cellData as &$cell) {
            $cell = filterEmoji($cell);
        }
        try {
            ini_set('memory_limit', '500M');
            set_time_limit(0);//设置超时限制为0分钟
            app(Excel::class)->create('客户列表信息', function ($excel) use ($cellData, $count) {
                $excel->sheet('score', function ($sheet) use ($cellData, $count) {
                    $sheet->rows($cellData);
                    $sheet->cells("A1:G1", function ($cells) {
                        $cells->setAlignment('center');//字体水平居中
                        $cells->setFontWeight('bold');
                    });
                    $sheet->cells("A1:G" . ($count + 1), function ($cells) {
                        $cells->setAlignment('center');//字体水平居中
                    });
                    $sheet->setWidth(array(
                        'A' => 25,
                        'B' => 15,
                        'C' => 10,
                        'D' => 20,
                        'E' => 20,
                        'F' => 20,
                        'G' => 20
                    ));
                });
            })->export('xls');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    //导出客户联系人excel
    public function downContactExcel(Request $request)
    {
        $query = CrmUser::query();
        if ($k = $request->cus_type) $query->where('cus_type', $k);
        if ($k = $request->cus_level) $query->where('cus_level', $k);
        if ($k = $request->cus_source) $query->where('cus_source', $k);
        if ($k = $request->history_deal) $query->where('history_deal', $k);
        if ($k = $request->province_id) $query->where('province_id', $k);
        if ($k = $request->date1) {
            $dateArr = explode(' - ', $k);
            if (isset($dateArr[1])) {
                $dateArr[1] = $dateArr[1] . ' 23:59:59';
            } else {
                $dateArr[1] = now();
            }
            $query->whereBetween('created_at', $dateArr);
        };
        if ($search = $request->searchStr) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%")
                    ->orWhere('charger_name', 'like', "%$search%");
            });
        }
        $type = $request->type ?? 'myself';
        if ($type == 'myself') {//自己客户
            $query->where('charger_id', getUserId());
        } elseif ($type == 'under') {//下属客户
            //下属职员ids
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
            if (!empty($underStaffIds)) {
                $query->whereIn('charger_id', array_unique($underStaffIds));
            } else {
                $query->where("id", "<", 0);
            }
        } elseif ($type == 'myteam') {
            $teamCus = CrmTeamList::query()->where([
                'user_id'   => getUserId(),
                'team_role' => CrmTeamList::ROLE_TWO
            ])->pluck('customer_id')->toArray();
            $query->whereIn('id', array_unique($teamCus))->where('charger_id', '!=', getUserId());
        } elseif ($type == 'underteam') {
            //下属职员ids
            $underStaffIds = array_unique(app(PositionStaff::class)->allUnderStaffIds(getUserId()));
            if (!empty($underStaffIds)) {
                $teamCus = CrmTeamList::query()->where('team_role', CrmTeamList::ROLE_TWO)
                    ->whereIn('user_id', $underStaffIds)->pluck('customer_id')->toArray();
                $query->whereIn('id', array_unique($teamCus));
            } else {
                $query->where("id", "<", 0);
            }
        }
        $cellData = [array('客户名称(手机号)', '客户类型', '客户来源', '客户号码', '所在地区', '负责人', '创建时间')];
        $cusTypeArr = CrmUser::CUS_TYPE_ARR;
        $cusSourceArr = CrmUser::CUS_SOURCE_ARR;
        $query->orderByRaw('FIELD(is_top,1,0),FIELD(is_mark,1,0),id desc,created_at desc')
            ->select('name', 'short_name', 'cus_type', 'cus_source', 'mobile', 'charger_id', 'created_at', 'province_name', 'city_name');
        $count = $query->count();
        $allUsers = allUsersArr();
        $query->chunk('1000', function ($customers) use (&$cellData, $cusTypeArr, $cusSourceArr, $allUsers) {
            foreach ($customers as $customer) {
                $cellData[] = [
                    $customer->name ? $customer->name : $customer->mobile,
                    isset($cusTypeArr[$customer->cus_type]) ? $cusTypeArr[$customer->cus_type] : '',
                    isset($cusSourceArr[$customer->cus_source]) ? $cusTypeArr[$customer->cus_source] : '',
                    $customer->mobile,
                    $customer->province_name . $customer->city_name,
                    isset($allUsers[$customer->charger_id]) ? $allUsers[$customer->charger_id]['name'] : '',
                    $customer->created_at
                ];
            }
        });

        foreach ($cellData as &$cell) {
            $cell = filterEmoji($cell);
        }
        try {
            ini_set('memory_limit', '500M');
            set_time_limit(0);//设置超时限制为0分钟
            app(Excel::class)->create('客户列表信息', function ($excel) use ($cellData, $count) {
                $excel->sheet('score', function ($sheet) use ($cellData, $count) {
                    $sheet->rows($cellData);
                    $sheet->cells("A1:G1", function ($cells) {
                        $cells->setAlignment('center');//字体水平居中
                        $cells->setFontWeight('bold');
                    });
                    $sheet->cells("A1:G" . ($count + 1), function ($cells) {
                        $cells->setAlignment('center');//字体水平居中
                    });
                    $sheet->setWidth(array(
                        'A' => 25,
                        'B' => 15,
                        'C' => 10,
                        'D' => 20,
                        'E' => 20,
                        'F' => 20,
                        'G' => 20
                    ));
                });
            })->export('xls');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}