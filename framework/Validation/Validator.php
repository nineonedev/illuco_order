<?php

namespace Framework\Validation;

use Framework\Support\Exceptions\ValidationException;
use Framework\Validation\Rules\Nullable;

class Validator
{
    protected array $data = [];
    protected array $rules = [];
    protected array $messages = [];
    protected array $errors = [];
    protected array $validatedData = [];

    // Validator 클래스 내부에 추가/교체

    /** @var array<string,string> */
    protected array $labels = [
        // 공통/메타
        'id' => 'ID',
        'code' => '코드',
        'label' => '라벨',
        'name' => '이름',
        'description' => '설명',
        'status' => '상태',
        'previous_status' => '변경 전 상태',
        'message' => '메시지/사유',
        'memo' => '메모',
        'note' => '메모',
        'created_at' => '생성일시',
        'updated_at' => '수정일시',
        'deleted_at' => '삭제일시',
        'created_by' => '작성자',
        'updated_by' => '수정자',
        'is_active' => '활성화',
        'enabled' => '사용 여부',
        'visible' => '노출 여부',
        'sort_order' => '정렬 순서',
        'date' => '날짜',
        'time' => '시간',
        'year' => '연도',
        'month' => '월',
        'day' => '일',

        // 인증/계정
        'email' => '이메일',
        'password' => '비밀번호',
        'password_confirmation' => '비밀번호 확인',
        'username' => '사용자명',
        'user_id' => '사용자',
        'role' => '역할',
        'role_id' => '역할',
        'permission_id' => '권한',
        'remember_token' => '로그인 유지 토큰',
        'token' => '토큰',
        'expires_at' => '만료일시',
        'used_at' => '사용일시',

        // 대리점/고객
        'dealer_id' => '대리점',
        'dealer_code' => '대리점 코드',
        'dealer_name' => '대리점명',
        'customer_id' => '고객',
        'customer_code' => '고객 코드',
        'customer_name' => '고객명',
        'company' => '회사명',
        'contact_name' => '담당자명',
        'contact_email' => '담당자 이메일',
        'contact_phone' => '담당자 연락처',
        'phone' => '전화번호',
        'mobile' => '휴대전화',
        'country' => '국가',
        'country_code' => '국가코드',
        'timezone' => '시간대',
        'address_line1' => '주소',
        'address_line2' => '상세 주소',
        'city' => '도시',
        'state' => '주/도',
        'postal_code' => '우편번호',

        // 제품/모델
        'product_id' => '제품',
        'product_code' => '제품 코드',
        'product_name' => '제품명',
        'model_no' => '모델 번호',
        'model_number' => '모델 번호',
        'sku' => 'SKU',
        'unit_price' => '단가',
        'price' => '가격',
        'currency' => '통화',
        'quantity' => '수량',
        'amount' => '금액',
        'subtotal' => '소계',
        'tax' => '세금',
        'discount' => '할인',
        'total_amount' => '합계',

        // 시력/옵션(루페·헤드라이트)
        'engraving_text' => '각인 정보',
        'engraving_requested' => '각인 요청',
        'wd' => '작업거리(WD, cm)',
        'frame' => '테 정보',
        'frame_type' => '테 종류',
        'frame_code' => '테 코드',
        'far_pd_right' => '원거리 PD(우, mm)',
        'far_pd_left' => '원거리 PD(좌, mm)',
        'near_pd_right' => '근거리 PD(우, mm)',
        'near_pd_left' => '근거리 PD(좌, mm)',
        'sph_right' => 'SPH(우)',
        'sph_left'  => 'SPH(좌)',
        'cyl_right' => 'CYL(우)',
        'cyl_left'  => 'CYL(좌)',
        'axis_right' => 'AXIS(우)',
        'axis_left'  => 'AXIS(좌)',
        'add_right'  => 'ADD(우)',
        'add_left'   => 'ADD(좌)',
        'option1' => '옵션 1',
        'option2' => '옵션 2',
        'option3' => '옵션 3',

        // 주문/장바구니
        'cart_id' => '장바구니',
        'order_id' => '주문',
        'order_no' => '주문번호',
        'order_number' => '주문번호',
        'order_date' => '주문일자',
        'order_status' => '주문 상태',
        'confirmed_at' => '확정일시',
        'confirmed_by' => '확정자',
        'canceled_at' => '취소일시',
        'canceled_by' => '취소자',

        // 출하/배송
        'shipped_at' => '출하일시',
        'tracking_no' => '운송장 번호',
        'tracking_number' => '운송장 번호',
        'courier' => '배송사',
        'delivery_address_id' => '배송지',
        'shipping_memo' => '배송 메모',

        // 문서/인보이스
        'proforma_no' => 'PI 문서번호',
        'invoice_no' => '인보이스 번호',
        'packing_list_no' => '패킹리스트 번호',
        'order_document_id' => '주문 문서',
        'document_type' => '문서 유형',
        'document_file' => '문서 파일',

        // 포장/박스
        'box_no' => '박스 번호',
        'box_weight' => '박스 무게(kg)',
        'box_length' => '박스 가로(cm)',
        'box_width' => '박스 세로(cm)',
        'box_height' => '박스 높이(cm)',
        'box_size' => '박스 사이즈',
        'box_count' => '박스 수',

        // 시리얼/클레임
        'serial_no' => '시리얼 번호',
        'serial_number' => '시리얼 번호',
        'defect_description' => '불량 현상(영문)',
        'defect_description_ko' => '불량 현상(국문)',
        'claim_id' => '클레임',
        'claim_status' => '클레임 상태',
        'processed_at' => '처리일시',
        'processed_by' => '처리 담당자',
        'resolution' => '조치 내용',
        'result' => '처리 결과',

        // 파일 업로드
        'file_id' => '파일',
        'file_name' => '파일명',
        'file_path' => '파일 경로',
        'mime_type' => '파일 형식',
        'size' => '파일 크기',
        'uploaded_at' => '업로드일시',
        'uploaded_by' => '업로더',
    ];

    /** 라벨 일괄 등록 */
    public function setLabels(array $labels): self
    {
        $this->labels = array_merge($this->labels, $labels);
        return $this;
    }

    /** 단일 라벨 설정 */
    public function setLabel(string $field, string $label): self
    {
        $this->labels[$field] = $label;
        return $this;
    }

    /** 라벨 조회 */
    public function getLabel(string $field): string
    {
        return $this->labels[$field] ?? $field;
    }


    public function __construct(array $data = [], array $rules = [])
    {
        $this->data = $data;
        $this->addRules($rules);
    }

    /**
     * 단일 필드 규칙 추가
     */
    public function addRule(string $field, $rules): self
    {
        if (is_string($rules)) {
            $rules = $this->parseRules($rules);
        }
        $this->rules[$field] = $rules;
        return $this;
    }

    /**
     * 다중 규칙 추가
     */
    public function addRules(array $rules): void
    {
        foreach ($rules as $field => $ruleSet) {
            $this->addRule($field, $ruleSet);
        }
    }

    /**
     * 커스텀 에러 메시지 등록(필드 단위)
     */
    public function addMessage(string $field, string $message): self
    {
        $this->messages[$field] = $message;
        return $this;
    }

    /**
     * 라벨(표시명) 등록
     */
    public function label(string $field, string $label): self
    {
        $this->labels[$field] = $label;
        return $this;
    }

    /**
     * 라벨 맵 일괄 등록
     */
    public function labels(array $map): self
    {
        foreach ($map as $f => $l) {
            $this->label($f, (string)$l);
        }
        return $this;
    }

    protected function labelOf(string $field): string
    {
        return $this->labels[$field] ?? $field;
    }

    /**
     * 유효성 검사 수행 (재호출 안전)
     */
    public function validate(): bool
    {
        // 재호출 안전
        $this->errors = [];
        $this->validatedData = [];

        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                $value = $this->data[$field] ?? null;

                // ['required','email'] 형태도 허용
                if (is_string($rule)) {
                    $rule = ['name' => $rule, 'params' => []];
                }

                $ruleInstance = RuleFactory::create($rule['name'], $rule['params']);
                $ruleInstance->setField($field);

                // Nullable 규칙: 값이 null/'' 등 허용이면 나머지 규칙 스킵
                if ($ruleInstance instanceof Nullable) {
                    if ($ruleInstance->passes($value)) {
                        continue 2;
                    }
                    continue;
                }

                if (!$ruleInstance->passes($value)) {
                    $rawMsg = $this->messages[$field] ?? $ruleInstance->message();
                    $this->errors[$field][] = $this->formatMessage($field, $rawMsg);

                    // 필드당 첫 에러만 수집하려면 아래 주석 해제
                    // break;
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * 메시지에 라벨 반영 및 플레이스홀더 치환
     */
    protected function formatMessage(string $field, string $message): string
    {
        $label = $this->labelOf($field);

        // 플레이스홀더 지원
        $message = strtr($message, [
            ':attribute' => $label,
            ':label'     => $label,
            ':field'     => $field,
        ]);

        // 이미 라벨이 앞에 없으면 접두
        if (strpos($message, $label . ':') !== 0 && strpos($message, $label . ' :') !== 0) {
            $message = $label . ': ' . $message;
        }
        return $message;
    }

    public function setValidated(array $validated): void
    {
        $this->validatedData = array_merge($this->validated(), $validated);
    }

    /**
     * 통과한 데이터만 반환(에러난 필드 제외)
     */
    public function validated(): array
    {
        return $this->validatedData ?: array_filter(
            $this->data,
            fn ($key) => !array_key_exists($key, $this->errors),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * 실패 시 첫 에러 메시지만 담아 예외 던지기
     */
    public function validateOrFail(): bool
    {
        if (!$this->validate()) {
            $message = $this->firstErrorMessage() ?? '유효성 검사에 실패하였습니다.';
            throw new ValidationException($message, $this->errors);
        }
        return true;
    }

    protected function firstErrorMessage(): ?string
    {
        if (empty($this->errors)) return null;
        $firstField = array_key_first($this->errors);
        $messages   = $this->errors[$firstField] ?? [];
        return is_array($messages) ? (string)reset($messages) : (string)$messages;
    }

    protected function firstErrorField(): ?string
    {
        return empty($this->errors) ? null : array_key_first($this->errors);
    }

    /**
     * 상태 초기화
     */
    public function flush(): void
    {
        $this->errors = [];
        $this->messages = [];
        $this->rules = [];
        $this->validatedData = [];
        // $this->labels 는 보통 인스턴스 생애동안 유지 (원하면 여기도 비워도 됨)
    }

    public function fails(): bool
    {
        return !$this->validate();
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function parseRules(string $rules): array
    {
        $parsedRules = [];
        $rulesArray = explode('|', $rules);

        foreach ($rulesArray as $rule) {
            $parts  = explode(':', $rule, 2);
            $name   = $parts[0];
            $params = isset($parts[1]) ? explode(',', $parts[1]) : [];
            $parsedRules[] = ['name' => $name, 'params' => $params];
        }
        return $parsedRules;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $this->formatMessage($field, $message);
    }

    public static function make(array $data, array $rules): Validator
    {
        return new self($data, $rules);
    }
}
