import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

// Updated leadership team with only three executives
const leadershipTeam = [
  {
    id: 1,
    name: 'John Smith',
    title: 'Chief Executive Officer',
    image: '/images/executives/John Smith.jpeg',
    bio: 'With over 15 years of experience in the automotive industry, John has led ABC Cars to become Singapore\'s premier used car marketplace. His vision for transparent and accessible car buying has transformed the company into what it is today.'
  },
  {
    id: 2,
    name: 'Jane Doe',
    title: 'Chief Operations Officer',
    image: '/images/executives/Jane Doe.jpg',
    bio: 'Jane oversees daily operations at ABC Cars, ensuring seamless experiences for both buyers and sellers. Her background in logistics and e-commerce has helped create efficient systems that power our platform.'
  },
  {
    id: 3,
    name: 'Micheal Johnson',
    title: 'Chief Technology Officer',
    image: '/images/executives/Micheal Johnson.jpeg',
    bio: 'Micheal leads our technology initiatives, focusing on creating innovative solutions that make car buying easier. His team has developed our proprietary verification system that ensures all vehicles meet our quality standards.'
  }
];

// Core values
const coreValues = [
  {
    id: 1,
    title: 'Transparency',
    description: 'We believe in full disclosure about every vehicle on our platform, giving buyers the confidence they need.'
  },
  {
    id: 2,
    title: 'Customer-First',
    description: 'Every decision we make is guided by what provides the best experience for our customers.'
  },
  {
    id: 3,
    title: 'Quality',
    description: 'We maintain strict quality standards for all vehicles listed on our platform.'
  },
  {
    id: 4,
    title: 'Innovation',
    description: 'We continuously improve our services with new technologies and ideas.'
  }
];

// Benefits of choosing ABC Cars
const benefits = [
  {
    id: 1,
    title: 'Verified Vehicles',
    description: 'All cars undergo a 150-point inspection by certified mechanics before listing.'
  },
  {
    id: 2,
    title: 'Secure Transactions',
    description: 'Our escrow system ensures safe and secure transactions for both buyers and sellers.'
  },
  {
    id: 3,
    title: 'Extended Warranty',
    description: 'All purchases come with a 6-month warranty that covers major components.'
  },
  {
    id: 4,
    title: 'After-Sales Support',
    description: 'Our dedicated team provides support even after your purchase is complete.'
  }
];

export default function About() {
  return (
    <AppLayout>
      <Head title="About Us" />

      {/* Hero Section */}
      <div className="bg-primary py-12 md:py-20">
        <div className="container mx-auto px-6 text-center">
          <h1 className="text-4xl md:text-5xl font-bold text-white mb-6">About ABC Cars</h1>
          <p className="text-xl text-white max-w-3xl mx-auto">
            Singapore's trusted platform for buying and selling quality used cars since 2010.
          </p>
        </div>
      </div>

      {/* Our Story */}
      <div className="py-16 bg-white">
        <div className="container mx-auto px-6">
          <div className="max-w-3xl mx-auto text-center">
            <h2 className="text-3xl font-bold text-gray-800 mb-6">Our Story</h2>
            <p className="text-gray-700 mb-4">
              Founded in 2010, ABC Cars started with a simple mission: to make buying and selling used cars in Singapore transparent, fair, and stress-free. What began as a small operation has grown into the country's most trusted platform for quality used vehicles.
            </p>
            <p className="text-gray-700 mb-4">
              Our founder, John Smith, recognized the challenges that consumers faced in the used car market – from questionable vehicle conditions to opaque pricing. He established ABC Cars to address these pain points by implementing rigorous verification processes and a transparent bidding system.
            </p>
            <p className="text-gray-700">
              Today, we serve thousands of customers across Singapore, helping them find their perfect vehicle or sell their current one at the best possible price. We're proud of the trust we've built in the community and continue to innovate to provide the best service possible.
            </p>
          </div>
        </div>
      </div>

      {/* Leadership Team */}
      <div className="py-16 bg-gray-50">
        <div className="container mx-auto px-6">
          <h2 className="text-3xl font-bold text-center text-gray-800 mb-12">Our Leadership Team</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {leadershipTeam.map((leader) => (
              <div key={leader.id} className="card bg-base-100 shadow-xl">
                <figure className="px-6 pt-6">
                  <img
                    src={leader.image}
                    alt={leader.name}
                    className="rounded-lg h-60 w-full object-cover"
                    onError={(e) => {
                      const target = e.target as HTMLImageElement;
                      target.src = '/images/default-avatar.jpg';
                    }}
                  />
                </figure>
                <div className="card-body">
                  <h3 className="text-xl font-bold text-gray-800">{leader.name}</h3>
                  <p className="text-primary font-semibold">{leader.title}</p>
                  <p className="text-gray-700 mt-2">{leader.bio}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Mission and Vision */}
      <div className="py-16 bg-white">
        <div className="container mx-auto px-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
            {/* Mission */}
            <div className="card bg-base-100 shadow-lg border-t-4 border-primary">
              <div className="card-body">
                <h3 className="text-2xl font-bold text-gray-800 mb-4">Our Mission</h3>
                <p className="text-gray-700">
                  To revolutionize the used car market in Singapore by providing a platform that ensures transparency, quality, and fair pricing for all parties involved. We aim to make the process of buying and selling used cars as simple and stress-free as possible.
                </p>
              </div>
            </div>

            {/* Vision */}
            <div className="card bg-base-100 shadow-lg border-t-4 border-secondary">
              <div className="card-body">
                <h3 className="text-2xl font-bold text-gray-800 mb-4">Our Vision</h3>
                <p className="text-gray-700">
                  To be the most trusted and preferred platform for used car transactions in Singapore, known for our unwavering commitment to quality, customer satisfaction, and innovative services that continually exceed expectations.
                </p>
            </div>
            </div>
          </div>
        </div>
      </div>

      {/* Core Values */}
      <div className="py-16 bg-gray-50">
        <div className="container mx-auto px-6">
          <h2 className="text-3xl font-bold text-center text-gray-800 mb-12">Our Core Values</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {coreValues.map((value) => (
              <div key={value.id} className="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
              <div className="card-body">
                  <h3 className="text-xl font-bold text-gray-800 mb-2">{value.title}</h3>
                  <p className="text-gray-700">{value.description}</p>
                </div>
              </div>
            ))}
            </div>
        </div>
      </div>

      {/* Why Choose Us */}
      <div className="py-16 bg-white">
        <div className="container mx-auto px-6">
          <h2 className="text-3xl font-bold text-center text-gray-800 mb-12">Why Choose ABC Cars</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {benefits.map((benefit) => (
              <div key={benefit.id} className="flex gap-4">
                <div className="bg-primary rounded-full h-12 w-12 flex-shrink-0 flex items-center justify-center text-white">
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div>
                  <h3 className="text-xl font-bold text-gray-800 mb-2">{benefit.title}</h3>
                  <p className="text-gray-700">{benefit.description}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Call to Action */}
      <div className="bg-primary text-white py-16">
        <div className="container mx-auto px-6 text-center">
          <h2 className="text-3xl font-bold mb-4">Ready to Experience the ABC Cars Difference?</h2>
          <p className="text-lg mb-8 max-w-2xl mx-auto">
            Join thousands of satisfied customers who have found their perfect vehicle through our platform.
          </p>
          <a href={route('cars.index')} className="btn btn-secondary btn-lg">
            Explore Our Cars
          </a>
        </div>
      </div>
    </AppLayout>
  );
}
