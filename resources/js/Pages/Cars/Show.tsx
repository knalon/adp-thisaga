import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Appointment, Car, PageProps } from '@/types';
import { Dialog, Transition } from '@headlessui/react';
import CarouselComponent from '@/Components/CarouselComponent';
import { ChevronDownIcon, ChevronUpIcon } from '@heroicons/react/24/outline';

interface Props extends Record<string, unknown> {
  car: Car;
  carImages: string[];
  currentBid: number | null;
  userAppointment: Appointment | null;
  bidHistory: {
    id: number;
    user_name: string;
    amount: number;
    created_at: string;
  }[];
  similarCars: (Car & { primary_image: string })[];
}

export default function Show({ car, carImages, auth, currentBid, userAppointment, bidHistory, similarCars }: PageProps<Props>) {
  const [activeImage, setActiveImage] = useState(carImages[0] || '/images/default-car.jpg');
  const isOwner = auth.user && car.user_id === auth.user.id;
  const isLoggedIn = !!auth.user;

  // Modal states
  const [appointmentModalOpen, setAppointmentModalOpen] = useState(false);
  const [bidModalOpen, setBidModalOpen] = useState(false);

  // Form states
  const [appointmentDate, setAppointmentDate] = useState('');
  const [appointmentNotes, setAppointmentNotes] = useState('');
  const [bidAmount, setBidAmount] = useState(currentBid ? (currentBid + 100).toString() : car.price.toString());
  const [formErrors, setFormErrors] = useState<Record<string, string>>({});

  const handleScheduleAppointment = () => {
    if (!isLoggedIn) {
      router.visit('/login');
      return;
    }

    setAppointmentModalOpen(true);
  };

  const handleSubmitAppointment = (e: React.FormEvent) => {
    e.preventDefault();

    router.post('/appointments', {
      car_id: car.id,
      appointment_date: appointmentDate,
      notes: appointmentNotes,
    }, {
      onSuccess: () => {
        setAppointmentModalOpen(false);
        setAppointmentDate('');
        setAppointmentNotes('');
      },
      onError: (errors) => {
        setFormErrors(errors);
      }
    });
  };

  const handleOpenBidModal = () => {
    if (!isLoggedIn) {
      router.visit('/login');
      return;
    }

    setBidModalOpen(true);
  };

  const handleSubmitBid = (e: React.FormEvent) => {
    e.preventDefault();

    if (!userAppointment) {
      setFormErrors({
        appointment: 'You need to schedule a test drive before submitting a bid.'
      });
      return;
    }

    router.post(`/appointments/${userAppointment.id}/submit-bid`, {
      bid_price: bidAmount,
    }, {
      onSuccess: () => {
        setBidModalOpen(false);
      },
      onError: (errors) => {
        setFormErrors(errors);
      }
    });
  };

  // Format date to local date string
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString();
  };

  // Format date to local date and time string
  const formatDateTime = (dateString: string) => {
    return new Date(dateString).toLocaleString();
  };

  return (
    <AppLayout>
      <Head title={`${car.make} ${car.model} - ${car.registration_year}`} />

      <div className="py-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {/* Breadcrumbs */}
          <nav className="flex mb-6" aria-label="Breadcrumb">
            <ol className="inline-flex items-center space-x-1 md:space-x-3">
              <li className="inline-flex items-center">
                <Link href="/" className="text-sm text-gray-700 hover:text-primary">
                  Home
                </Link>
              </li>
              <li>
                <div className="flex items-center">
                  <svg className="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m1 9 4-4-4-4"/>
                  </svg>
                  <Link href="/cars" className="text-sm text-gray-700 hover:text-primary ml-1 md:ml-2">
                    Cars
                  </Link>
                </div>
              </li>
              <li aria-current="page">
                <div className="flex items-center">
                  <svg className="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m1 9 4-4-4-4"/>
                  </svg>
                  <span className="text-sm text-gray-500 ml-1 md:ml-2">
                    {car.make} {car.model}
                  </span>
                </div>
              </li>
            </ol>
          </nav>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {/* Car Images */}
            <div>
              <div className="bg-white rounded-lg shadow-md overflow-hidden mb-4">
                <img
                  src={activeImage}
                  alt={`${car.make} ${car.model}`}
                  className="w-full h-96 object-cover"
                />
              </div>

              {carImages.length > 1 && (
                <div className="grid grid-cols-4 gap-2">
                  {carImages.map((image, index) => (
                    <div
                      key={index}
                      className={`border-2 rounded cursor-pointer ${activeImage === image ? 'border-primary' : 'border-gray-200'}`}
                      onClick={() => setActiveImage(image)}
                    >
                      <img
                        src={image}
                        alt={`${car.make} ${car.model} - view ${index + 1}`}
                        className="w-full h-20 object-cover"
                      />
                    </div>
                  ))}
                </div>
              )}
            </div>

            {/* Car Details */}
            <div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h1 className="text-3xl font-bold text-gray-900">
                  {car.make} {car.model} ({car.registration_year})
                </h1>

                <div className="flex items-center mt-2">
                  <span className="text-2xl font-bold text-primary">${car.price.toLocaleString()}</span>

                  {currentBid && (
                    <span className="ml-4 px-3 py-1 bg-secondary text-white rounded-full text-sm">
                      Current Highest Bid: ${currentBid.toLocaleString()}
                    </span>
                  )}
                </div>

                <div className="flex flex-wrap gap-2 mt-4">
                  {car.color && (
                    <span className="px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm">
                      Color: {car.color}
                    </span>
                  )}
                  {car.mileage && (
                    <span className="px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm">
                      Mileage: {car.mileage}
                    </span>
                  )}
                  {car.transmission && (
                    <span className="px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm">
                      Transmission: {car.transmission}
                    </span>
                  )}
                  {car.fuel_type && (
                    <span className="px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm">
                      Fuel Type: {car.fuel_type}
                    </span>
                  )}
                </div>

                <div className="mt-6">
                  <h2 className="text-xl font-semibold mb-2">Description</h2>
                  <p className="text-gray-700 whitespace-pre-line">{car.description}</p>
                </div>

                <div className="mt-6">
                  <h2 className="text-xl font-semibold mb-2">Seller Information</h2>
                  <p className="text-gray-700">Seller: {car.user?.name}</p>
                  <p className="text-gray-700">Listed on: {formatDate(car.created_at)}</p>
                </div>

                {/* User Appointment Status */}
                {isLoggedIn && !isOwner && userAppointment && (
                  <div className="mt-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <h2 className="text-lg font-semibold mb-2">Your Test Drive Appointment</h2>
                    <p className="text-gray-700">Date: {formatDateTime(userAppointment.appointment_date)}</p>
                    <p className="text-gray-700">Status:
                      <span className={`ml-2 px-2 py-0.5 rounded text-white ${
                        userAppointment.status === 'approved' ? 'bg-green-500' :
                        userAppointment.status === 'rejected' ? 'bg-red-500' :
                        userAppointment.status === 'completed' ? 'bg-blue-500' : 'bg-yellow-500'
                      }`}>
                        {userAppointment.status.charAt(0).toUpperCase() + userAppointment.status.slice(1)}
                      </span>
                    </p>
                    {userAppointment.bid_price && (
                      <p className="text-gray-700 mt-2">Your Bid: ${userAppointment.bid_price.toLocaleString()}</p>
                    )}
                  </div>
                )}

                {/* Action Buttons */}
                <div className="mt-8 flex flex-col sm:flex-row gap-4">
                  {isOwner ? (
                    <>
                      <Link
                        href={`/cars/${car.id}/edit`}
                        className="btn btn-primary flex-1"
                      >
                        Edit Listing
                      </Link>
                      <button
                        className="btn btn-outline btn-error flex-1"
                        onClick={() => {
                          if (confirm('Are you sure you want to deactivate this listing?')) {
                            router.patch(`/cars/${car.id}/deactivate`);
                          }
                        }}
                      >
                        Deactivate Listing
                      </button>
                    </>
                  ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                      <button
                        className="btn btn-primary"
                        onClick={handleScheduleAppointment}
                        disabled={isOwner || (userAppointment !== null)}
                      >
                        {userAppointment ? 'Test Drive Scheduled' : 'Schedule Test Drive'}
                      </button>
                      <button
                        className="btn btn-secondary"
                        onClick={handleOpenBidModal}
                        disabled={isOwner || !userAppointment || userAppointment.status !== 'approved'}
                      >
                        Submit Bid
                      </button>
                    </div>
                  )}
                </div>

                {/* Bid History Section */}
                {bidHistory && bidHistory.length > 0 && (
                  <div className="mt-8">
                    <h2 className="text-xl font-semibold mb-4">Bid History</h2>
                    <div className="overflow-x-auto">
                      <table className="table w-full">
                        <thead>
                          <tr>
                            <th>Bidder</th>
                            <th>Amount</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                          {bidHistory.map((bid) => (
                            <tr key={bid.id}>
                              <td>{bid.user_name}</td>
                              <td>${bid.amount.toLocaleString()}</td>
                              <td>{formatDateTime(bid.created_at)}</td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Similar Cars Section */}
      {similarCars && similarCars.length > 0 && (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
          <h2 className="text-2xl font-bold text-gray-900 mb-6">Similar Cars You Might Like</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {similarCars.map((similarCar) => (
              <Link
                key={similarCar.id}
                href={`/cars/${similarCar.id}`}
                className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
              >
                <div className="relative h-48">
                  {similarCar.primary_image ? (
                    <img
                      src={similarCar.primary_image.startsWith('http')
                        ? similarCar.primary_image
                        : `/storage/${similarCar.primary_image}`}
                      alt={`${similarCar.make} ${similarCar.model}`}
                      className="w-full h-full object-cover"
                    />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center bg-gray-200">
                      <span className="text-gray-400">No image available</span>
                    </div>
                  )}
                </div>
                <div className="p-4">
                  <h3 className="text-lg font-semibold text-gray-900">
                    {similarCar.make} {similarCar.model} ({similarCar.year})
                  </h3>
                  <div className="flex justify-between items-center mt-2">
                    <span className="text-xl font-bold text-primary">${Number(similarCar.price).toLocaleString()}</span>
                    {similarCar.mileage && (
                      <span className="text-sm text-gray-600">
                        {Number(similarCar.mileage).toLocaleString()} km
                      </span>
                    )}
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </div>
      )}

      {/* Test Drive Appointment Modal */}
      <Transition show={appointmentModalOpen} as={React.Fragment}>
        <Dialog
          as="div"
          className="fixed inset-0 z-10 overflow-y-auto"
          onClose={() => setAppointmentModalOpen(false)}
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
                  Schedule Test Drive
                </Dialog.Title>
                <form onSubmit={handleSubmitAppointment}>
                  <div className="mt-4">
                    <label htmlFor="appointment_date" className="block text-sm font-medium text-gray-700">
                      Preferred Date and Time
                    </label>
                    <input
                      type="datetime-local"
                      id="appointment_date"
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                      value={appointmentDate}
                      onChange={(e) => setAppointmentDate(e.target.value)}
                      min={new Date().toISOString().slice(0, 16)}
                      required
                    />
                    {formErrors.appointment_date && (
                      <p className="text-red-500 text-xs mt-1">{formErrors.appointment_date}</p>
                    )}
                  </div>

                  <div className="mt-4">
                    <label htmlFor="notes" className="block text-sm font-medium text-gray-700">
                      Notes (Optional)
                    </label>
                    <textarea
                      id="notes"
                      rows={3}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                      value={appointmentNotes}
                      onChange={(e) => setAppointmentNotes(e.target.value)}
                    ></textarea>
                  </div>

                  <div className="mt-6 flex justify-end gap-3">
                    <button
                      type="button"
                      className="btn btn-outline"
                      onClick={() => setAppointmentModalOpen(false)}
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      className="btn btn-primary"
                    >
                      Schedule Appointment
                    </button>
                  </div>
                </form>
              </div>
            </Transition.Child>
          </div>
        </Dialog>
      </Transition>

      {/* Bid Submission Modal */}
      <Transition show={bidModalOpen} as={React.Fragment}>
        <Dialog
          as="div"
          className="fixed inset-0 z-10 overflow-y-auto"
          onClose={() => setBidModalOpen(false)}
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
                  Submit Your Bid
                </Dialog.Title>
                {formErrors.appointment && (
                  <div className="mt-2 p-3 bg-red-50 text-red-700 rounded-md">
                    {formErrors.appointment}
                  </div>
                )}
                <form onSubmit={handleSubmitBid}>
                  <div className="mt-4">
                    <label htmlFor="bid_price" className="block text-sm font-medium text-gray-700">
                      Your Bid Amount ($)
                    </label>
                    <input
                      type="number"
                      id="bid_price"
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                      value={bidAmount}
                      onChange={(e) => setBidAmount(e.target.value)}
                      min={currentBid ? currentBid + 1 : car.price}
                      step="0.01"
                      required
                    />
                    {formErrors.bid_price && (
                      <p className="text-red-500 text-xs mt-1">{formErrors.bid_price}</p>
                    )}
                  </div>

                  {currentBid && (
                    <div className="mt-2 text-sm text-gray-500">
                      Current highest bid is ${currentBid.toLocaleString()}. Your bid must be higher.
                    </div>
                  )}

                  <div className="mt-6 flex justify-end gap-3">
                    <button
                      type="button"
                      className="btn btn-outline"
                      onClick={() => setBidModalOpen(false)}
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      className="btn btn-secondary"
                    >
                      Submit Bid
                    </button>
                  </div>
                </form>
              </div>
            </Transition.Child>
          </div>
        </Dialog>
      </Transition>
    </AppLayout>
  );
}
