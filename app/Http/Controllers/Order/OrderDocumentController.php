<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\OrderItem;
use App\Domains\Order\Repositories\OrderDocumentRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;

class OrderDocumentController extends Controller
{
    public function show()
    {

    }

    public function edit(string $documentNo, Request $request)
    {
        $document = OrderDocumentRepository::make()
            ->query()
            ->with(['order.items.product'])
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

    public function update()
    {

    }

    public function download(string $documentNo)
    {

    }
}