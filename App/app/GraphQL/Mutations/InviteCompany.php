<?php

namespace App\GraphQL\Mutations;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use App\Models\UserCompany;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tymon\JWTAuth\Facades\JWTAuth;

class InviteCompany
{
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $code = 201;
        $jwtToken = null;
        try {
            DB::beginTransaction();

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

            $company = Company::firstOrCreate(
                ['owner_email' => $ownerEmail],
                ['name' => $organizationName]
            );

            if (!$invitation || !$company) {
                throw new \Exception("Failed to create invitation or company.");
            }

            if ($user->wasRecentlyCreated) {
                $user->companies()->attach($company);

                $companyRelation = UserCompany::where([
                    'user_id'    => $user->id,
                    'company_id' => $company->id,
                ])->first();

                $companyRelation->roles()->attach(2);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $jwtToken = null;
            $code = 500;
        }

        return [
            'token' => $jwtToken,
            'code'  => $code
        ];
    }
}
