import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import InputLabel from '@/Components/Core/InputLabel';
import TextInput from '@/Components/Core/TextInput';
import InputError from '@/Components/Core/InputError';
import PrimaryButton from '@/Components/Core/PrimaryButton';

// FAQ data
const faqs = [
  {
    question: "How can I schedule a test drive?",
    answer: "You can schedule a test drive by browsing our inventory, selecting a vehicle, and clicking the 'Schedule Test Drive' button. Alternatively, you can contact us directly using the form on this page."
  },
  {
    question: "Do you offer financing options?",
    answer: "Yes, we offer various financing options to help you purchase your vehicle. Our finance team works with multiple lenders to find the best rates and terms for your situation."
  },
  {
    question: "What documents do I need to buy a car?",
    answer: "You'll need a valid driver's license, proof of insurance, and proof of income. If you're financing, additional documentation may be required, such as proof of residence and references."
  },
  {
    question: "Can I trade in my current vehicle?",
    answer: "Yes, we accept trade-ins. We'll evaluate your vehicle and offer you a competitive price that can be applied toward your new purchase."
  },
  {
    question: "Do you sell used cars?",
    answer: "Yes, we offer a wide selection of quality pre-owned vehicles that have passed our comprehensive inspection process."
  }
];

export default function Contact({ flash }) {
  // Use Inertia form
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: ''
  });

  // Map configuration
  const mapStyles = {
    height: '350px',
    width: '100%',
  };

  const defaultCenter = {
    lat: 34.0522, // Los Angeles
    lng: -118.2437
  };

  // Handle form input changes
  const handleChange = (e) => {
    const { name, value } = e.target;
    setData(name, value);
  };

  // Handle form submission
  const handleSubmit = (e) => {
    e.preventDefault();
    post(route('contact.submit'), {
      onSuccess: () => reset(),
    });
  };

  return (
    <AppLayout>
      <Head title="Contact Us | ABC Cars" />

      {/* Hero Section */}
      <div className="w-full bg-gradient-to-r from-primary to-primary/80 py-20">
        <div className="container mx-auto text-center px-4">
          <h1 className="text-5xl font-bold text-white">Get in Touch</h1>
          <p className="mt-4 text-lg text-white">We'd love to hear from you! Fill out the form below or use our contact information.</p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-16">
        {/* Contact Form Card */}
        <div className="card bg-base-100 shadow-md p-6 mb-16">
          <h2 className="text-2xl font-semibold text-gray-800 mb-4">Contact Form</h2>

          {flash?.success && (
            <div className="alert alert-success mb-4">
              {flash.success}
            </div>
          )}

          <form onSubmit={handleSubmit}>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Name */}
              <div>
                <InputLabel htmlFor="name" value="Name" className="text-gray-700" />
                <TextInput
                  id="name"
                  name="name"
                  value={data.name}
                  className="input input-bordered mt-1 block w-full"
                  onChange={handleChange}
                  required
                />
                <InputError message={errors.name} className="mt-2" />
              </div>

              {/* Email */}
              <div>
                <InputLabel htmlFor="email" value="Email" className="text-gray-700" />
                <TextInput
                  id="email"
                  type="email"
                  name="email"
                  value={data.email}
                  className="input input-bordered mt-1 block w-full"
                  onChange={handleChange}
                  required
                />
                <InputError message={errors.email} className="mt-2" />
              </div>

              {/* Phone */}
              <div>
                <InputLabel htmlFor="phone" value="Phone Number" className="text-gray-700" />
                <TextInput
                  id="phone"
                  name="phone"
                  value={data.phone}
                  className="input input-bordered mt-1 block w-full"
                  onChange={handleChange}
                />
                <InputError message={errors.phone} className="mt-2" />
              </div>

              {/* Subject */}
              <div>
                <InputLabel htmlFor="subject" value="Subject" className="text-gray-700" />
                <TextInput
                  id="subject"
                  name="subject"
                  value={data.subject}
                  className="input input-bordered mt-1 block w-full"
                  onChange={handleChange}
                  required
                />
                <InputError message={errors.subject} className="mt-2" />
              </div>

              {/* Message */}
              <div className="md:col-span-2">
                <InputLabel htmlFor="message" value="Message" className="text-gray-700" />
                <textarea
                  id="message"
                  name="message"
                  value={data.message}
                  rows={5}
                  className="textarea textarea-bordered mt-1 block w-full"
                  onChange={handleChange}
                  required
                  maxLength={500}
                />
                <p className="text-xs text-gray-500 mt-1">
                  {500 - data.message.length} characters remaining
                </p>
                <InputError message={errors.message} className="mt-2" />
              </div>
            </div>

            <div className="flex justify-end mt-6">
              <PrimaryButton
                className="ml-4 btn-primary"
                disabled={processing}
              >
                {processing ? 'Sending...' : 'Send Message'}
              </PrimaryButton>
            </div>
          </form>
        </div>

        {/* Contact Information & Map */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
          {/* Contact Details */}
          <div className="card bg-base-100 shadow-md p-6">
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">Contact Information</h2>

            <ul className="space-y-4 text-gray-700">
              <li>
                <span className="font-bold block">Address:</span>
                123 Car Avenue, Singapore 123456
              </li>
              <li>
                <span className="font-bold block">Phone:</span>
                +65 6123 4567
              </li>
              <li>
                <span className="font-bold block">Email:</span>
                info@abccars.com
              </li>
              <li>
                <span className="font-bold block">Hours:</span>
                Monday - Friday: 9:00 AM - 6:00 PM<br />
                Saturday: 10:00 AM - 4:00 PM<br />
                Sunday: Closed
              </li>
            </ul>

            <div className="mt-6 space-x-2">
              <a href="https://facebook.com" className="btn btn-circle btn-primary" aria-label="Facebook">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                </svg>
              </a>
              <a href="https://twitter.com" className="btn btn-circle btn-primary" aria-label="Twitter">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                </svg>
              </a>
              <a href="https://instagram.com" className="btn btn-circle btn-primary" aria-label="Instagram">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                </svg>
              </a>
            </div>
          </div>

          {/* Map (Plain version instead of Google Maps) */}
          <div className="card bg-base-100 shadow-md p-6">
            <h2 className="text-2xl font-semibold text-gray-800 mb-4">Our Location</h2>
            <div className="bg-gray-200 rounded-lg" style={mapStyles}>
              <div className="flex items-center justify-center h-full">
                <div className="text-center">
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-16 w-16 text-primary mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <p className="text-gray-700">
                    <strong>ABC Cars</strong><br />
                    123 Car Avenue, Singapore 123456
                  </p>
                  <a
                    href="https://maps.google.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="btn btn-primary btn-sm mt-4"
                  >
                    Get Directions
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* FAQs */}
        <div className="card bg-base-100 shadow-md p-6 mb-12">
          <h2 className="text-2xl font-semibold text-gray-800 mb-4">Frequently Asked Questions</h2>

          <div className="space-y-6">
            {faqs.map((faq, index) => (
              <div key={index} className="collapse collapse-plus bg-base-200">
                <input type="radio" name="faq-accordion" defaultChecked={index === 0} />
                <div className="collapse-title text-lg font-medium">
                  {faq.question}
                </div>
                <div className="collapse-content">
                  <p className="text-gray-700">{faq.answer}</p>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Call to Action */}
        <div className="bg-primary text-white p-8 rounded-lg text-center mb-8">
          <h2 className="text-2xl font-bold mb-4">Ready to Find Your Dream Car?</h2>
          <p className="mb-6">Browse our extensive collection of quality vehicles today.</p>
          <a href="/cars" className="btn btn-secondary">Explore Cars</a>
        </div>
      </div>
    </AppLayout>
  );
}
