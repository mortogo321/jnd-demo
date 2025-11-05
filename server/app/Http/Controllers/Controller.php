<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="URL Shortener API",
 *     version="1.0.0",
 *     description="API documentation for URL Shortener application",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="token",
 *     description="Enter token in format: <token>"
 * )
 */
abstract class Controller
{
    //
}
