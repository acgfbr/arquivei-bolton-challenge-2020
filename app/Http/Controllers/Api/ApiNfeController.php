<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\Core\CommandBus\CommandBus;
use Src\Core\Validation\ValidationException;
use Src\Nfe\Commands\FindNfeCommand;
use Throwable;

class ApiNfeController extends Controller
{
    private $bus;
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke($access_key = null)
    {
        $command = new FindNfeCommand(['access_key' => $access_key]);
        try {
            $response = $this->bus->execute($command);
            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'validation error', 'errors' => $e->getErrors()], 400);
        } catch (Throwable $e) {
            return response()->json(['message' => 'internal error', 'errors' => $e->getMessage()], 500);
        }
    }
}
