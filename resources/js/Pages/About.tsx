import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

// Leadership team data
const leadershipTeam = [
  {
    name: 'Jane Smith',
    role: 'CEO & Founder',
    image: '/images/team/jane-smith.jpg',
    bio: 'With over 15 years in automotive retail, Jane founded ABC Cars with a vision to transform how people buy and sell vehicles online.'
  },
  {
    name: 'Michael Johnson',
    role: 'CTO',
    image: '/images/team/michael-johnson.jpg',
    bio: 'Michael leads our technology team, bringing 12 years of experience in building secure and scalable e-commerce platforms.'
  },
  {
    name: 'Sarah Wilson',
    role: 'Customer Experience Director',
    image: '/images/team/sarah-wilson.jpg',
    bio: 'Sarah ensures every customer interaction exceeds expectations, from browsing to post-purchase support.'
  },
  {
    name: 'David Lee',
    role: 'Head of Vehicle Operations',
    image: '/images/team/david-lee.jpg',
    bio: 'David manages our vehicle inspection process, ensuring every car listed meets our rigorous quality standards.'
  },
  {
    name: 'Amanda Chen',
    role: 'Marketing Director',
    image: '/images/team/amanda-chen.jpg',
    bio: 'Amanda drives our brand strategy and digital marketing initiatives, helping connect buyers with their perfect vehicles.'
  },
  {
    name: 'Robert Taylor',
    role: 'Finance Manager',
    image: '/images/team/robert-taylor.jpg',
    bio: 'Robert oversees our financing options and partnerships, making vehicle ownership accessible to more customers.'
  }
];

// Benefits data
const benefits = [
  {
    title: 'Comprehensive Listings',
    description: 'Browse thousands of verified vehicles with detailed specifications, high-quality images, and transparent pricing information.'
  },
  {
    title: 'Secure Transactions',
    description: 'Our platform ensures safe, secure payments with buyer protection and secure data handling for complete peace of mind.'
  },
  {
    title: 'Customer Support',
    description: 'Our dedicated support team is available 7 days a week to assist with any questions about vehicles, financing, or using our platform.'
  }
];

export default function About() {
  return (
    <AppLayout>
      <Head title="About Us | ABC Cars" />

      {/* Hero Banner */}
      <div className="w-full bg-gradient-to-r from-primary to-primary/80 py-20">
        <div className="container mx-auto text-center px-4">
          <h1 className="text-5xl font-bold text-white">About ABC Cars</h1>
          <p className="mt-4 text-lg text-white">Transforming the automotive marketplace with technology and trust since 2010</p>
        </div>
      </div>

      {/* Mission, Vision & Core Values */}
      <div className="container mx-auto px-4 py-16">
        <div className="card bg-base-100 shadow-md p-6">
          <h2 className="text-3xl font-bold text-center mb-10">Our Foundation</h2>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {/* Mission */}
            <div className="card bg-white shadow-lg p-6">
              <h3 className="card-title text-primary">Our Mission</h3>
              <p className="mt-4">To revolutionize the car buying and selling experience through innovative technology, exceptional service, and unwavering integrity.</p>
            </div>

            {/* Vision */}
            <div className="card bg-white shadow-lg p-6">
              <h3 className="card-title text-primary">Our Vision</h3>
              <p className="mt-4">To become the most trusted automotive marketplace where every transaction is seamless, transparent, and satisfying for all parties involved.</p>
            </div>

            {/* Core Values */}
            <div className="card bg-white shadow-lg p-6">
              <h3 className="card-title text-primary">Core Values</h3>
              <ul className="list-disc pl-5 mt-4 space-y-2">
                <li>Customer-centricity in every decision</li>
                <li>Transparency throughout the process</li>
                <li>Innovation that solves real problems</li>
                <li>Integrity in all our interactions</li>
                <li>Excellence in service delivery</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      {/* Meet Our Leadership Team */}
      <div className="container mx-auto px-4 py-16 bg-gray-50">
        <h2 className="text-3xl font-bold text-center mb-10">Meet Our Leadership Team</h2>
        <p className="text-lg text-center mb-12 max-w-3xl mx-auto">The passionate experts driving our mission forward and ensuring your experience with ABC Cars exceeds expectations.</p>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {leadershipTeam.map((member, index) => (
            <div key={index} className="card bg-white shadow-lg rounded-lg overflow-hidden">
              <figure>
                <img
                  src={member.image}
                  alt={`${member.name}, ${member.role}`}
                  className="object-cover w-full h-48"
                  onError={(e) => {
                    e.currentTarget.src = "https://placehold.co/400x300/gray/white?text=Team+Member";
                  }}
                />
              </figure>
              <div className="card-body">
                <h3 className="text-xl font-bold">{member.name}</h3>
                <p className="text-gray-600 font-medium">{member.role}</p>
                <p className="mt-2">{member.bio}</p>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Why Choose ABC Cars? */}
      <div className="container mx-auto px-4 py-16">
        <div className="card bg-base-100 shadow-md p-6">
          <h2 className="text-3xl font-bold text-center mb-10">Why Choose ABC Cars?</h2>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {benefits.map((benefit, index) => (
              <div key={index} className="card bg-white shadow-lg p-6">
                <h3 className="card-title text-primary">{benefit.title}</h3>
                <p className="mt-4">{benefit.description}</p>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Call-to-Action Section */}
      <div className="text-center mt-12 mb-20 container mx-auto px-4">
        <h2 className="text-2xl font-bold text-primary">Ready to Experience ABC Cars?</h2>
        <p className="mt-4 text-lg max-w-2xl mx-auto">Join thousands of satisfied customers who have found their perfect vehicle or sold their car with ease on our platform.</p>

        <div className="mt-8 space-x-4">
          <a href="/register" className="btn btn-primary">Register Now</a>
          <a href="/cars" className="btn btn-secondary">Explore Cars</a>
        </div>
      </div>
    </AppLayout>
  );
}
