<?php

namespace App\Http\Controllers\Communication;

use App\Domains\Communication\Entities\SalesInfo;
use App\Domains\Communication\Repositories\SalesInfoRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;

class SalesInfoController extends Controller
{
    public function index()
    {
        $info = SalesInfoRepository::make()->query()->first();

        return $this->render('admin.pages.salesinfo.index', [
            'info' => $info ?? SalesInfo::make(),
        ]);
    }

    public function save(Request $request)
    {
        return $this->runInTransaction(function() use ($request) {
                
            $body = $request->all();
            $info = SalesInfo::make($body); 
            
            $info = SalesInfoRepository::make()->save($info);

            return $this->render(null, ['info' => $info->toArray()], '정상적으로 생성되었습니다.');
        });
    }

}
