<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Enums\RolesEnum;
use Illuminate\Http\Request;
use App\Enums\VendorStatusEnum;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function profile(Vendor $vendor)
    {

    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name' => [
                'required', 
                'regex:/^[a-zA-Z0-9\s]+$/',
                Rule::unique('vendors', 'store_name')
                ->ignore($user->id, 'user_id')
            ],
            'store_address' => 'nullable',
        ] , [
            'store_name.regex' => 'Store name must only contain lowercase alphanumeric characters and spaces.',
        ]);
        $user = $request->user();
        $vendor = $user->vendor ?: new Vendor();
        $vendor->user_id = $user->id;
        $vendor->status = VendorStatusEnum::Approved->value;
        $vendor->store_name = $request->store_name;
        $vendor->store_address = $request->store_address;
        $vendor->save();

        $user->assignRole(RolesEnum::Vendor);
    }
}
