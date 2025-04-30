import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import {
  UserIcon,
  TruckIcon,
  CalendarIcon,
  HeartIcon,
  CogIcon,
  PlusCircleIcon,
  CurrencyDollarIcon,
  CheckCircleIcon,
  ShoppingCartIcon
} from '@heroicons/react/24/outline';

interface UserSidebarItem {
  name: string;
  href: string;
  icon: React.ElementType;
  current: boolean;
}

export default function UserCars({ auth }: PageProps) {
  const [activeSection, setActiveSection] = useState('cars');

  // User navigation items with icons
  const userSidebarItems: UserSidebarItem[] = [
    { name: 'Overview', href: '/user/dashboard', icon: UserIcon, current: activeSection === 'overview' },
    { name: 'My Cars', href: '/user/cars', icon: TruckIcon, current: activeSection === 'cars' },
    { name: 'My Appointments', href: '/user/appointments', icon: CalendarIcon, current: activeSection === 'appointments' },
    { name: 'My Bids', href: '/user/bids', icon: CurrencyDollarIcon, current: activeSection === 'bids' },
    { name: 'My Sold Cars', href: '/user/sold-cars', icon: CheckCircleIcon, current: activeSection === 'sold-cars' },
    { name: 'My Purchased Cars', href: '/user/purchased-cars', icon: ShoppingCartIcon, current: activeSection === 'purchased-cars' },
    { name: 'Saved Cars', href: '/user/saved', icon: HeartIcon, current: activeSection === 'saved' },
    { name: 'Settings', href: '/profile', icon: CogIcon, current: activeSection === 'settings' },
  ];

  function classNames(...classes: string[]) {
    return classes.filter(Boolean).join(' ');
  }

  return (
    <AppLayout>
      <Head title="My Cars" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">My Cars</h1>

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
              <div className="p-6 border-b border-gray-200 flex justify-between items-center">
                <h2 className="text-xl font-medium text-gray-900">My Listed Cars</h2>
                <Link href="/user/cars/create" className="btn btn-primary btn-sm">
                  <PlusCircleIcon className="h-5 w-5 mr-1" />
                  Add New Car
                </Link>
              </div>

              <div className="p-6">
                {/* No cars state */}
                <div className="text-center py-12">
                  <TruckIcon className="h-16 w-16 text-gray-400 mx-auto mb-4" />
                  <h3 className="text-lg font-medium text-gray-900 mb-2">No cars listed yet</h3>
                  <p className="text-gray-500 mb-6">Start selling your car by creating your first listing</p>
                  <Link href="/user/cars/create" className="btn btn-primary">
                    <PlusCircleIcon className="h-5 w-5 mr-2" />
                    List Your Car
                  </Link>
                </div>

                {/* Car listing instructions */}
                <div className="bg-blue-50 p-4 rounded-lg mt-8">
                  <h3 className="text-lg font-medium text-blue-800 mb-2">How to list your car</h3>
                  <ol className="list-decimal pl-5 text-blue-700 space-y-2">
                    <li>Click on "List Your Car" button above</li>
                    <li>Fill in your car details including make, model, year, and price</li>
                    <li>Upload clear photos of your vehicle</li>
                    <li>Add a detailed description highlighting key features</li>
                    <li>Submit your listing for review</li>
                  </ol>
                  <p className="mt-3 text-blue-700">Our team will review your listing within 24 hours.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
