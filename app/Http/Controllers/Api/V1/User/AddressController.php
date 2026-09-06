<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreAddressRequest;
use App\Http\Requests\User\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = Address::where('user_id', $request->user()->id)->latest()->get();

        return $this->successResponse(
            AddressResource::collection($addresses),
            'Daftar alamat berhasil diambil.'
        );
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('private/addresses', 'local');
            $data['photo_path'] = $path;
        }

        if (! empty($data['is_default'])) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        } else {
            // Jika alamat pertama, otomatis set default
            $count = Address::where('user_id', $user->id)->count();
            if ($count === 0) {
                $data['is_default'] = true;
            }
        }

        $address = $user->addresses()->create($data);

        return $this->successResponse(
            new AddressResource($address),
            'Alamat berhasil ditambahkan.',
            201
        );
    }

    public function update(int $id, UpdateAddressRequest $request): JsonResponse
    {
        $address = Address::findOrFail($id);
        Gate::authorize('update', $address);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('private/addresses', 'local');
            $data['photo_path'] = $path;
        }

        if (! empty($data['is_default'])) {
            Address::where('user_id', $request->user()->id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return $this->successResponse(
            new AddressResource($address->fresh()),
            'Alamat berhasil diperbarui.'
        );
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        $address = Address::findOrFail($id);
        Gate::authorize('delete', $address);

        $address->delete();

        return $this->successResponse(null, 'Alamat berhasil dihapus.');
    }
}
