<?php

namespace App\GraphQL\Mutations;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tymon\JWTAuth\Facades\JWTAuth;

class Invite
{
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $ownerEmail = $args['input']['ownerEmail'];

        $user = User::firstOrCreate(['email' => $ownerEmail]);

        $jwtToken = JWTAuth::fromUser($user);

        $invitation = Invitation::create([
            'email' => $ownerEmail,
            'jwt'   => $jwtToken,
        ]);

        $company = Company::create([
            'name' => $args['input']['organizationName'],
        ]);

        //TODo почему то role_id = null в запросе
        //$user->companies()->attach($company, ['role_id' => 1]);
        return [
            'token' => $jwtToken,
        ];
    }
}
