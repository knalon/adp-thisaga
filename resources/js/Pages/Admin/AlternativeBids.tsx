import React from 'react';
import { Head, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { PageProps } from '@/types';
import { CurrencyDollarIcon, CheckIcon, XMarkIcon } from '@heroicons/react/24/outline';

interface Car {
  id: number;
  title: string;
  make: string;
  model: string;
  registration_year: number;
  price: number;
  slug: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
}

interface Bid {
  id: number;
  car_id: number;
  user: {
    id: number;
    name: string;
    email: string;
  };
  bid_price: number;
  bid_approved: boolean;
  appointment_date: string;
  created_at: string;
}

export default function AlternativeBids({ 
  auth, 
  car, 
  alternativeBids 
}: PageProps & { car: Car, alternativeBids: Bid[] }) {
  
  const approveBid = (appointmentId: number) => {
    if (confirm('Are you sure you want to approve this bid?')) {
      router.patch(`/admin/appointments/${appointmentId}/approve-bid`, {}, {
        onSuccess: () => {
          // After approval, redirect to finalize the bid transaction
          router.visit(`/admin/appointments/${appointmentId}/finalize-bid`);
        }
      });
    }
  };

  const rejectBid = (appointmentId: number) => {
    if (confirm('Are you sure you want to reject this bid?')) {
      router.patch(`/admin/appointments/${appointmentId}/reject-bid`);
    }
  };

  return (
    <AdminLayout>
      <Head title="Alternative Bids" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="flex justify-between items-center mb-6">
            <h1 className="text-3xl font-bold text-gray-900">Alternative Bids for Car</h1>
            <a 
              href={`/admin/cars`}
              className="btn btn-primary btn-sm"
            >
              Back to Cars
            </a>
          </div>

          <div className="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div className="px-4 py-5 sm:px-6 bg-indigo-50">
              <h3 className="text-lg leading-6 font-medium text-indigo-900">Car Details</h3>
              <p className="mt-1 max-w-2xl text-sm text-indigo-700">
                {car.make} {car.model} ({car.registration_year})
              </p>
              <p className="mt-1 max-w-2xl text-sm text-indigo-700">
                Listed by: {car.user.name} ({car.user.email})
              </p>
              <p className="mt-1 max-w-2xl text-sm text-indigo-700">
                Listed Price: ${car.price.toLocaleString()}
              </p>
            </div>
          </div>

          <div className="bg-white shadow overflow-hidden sm:rounded-lg">
            <div className="px-4 py-5 sm:px-6">
              <div className="flex items-center">
                <CurrencyDollarIcon className="h-6 w-6 text-primary mr-2" />
                <h3 className="text-lg leading-6 font-medium text-gray-900">
                  Alternative Bids Available
                </h3>
              </div>
              <p className="mt-1 max-w-2xl text-sm text-gray-500">
                The previously approved bid has been cancelled. Here are other available bids.
              </p>
            </div>

            {alternativeBids.length === 0 ? (
              <div className="bg-white px-4 py-12 sm:px-6 text-center">
                <CurrencyDollarIcon className="mx-auto h-12 w-12 text-gray-400" />
                <h3 className="mt-2 text-sm font-medium text-gray-900">No alternative bids available</h3>
                <p className="mt-1 text-sm text-gray-500">
                  There are no other pending bids for this car.
                </p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Bidder
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Bid Amount
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                      </th>
                      <th scope="col" className="relative px-6 py-3">
                        <span className="sr-only">Actions</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {alternativeBids.map((bid) => (
                      <tr key={bid.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            <div>
                              <div className="text-sm font-medium text-gray-900">
                                {bid.user.name}
                              </div>
                              <div className="text-sm text-gray-500">
                                {bid.user.email}
                              </div>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-green-600">
                            ${bid.bid_price.toLocaleString()}
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {new Date(bid.created_at).toLocaleDateString()}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <button
                            onClick={() => approveBid(bid.id)}
                            className="text-green-600 hover:text-green-900 mr-4"
                          >
                            <span className="flex items-center">
                              <CheckIcon className="h-4 w-4 mr-1" />
                              Approve Bid
                            </span>
                          </button>
                          <button
                            onClick={() => rejectBid(bid.id)}
                            className="text-red-600 hover:text-red-900"
                          >
                            <span className="flex items-center">
                              <XMarkIcon className="h-4 w-4 mr-1" />
                              Reject Bid
                            </span>
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>
      </div>
    </AdminLayout>
  );
} 