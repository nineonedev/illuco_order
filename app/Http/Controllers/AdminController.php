<?php

namespace App\Http\Controllers;

use App\Domains\System\Repositories\FileAttachmentRepository;
use Framework\Routing\Controller;

class AdminController extends Controller
{

    public function home()
    {
        return $this->redirectRoute('auth.signin');
    }

    public function guide()
    {
        return $this->render('admin.pages.guide');
    }

    public function dashboard()
    {
        return $this->render('admin.pages.dashboard');
    }

    public function test()
    {
        return $this->render('admin.pages.test');
    }

    public function setting()
    {
        return $this->render('admin.pages.setting'); 
    }

    public function upload()
    {
        $file = FileAttachmentRepository::make()->uploadWithoutEntity('file');

        if (!$file) {
            return $this->renderError(null, '파일 업로드에 실패했습니다.');
        } 

        return $this->render(null, ['file'=> $file], '정상적으로 업로드되었습니다.');
    }
}