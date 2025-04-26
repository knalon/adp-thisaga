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
  CogIcon
} from '@heroicons/react/24/outline';

interface AdminSidebarItem {
  name: string;
  href: string;
  icon: React.ElementType;
  current: boolean;
}

export default function AdminSettings({ auth }: PageProps) {
  const [activeSection, setActiveSection] = useState('settings');

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
      <Head title="Settings" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">Site Settings</h1>

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
                <h2 className="text-xl font-medium text-gray-900">System Settings</h2>
              </div>

              <div className="p-6">
                {/* Settings Form */}
                <form className="space-y-8">
                  {/* General Settings */}
                  <div className="space-y-6">
                    <h3 className="text-lg font-medium text-gray-900">General Settings</h3>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">Site Name</span>
                        </label>
                        <input type="text" defaultValue="ABC Cars" className="input input-bordered w-full" />
                      </div>

                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">Contact Email</span>
                        </label>
                        <input type="email" defaultValue="contact@abccars.com" className="input input-bordered w-full" />
                      </div>

                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">Contact Phone</span>
                        </label>
                        <input type="tel" defaultValue="+65 1234 5678" className="input input-bordered w-full" />
                      </div>

                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">Site Currency</span>
                        </label>
                        <select className="select select-bordered w-full">
                          <option>SGD ($)</option>
                          <option>USD ($)</option>
                          <option>EUR (€)</option>
                          <option>GBP (£)</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  {/* Payment Settings */}
                  <div className="space-y-6 pt-6 border-t border-gray-200">
                    <h3 className="text-lg font-medium text-gray-900">Payment Settings</h3>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">Payment Gateway</span>
                        </label>
                        <select className="select select-bordered w-full">
                          <option>Stripe</option>
                          <option>PayPal</option>
                          <option>Bank Transfer</option>
                        </select>
                      </div>

                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">Transaction Fee (%)</span>
                        </label>
                        <input type="number" defaultValue="2.5" className="input input-bordered w-full" />
                      </div>
                    </div>
                  </div>

                  {/* Email Settings */}
                  <div className="space-y-6 pt-6 border-t border-gray-200">
                    <h3 className="text-lg font-medium text-gray-900">Email Settings</h3>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">SMTP Server</span>
                        </label>
                        <input type="text" defaultValue="smtp.example.com" className="input input-bordered w-full" />
                      </div>

                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">SMTP Port</span>
                        </label>
                        <input type="number" defaultValue="587" className="input input-bordered w-full" />
                      </div>

                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">Email Username</span>
                        </label>
                        <input type="text" defaultValue="notifications@abccars.com" className="input input-bordered w-full" />
                      </div>

                      <div className="form-control w-full">
                        <label className="label">
                          <span className="label-text">Email Password</span>
                        </label>
                        <input type="password" defaultValue="●●●●●●●●●●" className="input input-bordered w-full" />
                      </div>
                    </div>
                  </div>

                  {/* Submit Button */}
                  <div className="pt-6 border-t border-gray-200">
                    <button type="submit" className="btn btn-primary">
                      Save Settings
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
