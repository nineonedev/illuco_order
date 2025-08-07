<?php

return [
    'unexpected' => '알 수 없는 오류가 발생했습니다. 잠시 후 다시 시도해주세요.',

    // CSRF & Auth
    'csrf_mismatch' => '세션이 만료되었습니다. 페이지를 새로고침한 뒤 다시 시도해주세요.',
    'unauthenticated' => '로그인이 필요합니다.',
    'unauthorized' => '이 작업을 수행할 권한이 없습니다.',
    'forbidden' => '접근이 거부되었습니다.',
    'authorization' => '해당 작업을 수행할 수 없습니다.',

    // HTTP/REST
    'bad_request' => '잘못된 요청입니다.',
    'conflict' => '충돌이 발생했습니다.',
    'gone' => '해당 리소스를 더 이상 사용할 수 없습니다.',
    'internal_server_error' => '서버 내부 오류가 발생했습니다.',
    'method_not_allowed' => '허용되지 않은 HTTP 메서드입니다.',
    'not_acceptable' => '요청이 허용되지 않습니다.',
    'not_found' => '요청하신 리소스를 찾을 수 없습니다.',
    'not_implemented' => '아직 지원되지 않는 기능입니다.',
    'service_unavailable' => '현재 서비스 이용이 불가능합니다.',
    'too_many_requests' => '요청이 너무 많습니다. 잠시 후 다시 시도해 주세요.',
    'unprocessable_entity' => '잘못된 입력값으로 요청을 처리할 수 없습니다.',

    // Validation
    'success' => '성공적으로 처리되었습니다.',
    'fail' => '요청 처리에 실패했습니다.',
    'validation_failed' => '입력값이 올바르지 않습니다.',
    'email_verification_required' => '이메일 인증이 필요합니다.',
    'email_verified' => '이메일 인증이 완료되었습니다.',
    'email_already_verified' => '이미 인증된 이메일입니다.',
    
    // Login/Register
    'already_logged_in' => '이미 로그인되어 있습니다.',
    'already_registered' => '이미 가입된 계정입니다.',
    'auto_login_success' => '자동 로그인되었습니다.',
    'login_success' => '로그인되었습니다.',
    'login_failed' => '이메일 또는 비밀번호가 올바르지 않습니다.',
    'register_failed' => '이메일 또는 비밀번호가 올바르지 않습니다.',
    'register_success' => '회원가입이 완료되었습니다.',
    'logout_success' => '정상적으로 로그아웃 되었습니다.',
    'password_incorrect' => '비밀번호가 올바르지 않습니다.',
    'password_changed' => '비밀번호가 성공적으로 변경되었습니다.',
    'password_reset_sent' => '비밀번호 재설정 링크가 이메일로 발송되었습니다.',
    'password_reset_success' => '비밀번호가 성공적으로 재설정되었습니다.',
    'account_locked' => '계정이 잠겼습니다. 관리자에게 문의하세요.',
    'account_disabled' => '계정이 비활성화되었습니다.',

    // General system
    'server_error' => '서버 내부 오류가 발생했습니다.',
    'maintenance' => '현재 서비스 점검 중입니다.',

    // Form/Resource
    'resource_created' => '정상적으로 등록되었습니다.',
    'resource_updated' => '정상적으로 수정되었습니다.',
    'resource_deleted' => '정상적으로 삭제되었습니다.',
    'resource_restored' => '정상적으로 복구되었습니다.',

    // File/Upload
    'file_upload_failed' => '파일 업로드에 실패했습니다.',
    'file_type_not_allowed' => '허용되지 않는 파일 형식입니다.',
    'file_too_large' => '파일 용량이 너무 큽니다.',

    // Others
    'action_success' => '작업이 성공적으로 완료되었습니다.',
    'action_failed' => '작업에 실패했습니다.',
    'already_exists' => '이미 존재하는 항목입니다.',
    'does_not_exist' => '존재하지 않는 항목입니다.',
    'expired' => '만료된 요청입니다.',
    'invalid_token' => '유효하지 않은 토큰입니다.',
];
