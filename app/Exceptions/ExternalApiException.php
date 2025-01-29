<?php

namespace App\Exceptions;

use Exception;

class ExternalApiException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'error' => 'Erro na API externa',
            'message' => $this->getMessage()
        ], 503);
    }
}
