<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\Documents\CommercialInvoice;
use App\Domains\Order\Entities\Documents\PackingList;
use App\Domains\Order\Entities\Documents\ProductRequest;
use App\Domains\Order\Entities\Documents\ProformaInvoice;
use App\Domains\Order\Entities\OrderItem;
use App\Domains\Order\Repositories\OrderDocumentRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Exception;

use RuntimeException;

class OrderDocumentController extends Controller
{

    public function print(string $documentNo, Request $request)
    {
        $document = OrderDocumentRepository::make()
            ->query()
            ->with(['order' => ['customer', 'items.product.serials', 'user.dealer']])
            ->where('document_no', $documentNo)
            ->firstOrFail(); 

        $document->load([$document->type]);

        foreach ($document->order->items as $item) {
            $subType = $item->product->type; 

            if ($subType) {
                $item->product->load([$subType]);
            }
        }

        $orderItems = OrderItem::groupBySet($document->order->items);

        $document->order->forgetRelation('items');
        $document->order->setRelation('items', $orderItems);

        return $this->render('admin.pages.orders.print', [
            'document' => $document,
        ]);
    }

    public function edit(string $documentNo, Request $request)
    {
        $document = OrderDocumentRepository::make()
            ->query()
            ->with(['order' => ['customer', 'items.product.serials', 'user.dealer']])
            ->where('document_no', $documentNo)
            ->firstOrFail(); 

        $document->load([$document->type]);

        foreach ($document->order->items as $item) {
            $subType = $item->product->type; 

            if ($subType) {
                $item->product->load([$subType]);
            }
        }

        $orderItems = OrderItem::groupBySet($document->order->items);

        $document->order->forgetRelation('items');
        $document->order->setRelation('items', $orderItems);

        return $this->render('admin.pages.orders.document', [
            'document' => $document,
        ]);
    }

    public function update(int $id, Request $request)
    {
        return $this->runInTransaction(function() use ($id, $request) {
            $document = OrderDocumentRepository::make()->findOrFail($id);
            $document->load([$document->type]);
            $subClass = null;

            switch ($document->type) {
                case ProformaInvoice::alias(): 
                    $subClass = ProformaInvoice::class;
                    break; 
                case ProductRequest::alias(): 
                    $subClass = ProductRequest::class;
                    break; 
                case PackingList::alias(): 
                    $subClass = PackingList::class;
                    break; 
                case CommercialInvoice::alias(): 
                    $subClass = CommercialInvoice::class;
                    break; 
            }

            if (!$subClass) {
                throw new RuntimeException("확장된 문서를 찾을 수 없습니다.");
            }

            $repo = $subClass::repositoryClass()::make(); 
            $docData = array_merge($request->all(), [
                'id' => $document->{$document->type}->id,
            ]);
            $subDocument = $repo->save(new $subClass($docData)); 
            
            if (!$subDocument){ 
                throw new Exception("확장된 문서 수정에 실패하였습니다.");
            }

            return $this->render(null, [
                'document' => $subDocument->toArray(), 
            ], '성공적으로 수정되었습니다.');
        });
    }
}