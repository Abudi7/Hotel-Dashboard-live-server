<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiRequestController extends AbstractController
{
    private $names = [];

    #[Route('/api/request', name: 'app_api_request_get', methods: ['GET'])]
    public function getNames(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        
        // If the 'name' parameter is provided, return only the requested name
        if ($name !== null && isset($this->names[$name])) {
            return $this->json([$name => $this->names[$name]]);
        }
        
        // If no 'name' parameter is provided or the name doesn't exist, return all names
        return $this->json($this->names);
    }

    #[Route('/api/request', name: 'app_api_request_create', methods: ['POST'])]
    public function createName(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Check if 'name' key exists in the request data
        if (!isset($data['name'])) {
            return new JsonResponse(["error" => "Name is missing in the request data"], Response::HTTP_BAD_REQUEST);
        }
        
        // Extract name from the request data
        $name = $data['name'];
        
        // Additional data in POST request
        $additionalData = isset($data['additional_data']) ? $data['additional_data'] : null;
        // Process additional data if needed
        
        // Add the name to the list
        $this->names[] = $name;
        
        // Return success message
        return new JsonResponse(["message" => "Name '{$name}' added successfully"], Response::HTTP_CREATED);
    }

    #[Route('/api/request/{index}', name: 'app_api_request_update', methods: ['PUT'])]
    public function updateName(Request $request, int $index): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Check if 'name' key exists in the request data
        if (!isset($data['name'])) {
            return new JsonResponse(["error" => "Name is missing in the request data"], Response::HTTP_BAD_REQUEST);
        }
        
        $name = $data['name'];
        
        // Check if the index exists in the names array
        if (isset($this->names[$index])) {
            $this->names[$index] = $name;
            return new JsonResponse(["message" => "Name at index {$index} updated to '{$name}'"], Response::HTTP_OK);
        } else {
            return new JsonResponse(["error" => "Name not found"], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/request/{index}', name: 'app_api_request_delete', methods: ['DELETE'])]
    public function deleteName(int $index): JsonResponse
    {
        // Check if the index exists in the names array
        if (isset($this->names[$index])) {
            unset($this->names[$index]);
            $this->names = array_values($this->names); // reindex array
            return new JsonResponse(["message" => "Name at index {$index} deleted"], Response::HTTP_OK);
        } else {
            return new JsonResponse(["error" => "Name not found"], Response::HTTP_NOT_FOUND);
        }
    }
}
