<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CostRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $total_cost = 0;
        foreach($value as $cost) {
            if ( $cost < 0 ) {
                return false;
            } else {
                if ($cost != null) {
                    $total_cost += $cost;
                }
            }
        }
        if ($total_cost != 100) {
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
        return 'The validation error message.';
    }
}
