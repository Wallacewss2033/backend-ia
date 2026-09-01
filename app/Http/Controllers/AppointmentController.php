<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    protected AppointmentRepositoryInterface $repository;

    public function __construct(AppointmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(): JsonResponse
    {
        // Assuming authenticated user. Using id 1 as fallback for local dev without auth
        $userId = auth()->id() ?? 1;
        $appointments = $this->repository->getAllForUser($userId);
        
        return response()->json([
            'data' => AppointmentResource::collection($appointments)
        ]);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $userId = auth()->id() ?? 1;
        
        $data = $request->validated();
        $data['user_id'] = $userId;
        
        $appointment = $this->repository->create($data);
        
        return response()->json([
            'message' => 'Appointment created successfully.',
            'data' => new AppointmentResource($appointment->fresh())
        ], 201);
    }

    public function update(UpdateAppointmentRequest $request, int $id): JsonResponse
    {
        $appointment = $this->repository->findById($id);
        
        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        $this->repository->update($appointment, $request->validated());
        
        return response()->json([
            'message' => 'Appointment updated successfully.',
            'data' => new AppointmentResource($appointment->fresh())
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $appointment = $this->repository->findById($id);
        
        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        $this->repository->delete($appointment);
        
        return response()->json([
            'message' => 'Appointment deleted successfully.'
        ]);
    }
}
