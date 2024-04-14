<?php

namespace App\GraphQL\Mutations;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use App\Models\UserRoleCompany;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tymon\JWTAuth\Facades\JWTAuth;

class inviteUser
{
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $code = 200;
        $jwtToken = null;

        $userEmail = $args['input']['email'];
        $roles     = $args['input']['roles'];
        $companyId = $args['input']['companyId'];

        try {
            DB::beginTransaction();
            $company = Company::find($companyId);
            if (!$company) {
                throw new \Exception("Such company not found.");
            }

            $user = User::firstOrCreate(['email' => $userEmail]);

            foreach($roles as $role) {
                $existRole = UserRoleCompany::where([
                    'user_id'    => $user->id,
                    'company_id' => $companyId,
                    'role_id'    => $role,
                ])->exists();

                if (!$existRole) {
                    $user->companies()->attach($company, ['role_id' => $role]);
                }
            }

            $jwtToken = JWTAuth::fromUser($user);
            Invitation::create([
                'email' => $userEmail,
                'jwt'   => $jwtToken,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
            $jwtToken = null;
        }

        return [
            'token' => $jwtToken,
            'code'  => $code,
        ];
    }
}
