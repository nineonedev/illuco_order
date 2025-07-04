<?php

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Entities\Documents\CommercialInvoice;
use App\Domains\Order\Entities\Documents\PackingList;
use App\Domains\Order\Entities\Documents\ProductRequest;
use App\Domains\Order\Entities\Documents\ProformaInvoice;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Entities\OrderDocument;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Validation\Validator;
use RuntimeException;

class OrderDocumentRepository extends Repository
{
    public static function table(): string
    {
        return 'order_documents';
    }

    public static function entityClass(): string
    {
        return OrderDocument::class;
    }

    public function createDocuments(Order $order): array
    {
        $data = [
           'order_id' => $order->id, 
           'user_id' => user()->id,
        ];

        $rules = [
            'order_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]; 

        $validator = Validator::make($data, $rules);
        $validator->validateOrFail();

        $documentClasses = [
            ProformaInvoice::class,
            ProductRequest::class,
            PackingList::class,
            CommercialInvoice::class,
        ];

        $documents = [];

        foreach ($documentClasses as $class) {
            $documentAttributes = $validator->validated(); 
            $documentAttributes['type'] = $class::alias(); 
            
            $orderDocument = new OrderDocument((array) $documentAttributes);
            $orderDocument->generateDocumentNumber(
                $class::code(),
                user()->isDealer() ? user()->dealer->code : null,
            );

            $orderDocument = static::make()->save($orderDocument);

            if (!$orderDocument) {
                throw new RuntimeException("초기 문서 생성에 실패하였습니다.");
            }

            $subDocument = new $class(['id' => $orderDocument->id]);
            $subDocument = $class::repositoryClass()::make()->save($subDocument);

            if (!$subDocument) {
                throw new RuntimeException("초기 문서 확장에 실패하였습니다.");
            }

            $documents[$class::code()] = $subDocument;
        }

        return $documents;
        
    }
}
