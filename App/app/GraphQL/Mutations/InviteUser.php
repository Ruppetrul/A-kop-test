<?php

namespace App\GraphQL\Mutations;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use App\Models\UserCompany;
use App\Models\UserRoleCompany;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;
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

            $user->companies()->syncWithoutDetaching([$companyId]);

            if (is_array($roles) && !empty($roles)) {
                $companyRelation = UserCompany::where([
                    'user_id'    => $user->id,
                    'company_id' => $company->id,
                ])->first();

                foreach($roles as $role) {
                    $companyRelation->roles()->syncWithoutDetaching([$role]);
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
