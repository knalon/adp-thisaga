import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import {
  UsersIcon,
  TruckIcon,
  CalendarIcon,
  CreditCardIcon,
  ChartBarIcon,
  CogIcon,
  ArrowDownTrayIcon
} from '@heroicons/react/24/outline';

interface AdminSidebarItem {
  name: string;
  href: string;
  icon: React.ElementType;
  current: boolean;
}

export default function AdminTransactions({ auth }: PageProps) {
  const [activeSection, setActiveSection] = useState('transactions');

  // Admin navigation items with icons
  const adminSidebarItems: AdminSidebarItem[] = [
    { name: 'Overview', href: '/admin/dashboard', icon: ChartBarIcon, current: activeSection === 'overview' },
    { name: 'Manage Users', href: '/admin/users', icon: UsersIcon, current: activeSection === 'users' },
    { name: 'Manage Cars', href: '/admin/cars', icon: TruckIcon, current: activeSection === 'cars' },
    { name: 'Appointments', href: '/admin/appointments', icon: CalendarIcon, current: activeSection === 'appointments' },
    { name: 'Transactions', href: '/admin/transactions', icon: CreditCardIcon, current: activeSection === 'transactions' },
    { name: 'Settings', href: '/admin/settings', icon: CogIcon, current: activeSection === 'settings' },
  ];

  function classNames(...classes: string[]) {
    return classes.filter(Boolean).join(' ');
  }

  return (
    <AppLayout>
      <Head title="Transactions" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">Transactions</h1>

          <div className="flex flex-col md:flex-row gap-6">
            {/* Sidebar */}
            <div className="w-full md:w-64 bg-white shadow rounded-lg overflow-hidden">
              <div className="p-6 bg-primary text-white">
                <h2 className="text-xl font-semibold">Admin Panel</h2>
              </div>
              <nav className="mt-2 p-2">
                <ul className="space-y-1">
                  {adminSidebarItems.map((item) => (
                    <li key={item.name}>
                      <Link
                        href={item.href}
                        className={classNames(
                          item.current
                            ? 'bg-gray-100 text-primary'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-primary',
                          'group flex items-center px-3 py-2 text-sm font-medium rounded-md'
                        )}
                      >
                        <item.icon
                          className={classNames(
                            item.current ? 'text-primary' : 'text-gray-400 group-hover:text-primary',
                            'mr-3 flex-shrink-0 h-6 w-6'
                          )}
                          aria-hidden="true"
                        />
                        {item.name}
                      </Link>
                    </li>
                  ))}
                </ul>
              </nav>
            </div>

            {/* Main Content */}
            <div className="flex-1 bg-white shadow rounded-lg overflow-hidden">
              <div className="p-6 border-b border-gray-200">
                <h2 className="text-xl font-medium text-gray-900">Transaction History</h2>
              </div>

              <div className="p-6">
                {/* Summary cards */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                  <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div className="flex items-center">
                      <div className="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <CreditCardIcon className="h-6 w-6" />
                      </div>
                      <div>
                        <p className="text-sm text-gray-500">Total Revenue</p>
                        <p className="text-2xl font-semibold">$0</p>
                      </div>
                    </div>
                  </div>

                  <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div className="flex items-center">
                      <div className="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <TruckIcon className="h-6 w-6" />
                      </div>
                      <div>
                        <p className="text-sm text-gray-500">Cars Sold</p>
                        <p className="text-2xl font-semibold">0</p>
                      </div>
                    </div>
                  </div>

                  <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div className="flex items-center">
                      <div className="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                        <CalendarIcon className="h-6 w-6" />
                      </div>
                      <div>
                        <p className="text-sm text-gray-500">This Month</p>
                        <p className="text-2xl font-semibold">$0</p>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Search and filters */}
                <div className="flex flex-col md:flex-row md:items-center mb-6 gap-4">
                  <div className="flex-1">
                    <div className="form-control">
                      <div className="input-group">
                        <input type="text" placeholder="Search transactions..." className="input input-bordered w-full" />
                        <button className="btn btn-square">
                          <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div className="flex-shrink-0">
                    <select className="select select-bordered">
                      <option disabled selected>Date Range</option>
                      <option>Last 7 days</option>
                      <option>Last 30 days</option>
                      <option>Last 90 days</option>
                      <option>Custom</option>
                    </select>
                  </div>
                  <div className="flex-shrink-0">
                    <button className="btn btn-outline">
                      <ArrowDownTrayIcon className="h-5 w-5 mr-1" />
                      Export
                    </button>
                  </div>
                </div>

                {/* No transactions state */}
                <div className="text-center py-12">
                  <CreditCardIcon className="h-16 w-16 text-gray-400 mx-auto mb-4" />
                  <h3 className="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                  <p className="text-gray-500 mb-6">There are no financial transactions recorded in the system</p>
                </div>

                {/* Transaction management tips */}
                <div className="bg-blue-50 p-4 rounded-lg mt-8">
                  <h3 className="text-lg font-medium text-blue-800 mb-2">Financial Management Tips</h3>
                  <ul className="list-disc pl-5 text-blue-700 space-y-2">
                    <li>Use the search bar to find specific transactions by reference number or customer name</li>
                    <li>Filter transactions by date range to analyze time-based trends</li>
                    <li>Export transaction data for accounting and reporting purposes</li>
                    <li>Reconcile transactions regularly to ensure accuracy</li>
                    <li>Monitor revenue trends across different time periods</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
