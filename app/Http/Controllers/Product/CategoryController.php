<?php 

namespace App\Http\Controllers\Product;

use App\Domains\Product\Entities\Category;
use App\Domains\Product\Repositories\CategoryRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class CategoryController extends Controller 
{
    public function index()
    {
        $categories = CategoryRepository::make()
            ->query()
            ->orderByAsc('sort_order')
            ->get();
        
        return $this->render('admin.pages.products.category', [
            'categories'=> $categories,
        ]);
    }

    public function store(Request $request)
    {
        return $this->runInTransaction(function () use ($request) {
            $request->validateOrFail([
                'label' => 'required', 
                'sort_order' => 'nullable|integer',
                // 'slug' => 'required|minLength:4|unique:product_categories,slug',
            ]);

            $data = $request->safe(['label', 'sort_order']);

            $category = new Category($data);
            $category = CategoryRepository::make()->save($category);

            if (!$category) {
                throw new RuntimeException("카테고리 생성에 실패하였습니다.");
            }

            return $this->render(null, [
                'category' => $category->toArray(),
            ], '카테고리 생성에 성공하였습니다.');
        });
        
    }

    public function destroy(int $id)
    {
        return $this->runInTransaction(function () use ($id){
            $category = CategoryRepository::make()->findOrFail($id);
            $sort_order = $category->sort_order; 

            if ($category->is_locked) {
                throw new RuntimeException("시스템 예약 카테고리는 삭제할 수 없습니다.");
            }
    
            $deleted = CategoryRepository::make()->delete($category);

            if (!$deleted) {
                throw new RuntimeException("삭제 중 문제가 발생하였습니다.");
            }

            foreach (CategoryRepository::make()->all() as $category) {
                if ($category->sort_order < $sort_order) {
                    continue;
                }

                $category->fill(['sort_order' => $category->sort_order - 1]);
                CategoryRepository::make()->save($category);
            }

            return $this->render(null, [], '정상적으로 삭제되었습니다.');
        });
    }
    
    public function update(int $id, Request $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            $request->validateOrFail([
                'label' => 'required',
                // 'slug' => 'required|minLength:4|unique:product_categories,slug,' . $id, // 현재 ID 제외
                'sort_order' => 'nullable|integer',
            ]);

            $data = $request->safe(['label', 'sort_order']);

            $repo = CategoryRepository::make();
            $category = $repo->findOrFail($id);
            $category->fill([
                'label' => $data['label'],
                'sort_order' => $data['sort_order'],
            ]);

            $repo->save($category);

            return $this->render(null, [
                'category' => $category->toArray(),
            ], '카테고리 수정에 성공하였습니다.');
        });
    }

}