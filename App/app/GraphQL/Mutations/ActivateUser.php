<?php

namespace App\GraphQL\Mutations;

use App\Models\Invitation;
use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ActivateUser
{
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $code = 200;

        $userEmail = $args['input']['email'];
        $jwt       = $args['input']['jwt'];
        $name      = $args['input']['name'];
        $surname   = $args['input']['surname'];
        $password  = $args['input']['password'];

        try {
            DB::beginTransaction();
            $invitation = Invitation::find([
                'email' => $userEmail,
                'jwt'   => $jwt,
            ]);

            if (!$invitation) {
                throw new \Exception("Invitation with such email adn jwt token not found.");
            }

            $user = User::firstOrNew(['email' => $userEmail]);
            if (!$user) {
                throw new \Exception("User not found.");
            }

            if ($user->password) {
                throw new \Exception("User has already been activated.");
            } else {
                $user->name = $name;
                $user->surname = $surname;
                $user->password = Hash::make($password);
                $user->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
        }

        return [
            'code' => $code
        ];
    }
}
