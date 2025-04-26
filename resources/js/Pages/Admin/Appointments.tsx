import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Appointment, PageProps } from '@/types';
import { Dialog, Transition } from '@headlessui/react';
import {
  UsersIcon,
  TruckIcon,
  CalendarIcon,
  CreditCardIcon,
  ChartBarIcon,
  CogIcon,
  PlusCircleIcon
} from '@heroicons/react/24/outline';

// Update the Props interface to satisfy Record<string, unknown>
interface Props extends Record<string, unknown> {
  pendingAppointments: {
    data: Appointment[];
    links: any[];
  };
  approvedAppointments: {
    data: Appointment[];
    links: any[];
  };
  rejectedAppointments: {
    data: Appointment[];
    links: any[];
  };
  biddedAppointments: {
    data: Appointment[];
    links: any[];
  };
}

interface AdminSidebarItem {
  name: string;
  href: string;
  icon: React.ElementType;
  current: boolean;
}

export default function AdminAppointments({ pendingAppointments, approvedAppointments, rejectedAppointments, biddedAppointments }: PageProps<Props>) {
  const [activeTab, setActiveTab] = useState('pending');
  const [finalizeModal, setFinalizeModal] = useState(false);
  const [selectedAppointment, setSelectedAppointment] = useState<Appointment | null>(null);
  const [finalPrice, setFinalPrice] = useState('');
  const [formErrors, setFormErrors] = useState<Record<string, string>>({});

  // Initialize appointments with default empty values
  const pending = pendingAppointments?.data || [];
  const approved = approvedAppointments?.data || [];
  const rejected = rejectedAppointments?.data || [];
  const bidded = biddedAppointments?.data || [];

  const handleApprove = (appointment: Appointment) => {
    if (confirm('Are you sure you want to approve this appointment?')) {
      router.patch(route('admin.appointments.approve', appointment.id), {}, {
        onSuccess: () => {
          // No need to do anything, page will be refreshed automatically
        }
      });
    }
  };

  const handleReject = (appointment: Appointment) => {
    if (confirm('Are you sure you want to reject this appointment?')) {
      router.patch(route('admin.appointments.reject', appointment.id), {}, {
        onSuccess: () => {
          // No need to do anything, page will be refreshed automatically
        }
      });
    }
  };

  const openFinalizeModal = (appointment: Appointment) => {
    setSelectedAppointment(appointment);
    setFinalPrice(appointment.bid_price?.toString() || '');
    setFinalizeModal(true);
  };

  const handleFinalize = (e: React.FormEvent) => {
    e.preventDefault();

    if (!selectedAppointment) return;

    router.post(route('admin.transactions.finalize', selectedAppointment.id), {
      final_price: finalPrice,
    }, {
      onSuccess: () => {
        setFinalizeModal(false);
        setSelectedAppointment(null);
        setFinalPrice('');
      },
      onError: (errors) => {
        setFormErrors(errors);
      }
    });
  };

  // Format date to local date and time string
  const formatDateTime = (dateString: string) => {
    return new Date(dateString).toLocaleString();
  };

  return (
    <AppLayout>
      <Head title="Manage Appointments" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">Manage Appointments</h1>

          {/* Tabs */}
          <div className="tabs tabs-boxed mb-6">
            <a
              className={`tab ${activeTab === 'pending' ? 'tab-active' : ''}`}
              onClick={() => setActiveTab('pending')}
            >
              Pending
              <span className="ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded-full">
                {pending.length}
              </span>
            </a>
            <a
              className={`tab ${activeTab === 'approved' ? 'tab-active' : ''}`}
              onClick={() => setActiveTab('approved')}
            >
              Approved
            </a>
            <a
              className={`tab ${activeTab === 'bids' ? 'tab-active' : ''}`}
              onClick={() => setActiveTab('bids')}
            >
              Bids
              <span className="ml-2 px-2 py-1 bg-secondary text-white text-xs rounded-full">
                {bidded.length}
              </span>
            </a>
            <a
              className={`tab ${activeTab === 'rejected' ? 'tab-active' : ''}`}
              onClick={() => setActiveTab('rejected')}
            >
              Rejected
            </a>
          </div>

          {/* Pending Appointments Table */}
          {activeTab === 'pending' && (
            <div className="bg-white shadow rounded-lg overflow-hidden">
              <div className="p-6 bg-white border-b border-gray-200">
                <h2 className="text-lg font-medium text-gray-900">Pending Appointments</h2>
              </div>
              <div className="overflow-x-auto">
                <table className="table w-full">
                  <thead>
                    <tr>
                      <th>Car</th>
                      <th>User</th>
                      <th>Date</th>
                      <th>Notes</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {pending.length > 0 ? (
                      pending.map((appointment) => (
                        <tr key={appointment.id}>
                          <td>
                            <Link href={route('cars.show', appointment.car?.slug)} className="hover:underline">
                              {appointment.car?.make} {appointment.car?.model} ({appointment.car?.registration_year})
                            </Link>
                          </td>
                          <td>{appointment.user?.name}</td>
                          <td>{formatDateTime(appointment.appointment_date)}</td>
                          <td>{appointment.notes || 'N/A'}</td>
                          <td className="flex space-x-2">
                            <button
                              onClick={() => handleApprove(appointment)}
                              className="btn btn-sm btn-success"
                            >
                              Approve
                            </button>
                            <button
                              onClick={() => handleReject(appointment)}
                              className="btn btn-sm btn-error"
                            >
                              Reject
                            </button>
                          </td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan={5} className="text-center py-4">No pending appointments</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* Approved Appointments Table */}
          {activeTab === 'approved' && (
            <div className="bg-white shadow rounded-lg overflow-hidden">
              <div className="p-6 bg-white border-b border-gray-200">
                <h2 className="text-lg font-medium text-gray-900">Approved Appointments</h2>
              </div>
              <div className="overflow-x-auto">
                <table className="table w-full">
                  <thead>
                    <tr>
                      <th>Car</th>
                      <th>User</th>
                      <th>Date</th>
                      <th>Notes</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    {approved.length > 0 ? (
                      approved.map((appointment) => (
                        <tr key={appointment.id}>
                          <td>
                            <Link href={route('cars.show', appointment.car?.slug)} className="hover:underline">
                              {appointment.car?.make} {appointment.car?.model} ({appointment.car?.registration_year})
                            </Link>
                          </td>
                          <td>{appointment.user?.name}</td>
                          <td>{formatDateTime(appointment.appointment_date)}</td>
                          <td>{appointment.notes || 'N/A'}</td>
                          <td>
                            <span className="badge badge-success">Approved</span>
                          </td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan={5} className="text-center py-4">No approved appointments</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* Bids Table */}
          {activeTab === 'bids' && (
            <div className="bg-white shadow rounded-lg overflow-hidden">
              <div className="p-6 bg-white border-b border-gray-200">
                <h2 className="text-lg font-medium text-gray-900">Bids</h2>
                <p className="text-sm text-gray-500 mt-1">Review and finalize bids for car purchases</p>
              </div>
              <div className="overflow-x-auto">
                <table className="table w-full">
                  <thead>
                    <tr>
                      <th>Car</th>
                      <th>Bidder</th>
                      <th>Bid Amount</th>
                      <th>Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {bidded.length > 0 ? (
                      bidded.map((appointment) => (
                        <tr key={appointment.id}>
                          <td>
                            <Link href={route('cars.show', appointment.car?.slug)} className="hover:underline">
                              {appointment.car?.make} {appointment.car?.model} ({appointment.car?.registration_year})
                            </Link>
                            <div className="text-sm text-gray-500">Listed price: ${appointment.car?.price.toLocaleString()}</div>
                          </td>
                          <td>{appointment.user?.name}</td>
                          <td>
                            <span className="font-bold text-secondary">
                              ${appointment.bid_price?.toLocaleString()}
                            </span>
                          </td>
                          <td>{formatDateTime(appointment.updated_at)}</td>
                          <td>
                            <button
                              onClick={() => openFinalizeModal(appointment)}
                              className="btn btn-sm btn-primary"
                            >
                              Finalize
                            </button>
                          </td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan={5} className="text-center py-4">No bids available</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* Rejected Appointments Table */}
          {activeTab === 'rejected' && (
            <div className="bg-white shadow rounded-lg overflow-hidden">
              <div className="p-6 bg-white border-b border-gray-200">
                <h2 className="text-lg font-medium text-gray-900">Rejected Appointments</h2>
              </div>
              <div className="overflow-x-auto">
                <table className="table w-full">
                  <thead>
                    <tr>
                      <th>Car</th>
                      <th>User</th>
                      <th>Date</th>
                      <th>Notes</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    {rejected.length > 0 ? (
                      rejected.map((appointment) => (
                        <tr key={appointment.id}>
                          <td>
                            <Link href={route('cars.show', appointment.car?.slug)} className="hover:underline">
                              {appointment.car?.make} {appointment.car?.model} ({appointment.car?.registration_year})
                            </Link>
                          </td>
                          <td>{appointment.user?.name}</td>
                          <td>{formatDateTime(appointment.appointment_date)}</td>
                          <td>{appointment.notes || 'N/A'}</td>
                          <td>
                            <span className="badge badge-error">Rejected</span>
                          </td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan={5} className="text-center py-4">No rejected appointments</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Finalize Bid Modal */}
      <Transition show={finalizeModal} as={React.Fragment}>
        <Dialog
          as="div"
          className="fixed inset-0 z-10 overflow-y-auto"
          onClose={() => setFinalizeModal(false)}
        >
          <div className="min-h-screen px-4 text-center">
            <Transition.Child
              as={React.Fragment}
              enter="ease-out duration-300"
              enterFrom="opacity-0"
              enterTo="opacity-100"
              leave="ease-in duration-200"
              leaveFrom="opacity-100"
              leaveTo="opacity-0"
            >
              <div className="fixed inset-0 bg-black opacity-30" />
            </Transition.Child>

            <span
              className="inline-block h-screen align-middle"
              aria-hidden="true"
            >
              &#8203;
            </span>

            <Transition.Child
              as={React.Fragment}
              enter="ease-out duration-300"
              enterFrom="opacity-0 scale-95"
              enterTo="opacity-100 scale-100"
              leave="ease-in duration-200"
              leaveFrom="opacity-100 scale-100"
              leaveTo="opacity-0 scale-95"
            >
              <div className="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <Dialog.Title
                  as="h3"
                  className="text-lg font-medium leading-6 text-gray-900"
                >
                  Finalize Transaction
                </Dialog.Title>

                {selectedAppointment && (
                  <div className="mt-2">
                    <p className="text-sm text-gray-500">
                      You are about to finalize the transaction for:
                    </p>
                    <p className="font-medium mt-1">
                      {selectedAppointment.car?.make} {selectedAppointment.car?.model} ({selectedAppointment.car?.registration_year})
                    </p>
                    <p className="text-sm mt-1">
                      <span className="font-medium">Bidder:</span> {selectedAppointment.user?.name}
                    </p>
                    <p className="text-sm mt-1">
                      <span className="font-medium">Bid Amount:</span> ${selectedAppointment.bid_price?.toLocaleString()}
                    </p>

                    <form onSubmit={handleFinalize} className="mt-4">
                      <div>
                        <label htmlFor="final_price" className="block text-sm font-medium text-gray-700">
                          Final Price ($)
                        </label>
                        <input
                          type="number"
                          id="final_price"
                          className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                          value={finalPrice}
                          onChange={(e) => setFinalPrice(e.target.value)}
                          min={0}
                          step="0.01"
                          required
                        />
                        {formErrors.final_price && (
                          <p className="text-red-500 text-xs mt-1">{formErrors.final_price}</p>
                        )}
                      </div>

                      <div className="mt-6 flex justify-end gap-3">
                        <button
                          type="button"
                          className="btn btn-outline"
                          onClick={() => setFinalizeModal(false)}
                        >
                          Cancel
                        </button>
                        <button
                          type="submit"
                          className="btn btn-primary"
                        >
                          Finalize Transaction
                        </button>
                      </div>
                    </form>
                  </div>
                )}
              </div>
            </Transition.Child>
          </div>
        </Dialog>
      </Transition>
    </AppLayout>
  );
}
