<?php

use App\Domains\Communication\Enums\ClaimStatus;
use App\Domains\Order\Entities\Customer;
use App\Domains\Order\Enums\OrderStatus;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\User\Entities\Dealer;

return [
    // 역할
    'role' => [
        'employee' => '직원',
        'dealer' => '대리점',
        'claim' => '클레임',
        'notice' => '공지사항',
        'customer' => '고객',
        'product' => '제품',
        'order' => '주문',
        'cart' => '장바구니',
        'role' => '권한',
        'category' => '카테고리',
    ],

    // 권한
    'action' => [
        'create' => '생성',
        'read'   => '조회',
        'update' => '수정',
        'delete' => '삭제',
    ],
    
    // 라벨
    'email' => '이메일',

    'order' => [
        'status' => [
            OrderStatus::CANCELED   => '주문 취소',
            OrderStatus::NEW   => '주문 접수됨',
            OrderStatus::CONFIRMED  => '주문 확인됨',
            OrderStatus::PREPARING  => '상품 준비 중',
            OrderStatus::SHIPPED    => '출고 완료',
            OrderStatus::COMPLETED    => '주문 완료',
            OrderStatus::REJECTED    => '주문 거절됨',
        ]
    ],

    'claim' => [
        'status' => [
            ClaimStatus::RECEIVED   => '접수됨',
            ClaimStatus::PROCESSING   => '처리중',
            ClaimStatus::COMPLETED    => '처리 완료',
        ]
    ],

    'countries' => [
        'US' => '미국',
        'KR' => '대한민국',
        'JP' => '일본',
        'CN' => '중국',
        'FR' => '프랑스',
        'DE' => '독일',
        'ES' => '스페인',
        'RU' => '러시아',
        'IN' => '인도',
        'GB' => '영국',
        'IT' => '이탈리아',
        'BR' => '브라질',
        'CA' => '캐나다',
        'AU' => '호주',
        'MX' => '멕시코',
        'NL' => '네덜란드',
        'TR' => '터키',
        'ID' => '인도네시아',
        'SA' => '사우디아라비아',
        'AR' => '아르헨티나',
        'TH' => '태국',
        'VN' => '베트남',
        'PH' => '필리핀',
        'PL' => '폴란드',
        'SE' => '스웨덴',
        'CH' => '스위스',
        'BE' => '벨기에',
        'NO' => '노르웨이',
        'FI' => '핀란드',
        'DK' => '덴마크',
        'UA' => '우크라이나',
        'ZA' => '남아프리카 공화국',
        'EG' => '이집트',
        'NG' => '나이지리아',
        'KE' => '케냐',
        'NZ' => '뉴질랜드',
        'MY' => '말레이시아',
        'SG' => '싱가포르',
        'IL' => '이스라엘',
        'IR' => '이란',
        'GR' => '그리스',
        'PT' => '포르투갈',
        'AT' => '오스트리아',
        'CZ' => '체코',
        'HU' => '헝가리',
        'RO' => '루마니아',
        'BG' => '불가리아',
        'SK' => '슬로바키아',
        'HR' => '크로아티아',
        'SI' => '슬로베니아',
        'RS' => '세르비아',
        'TW' => '대만',
        'AE' => '아랍에미리트',
        'PK' => '파키스탄',
        'MA' => '모로코',
        'HK' => '홍콩',
        'IQ' => '이라크',
        'AM' => '아르메니아',
    ],

    'headlight' => [
        'type' => '헤드라이트 종류',
        'wireless_color' => '색상',
        'engraving_text' => '각인',
    ],
    'loupe' => [
        'type'              => '루페 종류',
        'frame_type'        => '프레임 종류',
        'working_distance'  => '작업 거리 (mm)',
        'engraving_text' => '각인',

        'od_sph'            => '우안 구면도수 (OD SPH)',
        'os_sph'            => '좌안 구면도수 (OS SPH)',

        'od_cyl'            => '우안 난시도수 (OD CYL)',
        'os_cyl'            => '좌안 난시도수 (OS CYL)',

        'od_axis'           => '우안 축 (OD Axis)',
        'os_axis'           => '좌안 축 (OS Axis)',

        'od_add'            => '우안 추가도수 (OD Add)',
        'os_add'            => '좌안 추가도수 (OS Add)',

        'pd_right'          => '우안 동공거리 (PD Right)',
        'pd_left'           => '좌안 동공거리 (PD Left)',
        'pd_total'          => '총 동공거리 (PD Total)',
        'vertex_distance'   => '정점거리 (mm)',
        'add_option'        => '추가 옵션',
    ]
];