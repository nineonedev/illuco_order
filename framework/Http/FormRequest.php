<?php 

namespace Framework\Http;

use Exception;
use Framework\Support\Exceptions\Http\UnauthorizedException;
use Framework\Support\Exceptions\ValidationException; 

abstract class FormRequest extends Request 
{
    public function __construct(
        array $get = [], 
        array $post = [], 
        array $cookies = [], 
        array $files = [],
        array $server = []
    )
    {
        parent::__construct($get, $post, $cookies, $files, $server);

        if (!$this->authorize()) {
            throw new UnauthorizedException();
        }

        $this->prepareForValidation(); // ✅ 전처리

        $this->ensureValidator($this->rules());


        foreach ($this->messages() as $field => $message) {
            $this->validator->addMessage($field, $message); 
        }

        
        if (!$this->validator->validate()) {
            $this->failedValidation();
            return; 
        }

        $this->afterValidation(); // ✅ 후처리
    }

    /**
     * 유효성 검사 규칙 정의
     */
    abstract protected function rules(): array;

    /**
     * 커스텀 에러 메시지 정의
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * 요청 허용 여부
     */
    protected function authorize(): bool
    {
        return true;
    }

    /**
     * 유효성 검사 전 전처리 로직 (예: 형변환, 병합 등)
     */
    protected function prepareForValidation(): void
    {
        // 자식 클래스에서 오버라이드
    }

    protected function failedValidationMessage(): string
    {
        return lang('validation.validation_failed');
    }

    /**
     * 유효성 검사 실패 시 커스텀 처리
     */
    protected function failedValidation(): void
    {
        throw new ValidationException(
            $this->failedValidationMessage(),
            $this->validator->getErrors()
        );
    }

    /**
     * 유효성 검사 통과 후 로직
     */
    protected function afterValidation(): void
    {
        // 자식 클래스에서 오버라이드
    }

    protected function setValidated(array $validated): void
    {
        $this->validator->setValidated($validated);
    }

    /**
     * Validator에 접근 (선택적 커스터마이징용)
     */
    public function withValidator(\Closure $callback): void
    {
        $callback($this->validator);
    }

    /**
     * @return static
     */
    public static function createFrom(Request $from, ?FormRequest $to = null)
    {
        $to = $to ?: new static(
            $from->get,
            $from->post,
            $from->cookies()->all(),
            $from->files,
            $from->http()->server(),
        ); 

        if ($from->route()) {
            $to->setRoute($from->route()); 
        }

        $to->data = $from->data;

        return $to; 
    }
}
