<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tymon\JWTAuth\Facades\JWTAuth;

class loginUser
{
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $code = 200;
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
                return response()->json(['error' => 'Unauthorized'], 401);
            }

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
