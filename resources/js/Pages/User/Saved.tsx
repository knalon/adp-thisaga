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

export default function UserSaved({ auth }: PageProps) {
  const [activeSection, setActiveSection] = useState('saved');

  // User navigation items with icons
  const userSidebarItems: UserSidebarItem[] = [
    { name: 'Overview', href: '/user/dashboard', icon: UserIcon, current: activeSection === 'overview' },
    { name: 'My Cars', href: '/user/cars', icon: TruckIcon, current: activeSection === 'cars' },
    { name: 'My Appointments', href: '/user/appointments', icon: CalendarIcon, current: activeSection === 'appointments' },
    { name: 'Saved Cars', href: '/user/saved', icon: HeartIcon, current: activeSection === 'saved' },
    { name: 'Settings', href: '/profile', icon: CogIcon, current: activeSection === 'settings' },
  ];

  function classNames(...classes: string[]) {
    return classes.filter(Boolean).join(' ');
  }

  return (
    <AppLayout>
      <Head title="Saved Cars" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">Saved Cars</h1>

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
                <h2 className="text-xl font-medium text-gray-900">My Favorite Cars</h2>
              </div>

              <div className="p-6">
                {/* No saved cars state */}
                <div className="text-center py-12">
                  <HeartIcon className="h-16 w-16 text-gray-400 mx-auto mb-4" />
                  <h3 className="text-lg font-medium text-gray-900 mb-2">No saved cars yet</h3>
                  <p className="text-gray-500 mb-6">Save cars you're interested in to compare them later</p>
                  <Link href="/cars" className="btn btn-primary">
                    Browse Cars
                  </Link>
                </div>

                {/* Saved cars instructions */}
                <div className="bg-blue-50 p-4 rounded-lg mt-8">
                  <h3 className="text-lg font-medium text-blue-800 mb-2">How to Save Cars</h3>
                  <ul className="list-disc pl-5 text-blue-700 space-y-2">
                    <li>Browse available cars on our marketplace</li>
                    <li>Click the heart icon on any car you're interested in</li>
                    <li>Your saved cars will appear here for easy comparison</li>
                    <li>Remove cars from your saved list anytime</li>
                  </ul>
                  <div className="flex items-center mt-4 text-blue-700">
                    <HeartIcon className="h-5 w-5 mr-2" />
                    <p>Save up to 20 cars to compare their features and prices.</p>
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
