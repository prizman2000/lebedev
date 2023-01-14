<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    public function response($data, $status = Response::HTTP_OK, $headers = []): JsonResponse
    {
        $result = is_array($data) ? $data : json_decode($data);
        return new JsonResponse($data, $status, $headers);
    }

    public function transformJsonBody(Request $request): Request
    {
        $body = json_decode($request->getContent(), true);
        if (!$body) {
            return $request;
        }
        $request->request->replace($body);

        return $request;
    }
}
