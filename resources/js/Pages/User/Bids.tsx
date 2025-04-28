import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { CurrencyDollarIcon, XMarkIcon } from '@heroicons/react/24/outline';

interface BidAppointment {
  id: number;
  car: {
    id: number;
    title: string;
    make: string;
    model: string;
    registration_year: number;
    price: number;
    slug: string;
    images: any[];
  };
  bid_price: number;
  bid_approved: boolean;
  status: string;
  appointment_date: string;
  created_at: string;
  transaction?: {
    id: number;
    status: string;
  };
}

export default function UserBids({ auth, appointments }: PageProps & { appointments: BidAppointment[] }) {
  const [processingId, setProcessingId] = useState<number | null>(null);

  // Filter appointments that have bids
  const bidsWithAppointments = appointments ? appointments.filter(app => app.bid_price !== null) : [];

  const cancelBid = (appointmentId: number) => {
    if (confirm('Are you sure you want to cancel this bid? This action cannot be undone.')) {
      setProcessingId(appointmentId);
      router.post(`/appointments/${appointmentId}/cancel-bid`, {}, {
        onSuccess: () => {
          setProcessingId(null);
        },
        onError: () => {
          setProcessingId(null);
          alert('There was a problem cancelling your bid. Please try again.');
        }
      });
    }
  };

  const hasPendingTransaction = (bid: BidAppointment) => {
    return bid.transaction && bid.transaction.status === 'pending';
  };

  const hasCompletedTransaction = (bid: BidAppointment) => {
    return bid.transaction && bid.transaction.status === 'paid';
  };

  const canCancelBid = (bid: BidAppointment) => {
    // Can cancel if:
    // 1. There's no transaction, or
    // 2. The transaction is still pending (not paid)
    return !hasCompletedTransaction(bid);
  };

  return (
    <AppLayout>
      <Head title="My Bids" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">My Bids</h1>

          <div className="bg-white shadow overflow-hidden sm:rounded-lg">
            <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
              <div className="flex items-center">
                <CurrencyDollarIcon className="h-6 w-6 text-primary mr-2" />
                <h3 className="text-lg leading-6 font-medium text-gray-900">Your Active Bids</h3>
              </div>
              <p className="mt-1 max-w-2xl text-sm text-gray-500">
                Track all your current bids on vehicles
              </p>
            </div>

            {bidsWithAppointments.length === 0 ? (
              // Empty state
              <div className="bg-white px-4 py-12 sm:px-6 text-center">
                <CurrencyDollarIcon className="mx-auto h-12 w-12 text-gray-400" />
                <h3 className="mt-2 text-sm font-medium text-gray-900">No bids yet</h3>
                <p className="mt-1 text-sm text-gray-500">
                  You haven't placed any bids on vehicles yet.
                </p>
                <div className="mt-6">
                  <a
                    href="/cars"
                    className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                  >
                    Browse Auction Cars
                  </a>
                </div>
              </div>
            ) : (
              // Table with bids
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Car
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Your Bid
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
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
                    {bidsWithAppointments.map((bid) => (
                      <tr key={bid.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            <div className="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-md overflow-hidden">
                              {bid.car.images && bid.car.images.length > 0 ? (
                                <img src={bid.car.images[0].url} alt={bid.car.title} />
                              ) : (
                                <div className="h-full w-full flex items-center justify-center bg-gray-100">
                                  <CurrencyDollarIcon className="h-6 w-6 text-gray-400" />
                                </div>
                              )}
                            </div>
                            <div className="ml-4">
                              <div className="text-sm font-medium text-gray-900">
                                <a href={`/cars/${bid.car.slug}`} className="hover:text-primary">
                                  {bid.car.make} {bid.car.model} ({bid.car.registration_year})
                                </a>
                              </div>
                              <div className="text-sm text-gray-500">
                                Listed for ${bid.car.price.toLocaleString()}
                              </div>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-green-600">
                            ${bid.bid_price.toLocaleString()}
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                            bid.bid_approved
                              ? 'bg-green-100 text-green-800'
                              : 'bg-yellow-100 text-yellow-800'
                          }`}>
                            {bid.bid_approved ? 'Approved' : 'Pending Approval'}
                          </span>
                          {hasPendingTransaction(bid) && (
                            <span className="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                              Payment Pending
                            </span>
                          )}
                          {hasCompletedTransaction(bid) && (
                            <span className="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                              Purchased
                            </span>
                          )}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {new Date(bid.created_at).toLocaleDateString()}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          {canCancelBid(bid) && (
                            <button
                              onClick={() => cancelBid(bid.id)}
                              disabled={processingId === bid.id}
                              className={`text-red-600 hover:text-red-900 ${processingId === bid.id ? 'opacity-50 cursor-not-allowed' : ''}`}
                            >
                              <span className="flex items-center">
                                <XMarkIcon className="h-4 w-4 mr-1" />
                                {processingId === bid.id ? 'Cancelling...' : 'Cancel Bid'}
                              </span>
                            </button>
                          )}
                          {hasPendingTransaction(bid) && (
                            <a
                              href={`/payment/${bid.transaction.id}`}
                              className="ml-4 text-blue-600 hover:text-blue-900"
                            >
                              Complete Payment
                            </a>
                          )}
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
    </AppLayout>
  );
}
