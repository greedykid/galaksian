<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminActivityLogResource;
use App\Models\AdminActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AdminActivityLog::with('admin')->latest();

        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }

        if ($adminId = $request->query('admin_id')) {
            $query->where('admin_id', $adminId);
        }

        if ($search = $request->query('q')) {
            $query->whereLike('description', "%{$search}%");
        }

        $perPage = min((int) $request->query('per_page', 30), 100);
        $logs = $query->paginate($perPage);

        return $this->successResponse(
            AdminActivityLogResource::collection($logs)->response()->getData(true),
            'Daftar activity log berhasil diambil.'
        );
    }
}
