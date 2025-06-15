<?php

return [
    // CSRF & Auth
    'csrf_mismatch' => 'CSRF token mismatch.',
    'unauthenticated' => 'Authentication is required.',
    'unauthorized' => 'You do not have permission to perform this action.',
    'forbidden' => 'Access denied.',
    'authorization' => 'This action is unauthorized.',

    // HTTP/REST
    'bad_request' => 'Bad request.',
    'conflict' => 'A conflict has occurred.',
    'gone' => 'This resource is no longer available.',
    'internal_server_error' => 'An internal server error has occurred.',
    'method_not_allowed' => 'This HTTP method is not allowed.',
    'not_acceptable' => 'The request is not acceptable.',
    'not_found' => 'The requested resource was not found.',
    'not_implemented' => 'This feature is not implemented.',
    'service_unavailable' => 'The service is temporarily unavailable.',
    'too_many_requests' => 'Too many requests. Please try again later.',
    'unprocessable_entity' => 'The request could not be processed due to invalid input.',

    // Validation
    'success' => 'Operation completed successfully.',
    'fail' => 'Failed to process your request.',
    'validation_failed' => 'Validation failed.',
    'email_verification_required' => 'Email verification is required.',
    'email_verified' => 'Email has been verified.',
    'email_already_verified' => 'Email is already verified.',
    
    // Login/Register
    'already_logged_in' => 'You are already logged in.',
    'already_registered' => 'This account is already registered.',
    'auto_login_success' => 'You have been logged in automatically.',
    'login_success' => 'You have been logged in.',
    'login_failed' => 'Invalid email or password.',
    'register_failed' => 'Invalid email or password.',
    'register_success' => 'Registration completed successfully.',
    'logout_success' => 'Logged out successfully.',
    'password_incorrect' => 'Incorrect password.',
    'password_changed' => 'Password changed successfully.',
    'password_reset_sent' => 'Password reset link has been sent to your email.',
    'password_reset_success' => 'Password has been reset successfully.',
    'account_locked' => 'Your account is locked. Please contact the administrator.',
    'account_disabled' => 'Your account has been disabled.',

    // General system
    'server_error' => 'An internal server error has occurred.',
    'maintenance' => 'The service is currently under maintenance.',

    // Form/Resource
    'resource_created' => 'Successfully created.',
    'resource_updated' => 'Successfully updated.',
    'resource_deleted' => 'Successfully deleted.',
    'resource_restored' => 'Successfully restored.',

    // File/Upload
    'file_upload_failed' => 'File upload failed.',
    'file_type_not_allowed' => 'File type is not allowed.',
    'file_too_large' => 'File size is too large.',

    // Others
    'action_success' => 'The action was completed successfully.',
    'action_failed' => 'The action failed.',
    'already_exists' => 'This item already exists.',
    'does_not_exist' => 'This item does not exist.',
    'expired' => 'The request has expired.',
    'invalid_token' => 'Invalid token.',
];
