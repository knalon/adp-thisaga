import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { CurrencyDollarIcon, EyeIcon } from '@heroicons/react/24/outline';

interface Bid {
  id: number;
  amount: number;
  status: 'pending' | 'accepted' | 'rejected';
  created_at: string;
  appointment: {
    id: number;
    date: string;
    time: string;
    car: {
      id: number;
      make: string;
      model: string;
      year: number;
      slug: string;
      price: number;
    };
  };
}

interface MyBidsProps extends PageProps {
  bids: Bid[];
}

export default function MyBids({ auth, bids = [] }: MyBidsProps) {
  return (
    <AppLayout>
      <Head title="My Bids" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">My Bids</h1>

          <div className="bg-white shadow-md rounded-lg overflow-hidden">
            {bids.length > 0 ? (
              <div className="overflow-x-auto">
                <table className="table w-full">
                  <thead>
                    <tr>
                      <th>Car</th>
                      <th>Appointment Date</th>
                      <th>Your Bid</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {bids.map((bid) => (
                      <tr key={bid.id}>
                        <td>
                          <div className="font-medium">
                            {bid.appointment.car.year} {bid.appointment.car.make} {bid.appointment.car.model}
                          </div>
                          <div className="text-sm text-gray-500">
                            Listed: ${bid.appointment.car.price.toLocaleString()}
                          </div>
                        </td>
                        <td>
                          {new Date(bid.appointment.date).toLocaleDateString()} at {bid.appointment.time}
                        </td>
                        <td>
                          <div className="font-semibold text-primary">
                            ${bid.amount.toLocaleString()}
                          </div>
                          <div className="text-xs text-gray-500">
                            Submitted on {new Date(bid.created_at).toLocaleDateString()}
                          </div>
                        </td>
                        <td>
                          <span className={`badge ${
                            bid.status === 'accepted' ? 'badge-success' :
                            bid.status === 'rejected' ? 'badge-error' :
                            'badge-warning'
                          }`}>
                            {bid.status.charAt(0).toUpperCase() + bid.status.slice(1)}
                          </span>
                        </td>
                        <td>
                          <div className="flex gap-2">
                            <Link
                              href={route('cars.show', bid.appointment.car.slug)}
                              className="btn btn-sm btn-ghost"
                              title="View Car"
                            >
                              <EyeIcon className="h-4 w-4" />
                            </Link>
                            {bid.status === 'pending' && (
                              <Link
                                href={route('appointments.submitBid', { appointment: bid.appointment.id })}
                                className="btn btn-sm btn-primary"
                                title="Update Bid"
                              >
                                <CurrencyDollarIcon className="h-4 w-4" />
                              </Link>
                            )}
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="p-12 text-center">
                <div className="rounded-full bg-gray-100 p-3 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                  <CurrencyDollarIcon className="h-8 w-8 text-gray-400" />
                </div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">No bids yet</h3>
                <p className="text-gray-500 max-w-md mx-auto mb-6">
                  You haven't made any bids on cars yet. Browse cars and schedule appointments to make a bid.
                </p>
                <Link href={route('cars.index')} className="btn btn-primary">
                  Browse Cars
                </Link>
              </div>
            )}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
