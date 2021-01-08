<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/9/11 17:51
 */

namespace Modules\Manage\Http\Controllers\Crm;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Services\UploadService;

class UploadController extends Controller
{
    protected $service;

    public function __construct(UploadService $service)
    {
        $this->service = $service;
    }

    public function uploadImage(Request $request)
    {
        $this->validate($request, ['file' => 'required|file']);
        $table = $request->get('table', 'crm_plan_records');
        return $this->service->upload($request, config("upload.attachment_url"), $table);
    }
}