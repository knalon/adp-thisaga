import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { TruckIcon, EyeIcon, DocumentTextIcon } from '@heroicons/react/24/outline';

interface Car {
  id: number;
  make: string;
  model: string;
  year: number;
  price: number;
  slug: string;
  color: string;
  mileage: number;
  transmission: string;
  purchase_date: string;
  images: { id: number; url: string }[];
}

interface Transaction {
  id: number;
  amount: number;
  transaction_date: string;
  payment_method: string;
  status: string;
  invoice_number: string;
}

interface PurchasedCar {
  id: number;
  car: Car;
  transaction: Transaction;
  delivery_status: 'pending' | 'shipped' | 'delivered';
  delivery_date: string | null;
  tracking_number: string | null;
}

interface MyPurchasedCarsProps extends PageProps {
  purchasedCars: PurchasedCar[];
}

export default function MyPurchasedCars({ auth, purchasedCars = [] }: MyPurchasedCarsProps) {
  return (
    <AppLayout>
      <Head title="My Purchased Cars" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">My Purchased Cars</h1>

          <div className="bg-white shadow-md rounded-lg overflow-hidden">
            {purchasedCars.length > 0 ? (
              <div className="grid gap-6 md:grid-cols-2 p-6">
                {purchasedCars.map((purchase) => (
                  <div key={purchase.id} className="card bg-base-100 shadow-md">
                    <figure className="h-48 bg-gray-100">
                      {purchase.car.images.length > 0 ? (
                        <img
                          src={purchase.car.images[0].url}
                          alt={`${purchase.car.year} ${purchase.car.make} ${purchase.car.model}`}
                          className="object-cover w-full h-full"
                        />
                      ) : (
                        <div className="flex items-center justify-center w-full h-full">
                          <TruckIcon className="h-12 w-12 text-gray-400" />
                        </div>
                      )}
                    </figure>
                    <div className="card-body">
                      <h2 className="card-title">
                        {purchase.car.year} {purchase.car.make} {purchase.car.model}
                      </h2>
                      <div className="grid grid-cols-2 gap-2 my-3">
                        <div className="text-sm">
                          <span className="text-gray-500">Color:</span> {purchase.car.color}
                        </div>
                        <div className="text-sm">
                          <span className="text-gray-500">Mileage:</span> {purchase.car.mileage.toLocaleString()} km
                        </div>
                        <div className="text-sm">
                          <span className="text-gray-500">Transmission:</span> {purchase.car.transmission}
                        </div>
                        <div className="text-sm">
                          <span className="text-gray-500">Purchase Date:</span>{' '}
                          {new Date(purchase.car.purchase_date).toLocaleDateString()}
                        </div>
                      </div>
                      <div className="mb-3">
                        <div className="font-medium">Price Paid</div>
                        <div className="text-2xl font-bold text-primary">
                          ${purchase.transaction.amount.toLocaleString()}
                        </div>
                        <div className="text-xs text-gray-500">
                          Invoice #{purchase.transaction.invoice_number}
                        </div>
                      </div>

                      <div className="mb-3">
                        <div className="font-medium">Delivery Status</div>
                        <div className="flex items-center mt-1">
                          <span className={`badge ${
                            purchase.delivery_status === 'delivered' ? 'badge-success' :
                            purchase.delivery_status === 'shipped' ? 'badge-info' :
                            'badge-warning'
                          }`}>
                            {purchase.delivery_status.charAt(0).toUpperCase() + purchase.delivery_status.slice(1)}
                          </span>

                          {purchase.tracking_number && (
                            <span className="text-xs ml-2">
                              Tracking: {purchase.tracking_number}
                            </span>
                          )}
                        </div>
                        {purchase.delivery_date && (
                          <div className="text-sm mt-1">
                            {purchase.delivery_status === 'delivered' ? 'Delivered on' : 'Expected delivery'}:{' '}
                            {new Date(purchase.delivery_date).toLocaleDateString()}
                          </div>
                        )}
                      </div>

                      <div className="card-actions justify-end mt-3">
                        <Link
                          href={route('cars.show', purchase.car.slug)}
                          className="btn btn-sm btn-ghost"
                          title="View Details"
                        >
                          <EyeIcon className="h-4 w-4 mr-1" /> Details
                        </Link>
                        <Link
                          href={route('transactions.viewInvoice', purchase.transaction.id)}
                          className="btn btn-sm btn-primary"
                          title="View Invoice"
                        >
                          <DocumentTextIcon className="h-4 w-4 mr-1" /> Invoice
                        </Link>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="p-12 text-center">
                <div className="rounded-full bg-gray-100 p-3 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                  <TruckIcon className="h-8 w-8 text-gray-400" />
                </div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">No purchased cars yet</h3>
                <p className="text-gray-500 max-w-md mx-auto mb-6">
                  You haven't purchased any cars yet. Browse our collection to find your dream car.
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
