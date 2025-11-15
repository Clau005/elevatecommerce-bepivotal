<?php

namespace App\Models;

use ElevateCommerce\Core\Models\Customer;

class User extends Customer
{
    /**
     * Add any custom user logic here.
     * 
     * This model extends the Customer model from the ElevateCommerce Core package,
     * which provides all the base customer functionality including authentication,
     * customer fields (title, first_name, last_name, company_name, etc.),
     * and the name accessor.
     */
}
