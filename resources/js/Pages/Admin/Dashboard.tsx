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
  ChatBubbleLeftRightIcon
} from '@heroicons/react/24/outline';

interface AdminSidebarItem {
  name: string;
  href: string;
  icon: React.ElementType;
  current: boolean;
}

export default function AdminDashboard({ auth }: PageProps) {
  const [activeSection, setActiveSection] = useState('overview');

  // Admin navigation items with icons
  const adminSidebarItems: AdminSidebarItem[] = [
    { name: 'Overview', href: '/admin/dashboard', icon: ChartBarIcon, current: activeSection === 'overview' },
    { name: 'Manage Users', href: '/admin/users', icon: UsersIcon, current: activeSection === 'users' },
    { name: 'Manage Cars', href: '/admin/cars', icon: TruckIcon, current: activeSection === 'cars' },
    { name: 'Appointments', href: '/admin/appointments', icon: CalendarIcon, current: activeSection === 'appointments' },
    { name: 'Transactions', href: '/admin/transactions', icon: CreditCardIcon, current: activeSection === 'transactions' },
    { name: 'Contact Messages', href: '/admin/contact-messages', icon: ChatBubbleLeftRightIcon, current: activeSection === 'contact-messages' },
    { name: 'Settings', href: '/admin/settings', icon: CogIcon, current: activeSection === 'settings' },
  ];

  function classNames(...classes: string[]) {
    return classes.filter(Boolean).join(' ');
  }

  return (
    <AppLayout>
      <Head title="Admin Dashboard" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">Admin Dashboard</h1>

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
                        onClick={() => setActiveSection(item.name.toLowerCase())}
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
                <h2 className="text-xl font-medium text-gray-900">Overview</h2>
              </div>
              <div className="p-6">
                {/* Stats Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                  <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div className="flex items-center">
                      <div className="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <UsersIcon className="h-6 w-6" />
                      </div>
                      <div>
                        <p className="text-sm text-gray-500">Total Users</p>
                        <p className="text-2xl font-semibold">0</p>
                      </div>
                    </div>
                  </div>

                  <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div className="flex items-center">
                      <div className="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <TruckIcon className="h-6 w-6" />
                      </div>
                      <div>
                        <p className="text-sm text-gray-500">Active Cars</p>
                        <p className="text-2xl font-semibold">0</p>
                      </div>
                    </div>
                  </div>

                  <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div className="flex items-center">
                      <div className="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                        <CalendarIcon className="h-6 w-6" />
                      </div>
                      <div>
                        <p className="text-sm text-gray-500">Appointments</p>
                        <p className="text-2xl font-semibold">0</p>
                      </div>
                    </div>
                  </div>

                  <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div className="flex items-center">
                      <div className="p-3 rounded-full bg-indigo-100 text-indigo-500 mr-4">
                        <ChatBubbleLeftRightIcon className="h-6 w-6" />
                      </div>
                      <div>
                        <p className="text-sm text-gray-500">Contact Messages</p>
                        <p className="text-2xl font-semibold">0</p>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Quick Actions */}
                <h3 className="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                  <Link href="/admin/users/create" className="btn btn-primary">Add User</Link>
                  <Link href="/admin/cars" className="btn btn-secondary">Manage Cars</Link>
                  <Link href="/admin/contact-messages" className="btn btn-accent">View Messages</Link>
                  <Link href="/admin/settings" className="btn btn-outline">Site Settings</Link>
                </div>

                {/* Admin Guide */}
                <div className="bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
                  <div className="p-4 border-b border-gray-200">
                    <h3 className="text-lg font-medium text-gray-900">Admin Guide</h3>
                  </div>
                  <div className="p-4">
                    <ul className="divide-y divide-gray-200">
                      <li className="py-3">
                        <div className="flex items-center">
                          <div className="mr-4 flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <UsersIcon className="h-5 w-5 text-blue-500" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">Manage Users</p>
                            <p className="text-sm text-gray-500">Add, edit, or deactivate user accounts</p>
                            <Link href="/admin/users" className="btn btn-xs btn-primary mt-1">Manage Users</Link>
                          </div>
                        </div>
                      </li>
                      <li className="py-3">
                        <div className="flex items-center">
                          <div className="mr-4 flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                            <TruckIcon className="h-5 w-5 text-green-500" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">Manage Car Listings</p>
                            <p className="text-sm text-gray-500">Review and moderate car listings</p>
                            <Link href="/admin/cars" className="btn btn-xs btn-secondary mt-1">Manage Cars</Link>
                          </div>
                        </div>
                      </li>
                      <li className="py-3">
                        <div className="flex items-center">
                          <div className="mr-4 flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <ChatBubbleLeftRightIcon className="h-5 w-5 text-indigo-500" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">Contact Messages</p>
                            <p className="text-sm text-gray-500">View and manage customer inquiries</p>
                            <Link href="/admin/contact-messages" className="btn btn-xs btn-accent mt-1">View Messages</Link>
                          </div>
                        </div>
                      </li>
                      <li className="py-3">
                        <div className="flex items-center">
                          <div className="mr-4 flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <CalendarIcon className="h-5 w-5 text-yellow-500" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">Appointment Calendar</p>
                            <p className="text-sm text-gray-500">Manage test drive scheduling</p>
                            <Link href="/admin/appointments" className="btn btn-xs btn-accent mt-1">View Calendar</Link>
                          </div>
                        </div>
                      </li>
                      <li className="py-3">
                        <div className="flex items-center">
                          <div className="mr-4 flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <CreditCardIcon className="h-5 w-5 text-purple-500" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">View Transactions</p>
                            <p className="text-sm text-gray-500">Monitor and manage financial transactions</p>
                            <Link href="/admin/transactions" className="btn btn-xs btn-outline mt-1">View Reports</Link>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
 