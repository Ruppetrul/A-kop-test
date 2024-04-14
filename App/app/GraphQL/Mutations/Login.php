<?php

namespace App\GraphQL\Mutations;

use App\Models\UserCompany;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tymon\JWTAuth\Facades\JWTAuth;

class login
{
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $code = 200;
        $roles = [];
        $companies = [];
        $jwtToken = null;

        $email = $args['input']['email'];
        $password = $args['input']['password'];

        try {
            DB::beginTransaction();

            $credentials = [
                'email' => $email,
                'password' => $password,
            ];
            if (!$jwtToken = JWTAuth::attempt($credentials)) {
                throw new AuthorizationException();
            }

            $user = JWTAuth::user();

            $companiesResult = $user->companies()->get();

            foreach ($companiesResult as $company) {
                $companies[] = $company->id;

                $userCompanyRelation = UserCompany::where([
                    'user_id'    => $user->id,
                    'company_id' => $company->id,
                ])->first();

                if ($userCompanyRelation) {
                    $rolesResult = $userCompanyRelation->roles()->get();
                    $roleData = [];
                    foreach ($rolesResult as $role) {
                        $roleData[] = $role->id;
                    }

                    $roles[] = ['company_id' => $company->id, 'role_ids' => $roleData];
                }
            }

            DB::commit();
        } catch (AuthorizationException $e) {
            $code = 401;
            $jwtToken = null;
            $roles = [];
            $companies = [];
        } catch (Exception $e) {
            $code = 500;
            $jwtToken = null;
            $roles = [];
            $companies = [];
        } finally {
            DB::rollBack();
        }

        return [
            'token'     => $jwtToken,
            'code'      => $code,
            'roles'     => $roles,
            'companies' => $companies,
        ];
    }
}
