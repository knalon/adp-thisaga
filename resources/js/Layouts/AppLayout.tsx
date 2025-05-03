import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { Disclosure, Menu, Transition } from '@headlessui/react';
import { Bars3Icon, XMarkIcon } from '@heroicons/react/24/outline';
import { PageProps } from '@/types';
import BanNotification from '@/Components/App/BanNotification';

interface NavigationItem {
  name: string;
  href: string;
  current: boolean;
}

interface UserNavigationItem {
  name: string;
  href: string;
  method?: string;
}

export default function AppLayout({ children }: { children: React.ReactNode }) {
  const { auth } = usePage<PageProps>().props;
  const userIsBanned = auth.isBanned;

  const isAdmin = auth?.user?.roles?.some(role => role.name === 'admin') ?? false;

  // Navigation for all users (authenticated and unauthenticated)
  const publicNavigation: NavigationItem[] = [
    { name: 'Cars', href: '/cars', current: route().current('cars.index') },
    { name: 'About Us', href: '/about', current: route().current('about') },
    { name: 'Contact Us', href: '/contact', current: route().current('contact') },
  ];

  // Profile dropdown items - for Admin
  const adminProfileNavigation: UserNavigationItem[] = [
    { name: 'Your Profile', href: '/profile' },
    { name: 'Admin Dashboard', href: '/admin/dashboard' },
    { name: 'Sign out', href: route('logout'), method: 'post' },
  ];

  // Profile dropdown items - for Regular User
  const userProfileNavigation: UserNavigationItem[] = [
    { name: 'Your Profile', href: '/profile' },
    { name: 'User Dashboard', href: '/dashboard' },
    { name: 'Sign out', href: route('logout'), method: 'post' },
  ];

  function classNames(...classes: string[]) {
    return classes.filter(Boolean).join(' ');
  }

  return (
    <div className="min-h-screen bg-base-100">
      <Disclosure as="nav" className="bg-primary">
        {({ open }) => (
          <>
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
              <div className="flex h-16 items-center justify-between">
                <div className="flex items-center">
                  <div className="flex-shrink-0">
                    <Link href="/">
                      <span className="text-xl font-bold">
                        <span className="text-secondary">A</span>
                        <span className="text-white">BC</span>
                        <span className="text-accent"> Cars</span>
                      </span>
                    </Link>
                  </div>
                  <div className="hidden md:block">
                    <div className="ml-10 flex items-baseline space-x-4">
                      {/* Public navigation available to all users */}
                      {publicNavigation.map((item) => (
                        <Link
                          key={item.name}
                          href={item.href}
                          className={classNames(
                            item.current
                              ? 'bg-primary-focus text-white'
                              : 'text-white hover:bg-primary-focus',
                            'rounded-md px-3 py-2 text-sm font-medium'
                          )}
                          aria-current={item.current ? 'page' : undefined}
                        >
                          {item.name}
                        </Link>
                      ))}
                    </div>
                  </div>
                </div>
                <div className="hidden md:block">
                  <div className="ml-4 flex items-center md:ml-6">
                    {/* Profile dropdown */}
                    {auth.user ? (
                      <Menu as="div" className="relative ml-3">
                        <div>
                          <Menu.Button className="flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                            <span className="sr-only">Open user menu</span>
                            <div className="h-8 w-8 rounded-full bg-secondary flex items-center justify-center text-white">
                              {auth.user.name.charAt(0)}
                            </div>
                          </Menu.Button>
                        </div>
                        <Transition
                          enter="transition ease-out duration-100"
                          enterFrom="transform opacity-0 scale-95"
                          enterTo="transform opacity-100 scale-100"
                          leave="transition ease-in duration-75"
                          leaveFrom="transform opacity-100 scale-100"
                          leaveTo="transform opacity-0 scale-95"
                        >
                          <Menu.Items className="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                            {isAdmin ? (
                              adminProfileNavigation.map((item) => (
                                <Menu.Item key={item.name}>
                                  {({ active }) => (
                                    <Link
                                      href={item.href}
                                      method={item.method}
                                      as={item.method ? 'button' : undefined}
                                      className={classNames(
                                        active ? 'bg-gray-100' : '',
                                        'block px-4 py-2 text-sm text-gray-700'
                                      )}
                                    >
                                      {item.name}
                                    </Link>
                                  )}
                                </Menu.Item>
                              ))
                            ) : (
                              userProfileNavigation.map((item) => (
                                <Menu.Item key={item.name}>
                                  {({ active }) => (
                                    <Link
                                      href={item.href}
                                      method={item.method}
                                      as={item.method ? 'button' : undefined}
                                      className={classNames(
                                        active ? 'bg-gray-100' : '',
                                        'block px-4 py-2 text-sm text-gray-700'
                                      )}
                                    >
                                      {item.name}
                                    </Link>
                                  )}
                                </Menu.Item>
                              ))
                            )}
                          </Menu.Items>
                        </Transition>
                      </Menu>
                    ) : (
                      <div className="flex space-x-4">
                        <Link
                          href="/login"
                          className="text-white hover:bg-primary-focus rounded-md px-3 py-2 text-sm font-medium"
                        >
                          Login
                        </Link>
                        <Link
                          href="/register"
                          className="bg-secondary text-white hover:bg-secondary-focus rounded-md px-3 py-2 text-sm font-medium"
                        >
                          Register
                        </Link>
                      </div>
                    )}
                  </div>
                </div>
                <div className="-mr-2 flex md:hidden">
                  {/* Mobile menu button */}
                  <Disclosure.Button className="inline-flex items-center justify-center rounded-md p-2 text-white hover:bg-primary-focus focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                    <span className="sr-only">Open main menu</span>
                    {open ? (
                      <XMarkIcon className="block h-6 w-6" aria-hidden="true" />
                    ) : (
                      <Bars3Icon className="block h-6 w-6" aria-hidden="true" />
                    )}
                  </Disclosure.Button>
                </div>
              </div>
            </div>

            <Disclosure.Panel className="md:hidden">
              <div className="space-y-1 px-2 pb-3 pt-2 sm:px-3">
                {/* Public navigation for mobile */}
                {publicNavigation.map((item) => (
                  <Disclosure.Button
                    key={item.name}
                    as={Link}
                    href={item.href}
                    className={classNames(
                      item.current
                        ? 'bg-primary-focus text-white'
                        : 'text-white hover:bg-primary-focus',
                      'block rounded-md px-3 py-2 text-base font-medium'
                    )}
                    aria-current={item.current ? 'page' : undefined}
                  >
                    {item.name}
                  </Disclosure.Button>
                ))}
              </div>

              <div className="border-t border-gray-700 pb-3 pt-4">
                {auth.user ? (
                  <div>
                    <div className="flex items-center px-5">
                      <div className="h-10 w-10 rounded-full bg-secondary flex items-center justify-center text-white">
                        {auth.user.name.charAt(0)}
                      </div>
                      <div className="ml-3">
                        <div className="text-base font-medium leading-none text-white">{auth.user.name}</div>
                        <div className="text-sm font-medium leading-none text-gray-300">{auth.user.email}</div>
                      </div>
                    </div>
                    <div className="mt-3 space-y-1 px-2">
                      {isAdmin ? (
                        adminProfileNavigation.map((item) => (
                          <Disclosure.Button
                            key={item.name}
                            as={Link}
                            href={item.href}
                            method={item.method}
                            className="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-primary-focus"
                          >
                            {item.name}
                          </Disclosure.Button>
                        ))
                      ) : (
                        userProfileNavigation.map((item) => (
                          <Disclosure.Button
                            key={item.name}
                            as={Link}
                            href={item.href}
                            method={item.method}
                            className="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-primary-focus"
                          >
                            {item.name}
                          </Disclosure.Button>
                        ))
                      )}
                    </div>
                  </div>
                ) : (
                  <div className="flex flex-col space-y-2 px-5">
                    <Link
                      href="/login"
                      className="text-white hover:bg-primary-focus rounded-md px-3 py-2 text-sm font-medium"
                    >
                      Login
                    </Link>
                    <Link
                      href="/register"
                      className="bg-secondary text-white hover:bg-secondary-focus rounded-md px-3 py-2 text-sm font-medium"
                    >
                      Register
                    </Link>
                  </div>
                )}
              </div>
            </Disclosure.Panel>
          </>
        )}
      </Disclosure>

      {userIsBanned && (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
          <BanNotification />
        </div>
      )}

      <main className="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        {children}
      </main>

      <footer className="bg-primary text-white py-8">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
              <h3 className="text-lg font-semibold mb-4">
                <span className="text-secondary">A</span>
                <span className="text-white">BC</span>
                <span className="text-accent"> Cars</span>
              </h3>
              <p className="text-sm">Your trusted partner in finding the perfect used car.</p>
            </div>
            <div>
              <h3 className="text-lg font-semibold mb-4">Quick Links</h3>
              <ul className="space-y-2">
                <li><Link href="/" className="text-sm hover:underline">Home</Link></li>
                <li><Link href="/cars" className="text-sm hover:underline">Browse Cars</Link></li>
                <li><Link href="/about" className="text-sm hover:underline">About Us</Link></li>
                <li><Link href="/contact" className="text-sm hover:underline">Contact Us</Link></li>
              </ul>
            </div>
            <div>
              <h3 className="text-lg font-semibold mb-4">Contact</h3>
              <p className="text-sm">123 Car Street, Singapore</p>
              <p className="text-sm">Phone: +65 1234 5678</p>
              <p className="text-sm">Email: info@abccars.com</p>
            </div>
          </div>
          <div className="mt-8 pt-8 border-t border-gray-700">
            <p className="text-sm text-center">© {new Date().getFullYear()} ABC Cars Pte Ltd. All rights reserved.</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
