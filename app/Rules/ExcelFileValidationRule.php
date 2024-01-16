<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExcelFileValidationRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $maxFileSize;
    private $size;

    public function __construct($size)
    {
        $this->maxFileSize = $size * 1024;
        $this->size = $size;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if the file has the correct extension (.xlsx)

        // $extension = $value->getClientOriginalExtension();
        // if ($extension !== 'xlsx') {
        //     return false;
        // }

        // Check if the file size is less than or equal to 10MB (10 * 1024 KB)
        // 10MB in kilobytes
        if (($value->getSize()/1024) > $this->maxFileSize) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a .xlsx file and should not exceed '.$this->size.'MB in size.';
    }
}
