<?php

namespace App\GraphQL\Mutations;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tymon\JWTAuth\Facades\JWTAuth;

class InviteCompany
{
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $ownerEmail = $args['input']['ownerEmail'];
            $organizationName = $args['input']['organizationName'];

            $user = User::firstOrCreate(['email' => $ownerEmail]);

            if (!$user) {
                throw new \Exception("Failed to create or find user.");
            }

            $jwtToken = JWTAuth::fromUser($user);

            $invitation = Invitation::create([
                'email' => $ownerEmail,
                'jwt'   => $jwtToken,
            ]);

            $company = Company::create(['owner_email' => $ownerEmail, 'name' => $organizationName]);

            if (!$invitation || !$company) {
                throw new \Exception("Failed to create invitation or company.");
            }

            $user->companies()->attach($company, ['role_id' => 2]);

            return [
                'token' => $jwtToken,
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
