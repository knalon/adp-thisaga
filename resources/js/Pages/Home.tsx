import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Car, PageProps } from '@/types';

interface HomeProps extends PageProps {
  featuredCars: Car[];
}

export default function Home({ auth, featuredCars }: HomeProps) {
  // Testimonials data with updated images
  const testimonials = [
    {
      id: 1,
      name: 'Sarah Thompson',
      avatar: '/images/What our customers say/Sarah Thompson.jpeg',
      text: 'Found my dream car on ABC Cars! The process was smooth from viewing to test drive to bid acceptance.',
    },
    {
      id: 2,
      name: 'Micheal Brown',
      avatar: '/images/What our customers say/Micheal Brown.jpeg',
      text: 'The variety of cars available is amazing. The bidding system makes buying transparent and fair.',
    },
    {
      id: 3,
      name: 'Emily Chen',
      avatar: '/images/What our customers say/Emily Chen.jpeg',
      text: 'As a first-time car buyer, ABC Cars made the process easy to understand. Great customer service!',
    },
    {
      id: 4,
      name: 'David Rodriguez',
      avatar: '/images/What our customers say/David Rodriguez.jpeg',
      text: 'I sold my car through ABC Cars and received fair offers quickly. Would definitely use again!',
    },
    {
      id: 5,
      name: 'Priya Patel',
      avatar: '/images/What our customers say/Priya Patel.jpeg',
      text: 'The scheduling system for test drives is convenient. I appreciated the flexibility offered.',
    },
  ];

  return (
    <AppLayout>
      <Head title="Home" />

      {/* Hero Section */}
      <div
        className="relative py-16 md:py-32 bg-cover bg-center"
        style={{
          backgroundImage: 'url(/images/Home-background-1.jpg)',
        }}
      >
        <div className="absolute inset-0 bg-black opacity-50"></div>
        <div className="relative container mx-auto px-6 text-center">
          <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
            Welcome to ABC Cars
          </h1>
          <p className="text-xl text-white mb-8">
            Find your perfect car today!
          </p>
          <div className="flex flex-col sm:flex-row justify-center gap-4">
            {!auth.user && (
              <Link
                href={route('register')}
                className="btn btn-primary btn-lg"
              >
                Join Us
              </Link>
            )}
            <Link
              href={route('cars.index')}
              className="btn btn-secondary btn-lg"
            >
              Explore Cars
            </Link>
          </div>
        </div>
      </div>

      {/* Featured Cars Section */}
      <div className="py-16 bg-gray-50">
        <div className="container mx-auto px-6">
          <h2 className="text-3xl font-bold text-center mb-2">Featured Cars</h2>
          <p className="text-center text-gray-600 mb-10">Discover our selection of quality vehicles</p>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {featuredCars && featuredCars.length > 0 ? (
              featuredCars.map((car) => (
                <div key={car.id} className="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                  <figure className="h-48">
                    {car.media && car.media[0] ? (
                      <img
                        src={car.media[0].original_url}
                        alt={`${car.make} ${car.model}`}
                        className="w-full h-full object-cover"
                        onError={(e) => {
                          const target = e.target as HTMLImageElement;
                          target.src = '/images/default-car.jpg';
                        }}
                      />
                    ) : (
                      <img
                        src="/images/default-car.jpg"
                        alt={`${car.make} ${car.model}`}
                        className="w-full h-full object-cover"
                      />
                    )}
                  </figure>
                  <div className="card-body">
                    <h3 className="card-title">
                      {car.make} {car.model}
                      <span className="badge badge-secondary">{car.registration_year}</span>
                    </h3>
                    <p className="text-xl font-bold text-primary">${car.price.toLocaleString()}</p>
                    <div className="card-actions justify-end mt-4">
                      <Link
                        href={route('cars.show', car.slug)}
                        className="btn btn-primary btn-sm"
                      >
                        View Details
                      </Link>
                    </div>
                  </div>
                </div>
              ))
            ) : (
              <div className="col-span-full text-center py-12">
                <p className="text-gray-500">No featured cars available at the moment.</p>
              </div>
            )}
          </div>

          <div className="text-center mt-10">
            <Link href={route('cars.index')} className="btn btn-outline btn-primary">
              View All Cars
            </Link>
          </div>
        </div>
      </div>

      {/* How It Works Section */}
      <div className="py-16 bg-white">
        <div className="container mx-auto px-6">
          <h2 className="text-3xl font-bold text-center mb-2">How It Works</h2>
          <p className="text-center text-gray-600 mb-12">Three easy steps to find your perfect car</p>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {/* Step 1 */}
            <div className="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
              <div className="card-body text-center">
                <div className="bg-primary w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl mx-auto mb-4">1</div>
                <h3 className="text-xl font-bold mb-2">Search for Cars</h3>
                <p className="text-gray-600">
                  Browse our extensive collection by make, model, registration year, and price range to find your ideal match.
                </p>
              </div>
            </div>

            {/* Step 2 */}
            <div className="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
              <div className="card-body text-center">
                <div className="bg-primary w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl mx-auto mb-4">2</div>
                <h3 className="text-xl font-bold mb-2">Schedule a Test Drive</h3>
                <p className="text-gray-600">
                  Book an appointment directly through our platform to experience your chosen car firsthand.
                </p>
              </div>
            </div>

            {/* Step 3 */}
            <div className="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
              <div className="card-body text-center">
                <div className="bg-primary w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl mx-auto mb-4">3</div>
                <h3 className="text-xl font-bold mb-2">Seal the Deal</h3>
                <p className="text-gray-600">
                  Make bids or buy at transparent prices. Our system ensures fair transactions for both buyers and sellers.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Customer Reviews Section */}
      <div className="py-16 bg-gray-50">
        <div className="container mx-auto px-6">
          <h2 className="text-3xl font-bold text-center mb-2">What Our Customers Say</h2>
          <p className="text-center text-gray-600 mb-12">Hear from our satisfied customers</p>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {testimonials.map((testimonial) => (
              <div key={testimonial.id} className="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
                <div className="card-body">
                  <div className="flex items-center mb-4">
                    <div className="avatar mr-4">
                      <div className="w-12 h-12 rounded-full">
                        <img
                          src={testimonial.avatar}
                          alt={testimonial.name}
                          onError={(e) => {
                            const target = e.target as HTMLImageElement;
                            target.src = '/images/default-avatar.jpg';
                          }}
                        />
                      </div>
                    </div>
                    <div>
                      <h3 className="font-bold">{testimonial.name}</h3>
                      <div className="flex">
                        {[...Array(5)].map((_, i) => (
                          <svg key={i} xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                        ))}
                      </div>
                    </div>
                  </div>
                  <p className="text-gray-600 italic">"{testimonial.text}"</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* CTA Section */}
      <div className="bg-primary text-white py-16">
        <div className="container mx-auto px-6 text-center">
          <h2 className="text-3xl font-bold mb-4">Ready to Find Your Dream Car?</h2>
          <p className="text-lg mb-8 max-w-2xl mx-auto">
            Join thousands of satisfied customers who have found their perfect vehicle through ABC Cars.
          </p>
          <div className="flex flex-col sm:flex-row justify-center gap-4">
            <Link href={route('cars.index')} className="btn btn-secondary btn-lg">
              Browse Cars
            </Link>
            {!auth.user && (
              <Link href={route('register')} className="btn bg-white text-primary hover:bg-gray-100 btn-lg">
                Sign Up Now
              </Link>
            )}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
