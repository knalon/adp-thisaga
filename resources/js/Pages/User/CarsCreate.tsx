import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';

export default function CarsCreate({ auth }: PageProps) {
  const { data, setData, post, processing, errors } = useForm({
    make: '',
    model: '',
    registration_year: '',
    price: '',
    mileage: '',
    color: '',
    transmission: 'automatic',
    fuel_type: 'petrol',
    description: '',
    images: [] as File[],
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('cars.store'), {
      onSuccess: () => {
        window.location.href = route('user.cars');
      }
    });
  };

  return (
    <AppLayout>
      <Head title="List Your Car" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">List Your Car for Sale</h1>

          <div className="bg-white shadow-md rounded-lg p-6">
            <form onSubmit={handleSubmit}>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label htmlFor="make" className="block text-sm font-medium text-gray-700 mb-1">Make</label>
                  <input
                    id="make"
                    type="text"
                    className="input input-bordered w-full"
                    value={data.make}
                    onChange={e => setData('make', e.target.value)}
                    required
                  />
                  {errors.make && <div className="text-red-500 text-sm mt-1">{errors.make}</div>}
                </div>

                <div>
                  <label htmlFor="model" className="block text-sm font-medium text-gray-700 mb-1">Model</label>
                  <input
                    id="model"
                    type="text"
                    className="input input-bordered w-full"
                    value={data.model}
                    onChange={e => setData('model', e.target.value)}
                    required
                  />
                  {errors.model && <div className="text-red-500 text-sm mt-1">{errors.model}</div>}
                </div>

                <div>
                  <label htmlFor="registration_year" className="block text-sm font-medium text-gray-700 mb-1">Year</label>
                  <input
                    id="registration_year"
                    type="number"
                    className="input input-bordered w-full"
                    value={data.registration_year}
                    onChange={e => setData('registration_year', e.target.value)}
                    required
                    min="1900"
                    max={new Date().getFullYear()}
                  />
                  {errors.registration_year && <div className="text-red-500 text-sm mt-1">{errors.registration_year}</div>}
                </div>

                <div>
                  <label htmlFor="price" className="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                  <input
                    id="price"
                    type="number"
                    className="input input-bordered w-full"
                    value={data.price}
                    onChange={e => setData('price', e.target.value)}
                    required
                    min="0"
                  />
                  {errors.price && <div className="text-red-500 text-sm mt-1">{errors.price}</div>}
                </div>

                <div>
                  <label htmlFor="mileage" className="block text-sm font-medium text-gray-700 mb-1">Mileage (km)</label>
                  <input
                    id="mileage"
                    type="number"
                    className="input input-bordered w-full"
                    value={data.mileage}
                    onChange={e => setData('mileage', e.target.value)}
                    required
                    min="0"
                  />
                  {errors.mileage && <div className="text-red-500 text-sm mt-1">{errors.mileage}</div>}
                </div>

                <div>
                  <label htmlFor="color" className="block text-sm font-medium text-gray-700 mb-1">Color</label>
                  <input
                    id="color"
                    type="text"
                    className="input input-bordered w-full"
                    value={data.color}
                    onChange={e => setData('color', e.target.value)}
                    required
                  />
                  {errors.color && <div className="text-red-500 text-sm mt-1">{errors.color}</div>}
                </div>

                <div>
                  <label htmlFor="transmission" className="block text-sm font-medium text-gray-700 mb-1">Transmission</label>
                  <select
                    id="transmission"
                    className="select select-bordered w-full"
                    value={data.transmission}
                    onChange={e => setData('transmission', e.target.value)}
                    required
                  >
                    <option value="automatic">Automatic</option>
                    <option value="manual">Manual</option>
                    <option value="semi-automatic">Semi-Automatic</option>
                  </select>
                  {errors.transmission && <div className="text-red-500 text-sm mt-1">{errors.transmission}</div>}
                </div>

                <div>
                  <label htmlFor="fuel_type" className="block text-sm font-medium text-gray-700 mb-1">Fuel Type</label>
                  <select
                    id="fuel_type"
                    className="select select-bordered w-full"
                    value={data.fuel_type}
                    onChange={e => setData('fuel_type', e.target.value)}
                    required
                  >
                    <option value="petrol">Petrol</option>
                    <option value="diesel">Diesel</option>
                    <option value="electric">Electric</option>
                    <option value="hybrid">Hybrid</option>
                  </select>
                  {errors.fuel_type && <div className="text-red-500 text-sm mt-1">{errors.fuel_type}</div>}
                </div>
              </div>

              <div className="mb-6">
                <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                  id="description"
                  className="textarea textarea-bordered w-full"
                  rows={5}
                  value={data.description}
                  onChange={e => setData('description', e.target.value)}
                  required
                ></textarea>
                {errors.description && <div className="text-red-500 text-sm mt-1">{errors.description}</div>}
              </div>

              <div className="mb-6">
                <label htmlFor="images" className="block text-sm font-medium text-gray-700 mb-1">Photos</label>
                <input
                  id="images"
                  type="file"
                  className="file-input file-input-bordered w-full"
                  onChange={e => {
                    const fileList = e.target.files;
                    if (fileList) {
                      const filesArray = Array.from(fileList);
                      setData('images', filesArray);
                    }
                  }}
                  accept="image/*"
                  multiple
                />
                {errors.images && <div className="text-red-500 text-sm mt-1">{errors.images}</div>}
                <p className="text-xs text-gray-500 mt-1">
                  Upload multiple photos of your car. First photo will be used as the cover image.
                </p>
              </div>

              <div className="flex justify-end">
                <button
                  type="submit"
                  className="btn btn-primary"
                  disabled={processing}
                >
                  {processing ? 'Submitting...' : 'Submit Listing'}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
