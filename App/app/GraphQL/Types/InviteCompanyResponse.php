<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InviteCompanyResponse extends GraphQLType
{
    protected $attributes = [
        'name' => 'CustomResponse',
        'description' => 'A custom response type',
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'type' => Type::string(),
                'description' => 'The JWT token',
            ],
            'code' => [
                'type' => Type::int(),
                'description' => 'Http status code',
            ],
        ];
    }
}
