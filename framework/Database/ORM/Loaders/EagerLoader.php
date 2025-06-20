<?php

namespace Framework\Database\ORM\Loaders;

use Framework\Database\ORM\Rel;

class EagerLoader extends AbstractLoader
{
    public function load(array $entities, array $relations): void
    {
        if (empty($entities) || empty($relations)) return;

        foreach ($relations as $relationPath) {
            $this->loadNestedRelation($entities, explode('.', $relationPath));
        }
    }

    protected function loadNestedRelation(array $entities, array $segments): void
    {
        if (empty($segments)) return;

        $relationName = array_shift($segments);
        $entity = $this->getFirstEntity($entities);


        if (!$entity) return;

        $relation = Rel::getRelation($entity, $relationName);
        if (!$relation) return;

        // 부모 관계 로딩
        $relation->addEagerConstraints($entities);
        $results = $relation->getEagerResults($entities);
        $relation->match($entities, $results, $relationName);
        

        // 하위 관계 재귀 로딩
        if (!empty($segments)) {
            $relatedEntities = [];

            foreach ($entities as $entity) {
                $related = $entity->getRelation($relationName);

                if (is_array($related)) {
                    $relatedEntities = array_merge($relatedEntities, $related);
                } elseif ($related !== null) {
                    $relatedEntities[] = $related;
                }
            }

            if (!empty($relatedEntities)) {
                $this->loadNestedRelation($relatedEntities, $segments);
            }
        }
    }
}



// <?php

// namespace Framework\Database\ORM\Loaders;

// use Framework\Database\ORM\Rel;

// class EagerLoader extends AbstractLoader
// {
//     /** @var array<string, bool> 중복 방지를 위한 캐시 */
//     protected array $visited = [];

//     public function load(array $entities, array $relations): void
//     {
//         if (empty($entities) || empty($relations)) return;

//         // Queue 기반 BFS
//         $queue = [];

//         foreach ($relations as $path) {
//             $queue[] = [
//                 'entities' => $entities,
//                 'segments' => explode('.', $path),
//                 'fullPath' => $path,
//             ];
//         }

//         while (!empty($queue)) {
//             $current     = array_shift($queue);
//             $entities    = $current['entities'];
//             $segments    = $current['segments'];
//             $fullPath    = $current['fullPath'];

//             if (empty($segments)) continue;

//             $relationName = array_shift($segments);
//             $first        = $this->getFirstEntity($entities);
//             if (!$first) continue;

//             // 🔐 각 path별로 고유한 키
//             $hash = $this->getRelationKey($entities, $fullPath);
//             if (isset($this->visited[$hash])) continue;
//             $this->visited[$hash] = true;

//             $relation = Rel::getRelation($first, $relationName);
//             if (!$relation) continue;

//             $relation->addEagerConstraints($entities);
//             $results = $relation->getEagerResults($entities);
//             $relation->match($entities, $results, $relationName);

//             // 다음 단계가 있다면 큐에 추가
//             if (!empty($segments)) {
//                 $nextEntities = [];

//                 foreach ($entities as $entity) {
//                     $related = $entity->getRelation($relationName);

//                     if (is_array($related)) {
//                         foreach ($related as $rel) {
//                             if ($rel !== null) {
//                                 $nextEntities[] = $rel;
//                             }
//                         }
//                     } elseif ($related !== null) {
//                         $nextEntities[] = $related;
//                     }
//                 }

//                 if (!empty($nextEntities)) {
//                     $queue[] = [
//                         'entities' => $nextEntities,
//                         'segments' => $segments,
//                         'fullPath' => $fullPath, // 전체 경로 유지
//                     ];
//                 }
//             }
//         }
//     }

//     /**
//      * 엔티티와 전체 관계 경로를 기반으로 고유 키를 생성
//      */
//     protected function getRelationKey(array $entities, string $relationPath): string
//     {
//         $ids = array_map(fn($e) => spl_object_hash($e), $entities);
//         sort($ids);
//         return md5($relationPath . ':' . implode(',', $ids));
//     }
// }
