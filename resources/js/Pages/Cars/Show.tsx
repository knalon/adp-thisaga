import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Appointment, Car, PageProps } from '@/types';
import { Dialog, Transition } from '@headlessui/react';
import CarouselComponent from '@/Components/CarouselComponent';
import { ChevronDownIcon, ChevronUpIcon } from '@heroicons/react/24/outline';
import BidAndAppointment from '@/Components/BidAndAppointment';
import { Card } from '@/Components/ui/card';
import { Carousel } from '@/Components/ui/carousel';

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

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <Card className="overflow-hidden">
                <Carousel>
                  {carImages.map((image, index) => (
                    <img
                      key={index}
                      src={image}
                      alt={`${car.make} ${car.model} - Image ${index + 1}`}
                      className="w-full h-96 object-cover"
                    />
                  ))}
                </Carousel>
              </Card>

              <Card className="mt-8 p-6">
                <h1 className="text-3xl font-bold">{car.make} {car.model} ({car.registration_year})</h1>
                <div className="mt-4 space-y-2">
                  <p>
                    <span className="font-semibold">Brand:</span> {car.make}
                  </p>
                  <p>
                    <span className="font-semibold">Model:</span> {car.model}
                  </p>
                  <p>
                    <span className="font-semibold">Year:</span> {car.registration_year}
                  </p>
                  <p>
                    <span className="font-semibold">Price:</span> ${car.price.toLocaleString()}
                  </p>
                  <p>
                    <span className="font-semibold">Seller:</span> {car.user?.name}
                  </p>
                </div>
                <div className="mt-6">
                  <h2 className="text-xl font-semibold mb-2">Description</h2>
                  <p className="whitespace-pre-wrap">{car.description}</p>
                </div>
              </Card>
            </div>

            <div>
              <BidAndAppointment
                carId={car.id}
                currentUserId={auth.user?.id}
                carOwnerId={car.user_id}
              />
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
