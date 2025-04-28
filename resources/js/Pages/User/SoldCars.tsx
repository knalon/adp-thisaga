import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { CheckCircleIcon } from '@heroicons/react/24/outline';

export default function SoldCars({ auth }: PageProps) {
  return (
    <AppLayout>
      <Head title="My Sold Cars" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">My Sold Cars</h1>

          <div className="bg-white shadow overflow-hidden sm:rounded-lg">
            <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
              <div className="flex items-center">
                <CheckCircleIcon className="h-6 w-6 text-indigo-500 mr-2" />
                <h3 className="text-lg leading-6 font-medium text-gray-900">Cars You've Sold</h3>
              </div>
              <p className="mt-1 max-w-2xl text-sm text-gray-500">
                View all completed sales of your vehicles
              </p>
            </div>

            {/* Empty state */}
            <div className="bg-white px-4 py-12 sm:px-6 text-center">
              <CheckCircleIcon className="mx-auto h-12 w-12 text-gray-400" />
              <h3 className="mt-2 text-sm font-medium text-gray-900">No sold cars yet</h3>
              <p className="mt-1 text-sm text-gray-500">
                You haven't sold any cars yet. List a car to get started.
              </p>
              <div className="mt-6">
                <a
                  href="/user/cars/create"
                  className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Add a Car for Sale
                </a>
              </div>
            </div>

            {/* Table for when there are sold cars (hidden with empty state) */}
            <div className="hidden">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Car
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Sale Price
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Buyer
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Sale Date
                    </th>
                    <th scope="col" className="relative px-6 py-3">
                      <span className="sr-only">Actions</span>
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {/* Sold car rows will be mapped here */}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
