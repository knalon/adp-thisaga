import React, { useState } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import InputError from '@/Components/Core/InputError';
import InputLabel from '@/Components/Core/InputLabel';
import TextInput from '@/Components/Core/TextInput';
import { User } from '@/types';

export default function VendorDetails({ user }: { user: User }) {
  const [isVendor, setIsVendor] = useState<boolean>(user.roles?.some(role => role.name === 'Vendor') || false);
  const [isEditing, setIsEditing] = useState<boolean>(false);
  
  const { props } = usePage();
  const token = (props.auth as any)?.csrf_token || '';
  
  const { data, setData, post, processing, reset, errors } = useForm({
    store_name: user.vendor?.store_name || '',
    store_address: user.vendor?.store_address || '',
    _token: token,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('profile.vendor.store'), {
      onSuccess: () => {
        setIsEditing(false);
      }
    });
  };

  if (!isVendor) {
    return (
      <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <h2 className="text-lg font-medium text-gray-900">Vendor Account</h2>
        <p className="mt-1 text-sm text-gray-600">
          Apply to become a vendor to list products on our marketplace.
        </p>
        <div className="mt-4">
          <button 
            type="button" 
            className="btn btn-primary"
            onClick={() => post(route('profile.vendor.apply'))}
            disabled={processing}
          >
            Apply to become a vendor
          </button>
        </div>
      </div>
    );
  }

  if (!isEditing && user.vendor) {
    return (
      <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div className="flex justify-between">
          <h2 className="text-lg font-medium text-gray-900">Vendor Details</h2>
          <button 
            type="button" 
            className="btn btn-sm btn-outline"
            onClick={() => setIsEditing(true)}
          >
            Edit
          </button>
        </div>
        
        <div className="mt-4">
          <div className="mb-4">
            <h3 className="text-sm font-medium text-gray-500">Store Name</h3>
            <p className="mt-1 text-sm">{user.vendor.store_name || 'Not set'}</p>
          </div>
          
          <div className="mb-4">
            <h3 className="text-sm font-medium text-gray-500">Store Address</h3>
            <p className="mt-1 text-sm">{user.vendor.store_address || 'Not set'}</p>
          </div>
          
          <div>
            <h3 className="text-sm font-medium text-gray-500">Status</h3>
            <div className="mt-1">
              <span className={`badge ${user.vendor.status === 'approved' ? 'badge-success' : user.vendor.status === 'pending' ? 'badge-warning' : 'badge-error'}`}>
                {user.vendor.status_label}
              </span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
      <form onSubmit={handleSubmit}>
        <h2 className="text-lg font-medium text-gray-900">Vendor Details</h2>
        <p className="mt-1 text-sm text-gray-600">
          Update your store information for customers to see.
        </p>
        
        <div className="mt-6 space-y-6">
          <div>
            <InputLabel htmlFor="store_name" value="Store Name" />
            <TextInput
              id="store_name"
              value={data.store_name}
              onChange={(e) => setData('store_name', e.target.value)}
              className="mt-1 block w-full"
              required
            />
            <InputError message={errors.store_name} className="mt-2" />
          </div>
          
          <div>
            <InputLabel htmlFor="store_address" value="Store Address" />
            <TextInput
              id="store_address"
              value={data.store_address}
              onChange={(e) => setData('store_address', e.target.value)}
              className="mt-1 block w-full"
              required
            />
            <InputError message={errors.store_address} className="mt-2" />
          </div>
          
          <div className="flex items-center gap-4">
            <button type="submit" className="btn btn-primary" disabled={processing}>
              Save
            </button>
            <button 
              type="button" 
              className="btn btn-outline"
              onClick={() => {
                reset();
                setIsEditing(false);
              }}
            >
              Cancel
            </button>
          </div>
        </div>
      </form>
    </div>
  );
} 