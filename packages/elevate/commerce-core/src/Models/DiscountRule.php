<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountRule extends Model
{
    protected $fillable = [
        'discount_id',
        'type',
        'operator',
        'value',
    ];

    protected $casts = [
        'discount_id' => 'integer',
        'value' => 'json',
    ];

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Check if this rule passes for given context
     */
    public function passes(array $context): bool
    {
        $contextValue = $context[$this->type] ?? null;
        
        if ($contextValue === null) {
            return false;
        }

        return $this->evaluateCondition($contextValue, $this->operator, $this->value);
    }

    protected function evaluateCondition($contextValue, $operator, $ruleValue): bool
    {
        switch ($operator) {
            case 'equals':
                return $contextValue == $ruleValue;
                
            case 'not_equals':
                return $contextValue != $ruleValue;
                
            case 'in':
                return in_array($contextValue, (array) $ruleValue);
                
            case 'not_in':
                return !in_array($contextValue, (array) $ruleValue);
                
            case 'greater_than':
                return $contextValue > $ruleValue;
                
            case 'greater_than_or_equal':
                return $contextValue >= $ruleValue;
                
            case 'less_than':
                return $contextValue < $ruleValue;
                
            case 'less_than_or_equal':
                return $contextValue <= $ruleValue;
                
            case 'contains':
                if (is_array($contextValue)) {
                    return !empty(array_intersect((array) $contextValue, (array) $ruleValue));
                }
                return in_array($contextValue, (array) $ruleValue);
                
            case 'not_contains':
                if (is_array($contextValue)) {
                    return empty(array_intersect((array) $contextValue, (array) $ruleValue));
                }
                return !in_array($contextValue, (array) $ruleValue);
                
            default:
                return false;
        }
    }

    /**
     * Get available rule types
     */
    public static function getAvailableTypes(): array
    {
        return [
            'customer_id' => 'Specific Customer',
            'customer_group_id' => 'Customer Group',
            'customer_email' => 'Customer Email',
            'customer_affiliation' => 'Customer Affiliation',
            'event_type_id' => 'Event Type',
            'product_id' => 'Specific Product',
            'product_type' => 'Product Type',
            'product_tags' => 'Product Tags',
            'category_id' => 'Product Category',
            'total_quantity' => 'Total Quantity',
            'total_amount' => 'Total Amount',
            'order_count' => 'Customer Order Count',
            'first_order' => 'First Order',
        ];
    }

    /**
     * Get available operators for a rule type
     */
    public static function getAvailableOperators(string $type): array
    {
        $numericOperators = [
            'equals' => 'Equals',
            'not_equals' => 'Not Equals',
            'greater_than' => 'Greater Than',
            'greater_than_or_equal' => 'Greater Than or Equal',
            'less_than' => 'Less Than',
            'less_than_or_equal' => 'Less Than or Equal',
        ];

        $arrayOperators = [
            'in' => 'Is One Of',
            'not_in' => 'Is Not One Of',
            'contains' => 'Contains',
            'not_contains' => 'Does Not Contain',
        ];

        $stringOperators = [
            'equals' => 'Equals',
            'not_equals' => 'Not Equals',
            'in' => 'Is One Of',
            'not_in' => 'Is Not One Of',
        ];

        $booleanOperators = [
            'equals' => 'Is',
        ];

        switch ($type) {
            case 'total_quantity':
            case 'total_amount':
            case 'order_count':
                return $numericOperators;
                
            case 'product_tags':
            case 'category_id':
                return $arrayOperators;
                
            case 'customer_email':
                return $stringOperators;
                
            case 'customer_affiliation':
            case 'event_type_id':
                return [
                    'equals' => 'Equals',
                    'not_equals' => 'Not Equals',
                    'in' => 'Is One Of',
                    'not_in' => 'Is Not One Of',
                ];
                
            case 'first_order':
                return $booleanOperators;
                
            default:
                return $stringOperators;
        }
    }
}
