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
  PlusCircleIcon
} from '@heroicons/react/24/outline';

interface AdminSidebarItem {
  name: string;
  href: string;
  icon: React.ElementType;
  current: boolean;
}

export default function AdminUsers({ auth }: PageProps) {
  const [activeSection, setActiveSection] = useState('users');

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
      <Head title="Manage Users" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">Manage Users</h1>

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
              <div className="p-6 border-b border-gray-200 flex justify-between items-center">
                <h2 className="text-xl font-medium text-gray-900">User Management</h2>
                <Link href="/admin/users/create" className="btn btn-primary btn-sm">
                  <PlusCircleIcon className="h-5 w-5 mr-1" />
                  Add User
                </Link>
              </div>

              <div className="p-6">
                {/* Search and filters */}
                <div className="flex flex-col md:flex-row md:items-center mb-6 gap-4">
                  <div className="flex-1">
                    <div className="form-control">
                      <div className="input-group">
                        <input type="text" placeholder="Search users..." className="input input-bordered w-full" />
                        <button className="btn btn-square">
                          <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div className="flex-shrink-0">
                    <select className="select select-bordered" defaultValue="">
                      <option value="" disabled>Role</option>
                      <option value="all">All</option>
                      <option value="admin">Admin</option>
                      <option value="user">User</option>
                    </select>
                  </div>
                  <div className="flex-shrink-0">
                    <select className="select select-bordered" defaultValue="">
                      <option value="" disabled>Status</option>
                      <option value="all">All</option>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                </div>

                {/* Users table */}
                <div className="overflow-x-auto">
                  <table className="table w-full">
                    {/* head */}
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      {/* No users state */}
                      <tr>
                        <td colSpan={6}>
                          <div className="text-center py-4">
                            <p className="text-gray-500">No users found</p>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                {/* User management tips */}
                <div className="bg-blue-50 p-4 rounded-lg mt-8">
                  <h3 className="text-lg font-medium text-blue-800 mb-2">User Management Guidelines</h3>
                  <ul className="list-disc pl-5 text-blue-700 space-y-2">
                    <li>Use the search bar to quickly find specific users</li>
                    <li>Filter users by role or status to narrow down results</li>
                    <li>Click on a user's name to view their complete profile</li>
                    <li>Use the edit button to update user information and permissions</li>
                    <li>Deactivate accounts instead of deleting them to maintain data integrity</li>
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
