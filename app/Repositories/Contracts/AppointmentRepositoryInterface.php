<?php

namespace App\Repositories\Contracts;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentRepositoryInterface
{
    public function getAllForUser(int $userId): Collection;
    public function findById(int $id): ?Appointment;
    public function create(array $data): Appointment;
    public function update(Appointment $appointment, array $data): bool;
    public function delete(Appointment $appointment): bool;
}
