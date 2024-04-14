<?php

namespace App\GraphQL\Mutations;

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
            $userCompanies = $user->companies()->get();

            $roleData = [];
            foreach($userCompanies as $company) {
                $data = $company['pivot'];
                $roleData[$data['company_id']][] = $data['role_id'];
            }

            foreach ($roleData as $company => $roleIds) {
                $roles[] = ['company' => $company, 'roles' => $roleIds];
            }

            DB::commit();
        } catch (AuthorizationException $e) {
            $code = 401;
            $jwtToken = null;
        } catch (Exception $e) {
            $code = 500;
            $jwtToken = null;
        } finally {
            DB::rollBack();
        }

        return [
            'token' => $jwtToken,
            'code'  => $code,
            'roles' => $roles,
        ];
    }
}
