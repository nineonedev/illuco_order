<?php

namespace App\Http\Controllers;

use App\Domains\Communication\Entities\Notice;
use App\Domains\Communication\Repositories\NoticeRepository;
use App\Domains\System\Repositories\FileAttachmentRepository;
use Exception;
use Framework\Http\Request;
use Framework\Routing\Controller;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        return $this->render('admin.pages.notices.index');
    }


    public function show(string $id)
    {
        return $this->render('admin.pages.notices.show', ['id'=> $id]);
    }

    public function create()
    {
        return $this->render('admin.pages.notices.create');
    }

    
    public function edit()
    {
        return $this->render('admin.pages.notices.edit');
    }

    public function store(Request $request)
    {

        $request->validateOrFail([
            'title' => 'required',
        ]); 

        $data = $request->all();
        $notice = new Notice($data);
        $notice->user_id = guard()->id();

        $notice = NoticeRepository::make()->save($notice);

        if (!$notice) {
            throw new Exception("공지 생성에 실패했습니다.");
        }

        // $fileRepo = new FileAttachmentRepository();
        // $fileRepo->uploadMany($notice); 
        

        return $this->render(null, ['notice' => $notice]);
    }


    public function update()
    {
        
    }
    
    public function destroy()
    {

    }
}