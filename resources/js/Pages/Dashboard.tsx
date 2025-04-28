import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import {
  UserIcon,
  TruckIcon,
  CalendarIcon,
  HeartIcon,
  CogIcon
} from '@heroicons/react/24/outline';

interface UserSidebarItem {
  name: string;
  href: string;
  icon: React.ElementType;
  current: boolean;
}

export default function Dashboard({ auth }: PageProps) {
  const [activeSection, setActiveSection] = useState('overview');

  // User navigation items with icons
  const userSidebarItems: UserSidebarItem[] = [
    { name: 'Overview', href: '/dashboard', icon: UserIcon, current: activeSection === 'overview' },
    { name: 'My Cars', href: '/dashboard/cars', icon: TruckIcon, current: activeSection === 'cars' },
    { name: 'My Appointments', href: '/appointments', icon: CalendarIcon, current: activeSection === 'appointments' },
    { name: 'Saved Cars', href: '/dashboard/saved', icon: HeartIcon, current: activeSection === 'saved' },
    { name: 'Settings', href: '/profile', icon: CogIcon, current: activeSection === 'settings' },
  ];

  function classNames(...classes: string[]) {
    return classes.filter(Boolean).join(' ');
  }

    return (
    <AppLayout>
            <Head title="Dashboard" />

            <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">User Dashboard</h1>

          <div className="flex flex-col md:flex-row gap-6">
            {/* Sidebar */}
            <div className="w-full md:w-64 bg-white shadow rounded-lg overflow-hidden">
              <div className="p-6 bg-primary text-white">
                <h2 className="text-xl font-semibold">My Account</h2>
              </div>
              <nav className="mt-2 p-2">
                <ul className="space-y-1">
                  {userSidebarItems.map((item) => (
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
                {/* Welcome Message */}
                <div className="bg-blue-50 p-4 rounded-lg mb-6">
                  <div className="flex">
                    <div className="flex-shrink-0">
                      <div className="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <UserIcon className="h-6 w-6 text-blue-600" />
                      </div>
                    </div>
                    <div className="ml-4">
                      <h3 className="text-lg font-medium text-blue-600">Welcome, {auth.user.name}!</h3>
                      <p className="mt-1 text-blue-500">
                        This is your personal dashboard where you can manage your cars, appointments, and account settings.
                      </p>
                    </div>
                  </div>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                  <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div className="flex items-center">
                      <div className="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <TruckIcon className="h-6 w-6" />
                      </div>
                      <div>
                        <p className="text-sm text-gray-500">My Cars</p>
                        <p className="text-2xl font-semibold">2</p>
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
                        <p className="text-2xl font-semibold">1</p>
                      </div>
                    </div>
                  </div>

                  <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div className="flex items-center">
                      <div className="p-3 rounded-full bg-red-100 text-red-500 mr-4">
                        <HeartIcon className="h-6 w-6" />
                      </div>
                      <div>
                        <p className="text-sm text-gray-500">Saved Cars</p>
                        <p className="text-2xl font-semibold">5</p>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Recent Activity */}
                <div className="bg-white rounded-lg border border-gray-200 shadow-sm">
                  <div className="p-4 border-b border-gray-200">
                    <h3 className="text-lg font-medium text-gray-900">Recent Activity</h3>
                  </div>
                  <div className="p-4">
                    <ul className="divide-y divide-gray-200">
                      <li className="py-3">
                        <div className="flex items-center">
                          <div className="mr-4 flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                            <TruckIcon className="h-5 w-5 text-green-500" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">You listed a new car: Toyota Camry 2019</p>
                            <p className="text-sm text-gray-500">2 days ago</p>
                          </div>
                        </div>
                      </li>
                      <li className="py-3">
                        <div className="flex items-center">
                          <div className="mr-4 flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <CalendarIcon className="h-5 w-5 text-yellow-500" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">Scheduled appointment for BMW X5</p>
                            <p className="text-sm text-gray-500">1 week ago</p>
                          </div>
                        </div>
                      </li>
                      <li className="py-3">
                        <div className="flex items-center">
                          <div className="mr-4 flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                            <HeartIcon className="h-5 w-5 text-red-500" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">Saved a car to favorites: Honda Civic</p>
                            <p className="text-sm text-gray-500">2 weeks ago</p>
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
