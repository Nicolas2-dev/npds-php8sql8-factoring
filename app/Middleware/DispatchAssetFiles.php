<?php

namespace App\Middleware;

use Closure;

use Npds\Http\Request;
use Npds\Http\Response;
use Npds\Http\JsonResponse;

use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class DispatchAssetFiles
{

    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), array('GET', 'HEAD', 'OPTIONS'))) {
            $path = $request->path();

            // Check if the Request instance asks for an asset file.
            if (preg_match('#^assets/(.*)$#', $path, $matches) === 1) {
                $path = WEBPATH .'assets' .DS .str_replace('/', DS, $matches[1]);

                return $this->createFileResponse($path, $request);
            }
        }

        return $next($request);
    }

    protected function createFileResponse($path, Request $request)
    {
        if (! file_exists($path)) {
            return new Response('File Not Found', 404);
        } else if (! is_readable($path)) {
            return new Response('Unauthorized Access', 403);
        }

        $headers = array(
            'Access-Control-Allow-Origin' => '*',
        );

        if ($request->method() == 'OPTIONS') {
            // The OPTIONS method needs a special handling.

            return new Response('OK', 200, array_merge($headers, array(
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin',
            )));
        }

        $headers['Content-Type'] = $mimeType = $this->guessMimeType($path);

        if ($mimeType === 'application/json') {
            $data = json_decode(file_get_contents($path), true);

            return new JsonResponse($data, 200, $headers);
        }

        $response = new BinaryFileResponse($path, 200, $headers, true, 'inline', true, false);

        $response->isNotModified($request);

        return $response->prepare($request);
    }

    protected function guessMimeType($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'css':
                return 'text/css';

            case 'js':
                return 'application/javascript';

            case 'json':
                return 'application/json';

            case 'svg':
                return 'image/svg+xml';

            default:
                break;
        }
        
        $mimeTypes = new MimeTypes();
        return $mimeTypes->guessMimeType($path);
    }
}
