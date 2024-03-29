<?php

/*
 * Custom JWT authentication middleware since the original package does
 * not have a configurable option to change the authorization token name.
 *
 * The token name by default is set to 'bearer'.
 * The default middleware provided does not have any flexibility to
 * change the token name.
 *
 * This project api spec requires us to use the token name 'token'.
 */

namespace App\Http\Middleware;

use App\Instructor;
use Closure;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;


class AuthenticateInstructorWithJWT extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param bool $optional
     * @return mixed
     */
    public function handle($request, Closure $next, $optional = null)
    {
        $this->auth->setRequest($request);

        try {
            if (!$user = $this->auth->parseToken('token')->authenticate()) {
                return $this->respondError('JWT error: User not found');
            }
        } catch (TokenExpiredException $e) {
            return $this->respondError('JWT error: Token has expired');
        } catch (TokenInvalidException $e) {
            return $this->respondError('JWT error: Token is invalid');
        } catch (JWTException $e) {
            if ($optional === null) {
                return $this->respondError('JWT error: Token is absent');
            }
        }

        return $next($request);
    }

    /**
     * Respond with json error message.
     *
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondError($message)
    {
        return response()->json([
            'errors' => [
                'message' => $message,
                'status_code' => 401
            ]
        ], 401);
    }
}
