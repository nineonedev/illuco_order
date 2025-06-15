<?php

namespace Framework\Validation;

use Framework\Validation\Rules\Archive;
use Framework\Validation\Rules\ArrayRule;
use Framework\Validation\Rules\Audio;
use Framework\Validation\Rules\Date;
use Framework\Validation\Rules\DateISO;
use Framework\Validation\Rules\Digit;
use Framework\Validation\Rules\Document;
use Framework\Validation\Rules\Email;
use Framework\Validation\Rules\Equal;
use Framework\Validation\Rules\FileExtension;
use Framework\Validation\Rules\FileSize;
use Framework\Validation\Rules\Image;
use Framework\Validation\Rules\Max;
use Framework\Validation\Rules\MaxLength;
use Framework\Validation\Rules\MimeType;
use Framework\Validation\Rules\Min;
use Framework\Validation\Rules\MinLength;
use Framework\Validation\Rules\Number;
use Framework\Validation\Rules\Pattern;
use Framework\Validation\Rules\Phone;
use Framework\Validation\Rules\PhoneUS;
use Framework\Validation\Rules\Required;
use Framework\Validation\Rules\Unique;
use Framework\Validation\Rules\Uploaded;
use Framework\Validation\Rules\UploadedOk;
use Framework\Validation\Rules\Step;
use Framework\Validation\Rules\NotEqual; 
use Framework\Validation\Rules\Rule;
use Framework\Validation\Rules\StringRule;
use Framework\Validation\Rules\Video;

class RuleFactory
{
    /**
     * Create a rule instance based on the rule name.
     *
     * @param string $rule
     * @param array $parameters
     * @return Rule
     * @throws \RuntimeException
     */
    public static function create(string $rule, array $parameters = []): Rule
    {
        // Map the rule string to the appropriate rule class
        switch ($rule) {
            case 'array':
                return new ArrayRule();
            case 'string': 
                return new StringRule();
            case 'date':
                return new Date();
            case 'dateISO':
                return new DateISO();
            case 'digit':
                return new Digit();
            case 'email':
                return new Email();
            case 'equal':
                return new Equal(...$parameters);
            case 'fileExtension':
                return new FileExtension(...$parameters);
            case 'fileSize':
                return new FileSize(...$parameters);
            case 'max':
                return new Max(...$parameters);
            case 'maxLength':
                return new MaxLength(...$parameters);
            case 'min':
                return new Min(...$parameters);
            case 'minLength':
                return new MinLength(...$parameters);
            case 'number':
                return new Number();
            case 'pattern':
                return new Pattern(...$parameters);
            case 'phone':
                return new Phone();
            case 'phoneUS':
                return new PhoneUS();
            case 'required':
                return new Required();
            case 'unique':
                return new Unique(...$parameters);
            case 'uploaded':
                return new Uploaded();
            case 'uploadedOk':
                return new UploadedOk();
            case 'mimeType':
                return new MimeType(...$parameters);
            case 'image':
                return new Image();
            case 'video':
                return new Video();
            case 'audio':
                return new Audio();
            case 'document':
                return new Document();
            case 'archive':
                return new Archive();
            case 'step':
                return new Step(...$parameters);
            case 'notEqual':
                return new NotEqual(...$parameters);
            default:
                throw new \RuntimeException("Validation rule [{$rule}] is not defined.");
        }
    }
}
