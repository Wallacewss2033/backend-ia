<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function getAllForUser(int $userId): Collection
    {
        return Appointment::where('user_id', $userId)->get();
    }

    public function findById(int $id): ?Appointment
    {
        return Appointment::find($id);
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function update(Appointment $appointment, array $data): bool
    {
        return $appointment->update($data);
    }

    public function delete(Appointment $appointment): bool
    {
        return $appointment->delete();
    }
}
