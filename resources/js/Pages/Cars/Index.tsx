import React, { useState, useEffect } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Car, PageProps } from '@/types';
import { useForm } from '@inertiajs/react';

interface CarsProps extends Record<string, unknown> {
  cars: {
    data: Car[];
    links: any[];
    current_page: number;
    last_page: number;
    total: number;
  };
  filters: {
    make?: string;
    model?: string;
    year?: string;
    min_price?: string;
    max_price?: string;
    search?: string;
  };
  makes: string[];
  models: string[];
  years: number[];
  priceRange: {
    min: number;
    max: number;
  };
}

type FilterKey = 'make' | 'model' | 'year' | 'min_price' | 'max_price' | 'search';

export default function Index({
  auth,
  cars,
  filters,
  makes,
  models,
  years,
  priceRange,
}: PageProps<CarsProps>) {
  const [isLoading, setIsLoading] = useState(false);
  const [availableModels, setAvailableModels] = useState<string[]>(models || []);

  const form = useForm({
    make: filters.make || '',
    model: filters.model || '',
    year: filters.year || '',
    min_price: filters.min_price || '',
    max_price: filters.max_price || '',
    search: filters.search || '',
  });

  // Update available models when make changes
  useEffect(() => {
    if (form.data.make) {
      setIsLoading(true);
      fetch(`/api/models?make=${form.data.make}`)
        .then(response => response.json())
        .then(data => {
          setAvailableModels(data);
          setIsLoading(false);
        })
        .catch(() => {
          setIsLoading(false);
        });
    } else {
      setAvailableModels(models || []);
    }
  }, [form.data.make]);

  const handleFilterChange = (e: React.ChangeEvent<HTMLSelectElement | HTMLInputElement>) => {
    const { name, value } = e.target;
    form.setData(name as FilterKey, value);

    // Clear model if make changes
    if (name === 'make') {
      form.setData('model', '');
    }
  };

  const applyFilters = () => {
    setIsLoading(true);
    router.get('/cars', form.data, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => setIsLoading(false),
      onError: () => setIsLoading(false),
    });
  };

  const resetFilters = () => {
    form.reset();
    setIsLoading(true);
    router.get('/cars', {}, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => setIsLoading(false),
      onError: () => setIsLoading(false),
    });
  };

  return (
    <AppLayout>
      <Head title="Browse Cars" />

      {/* Hero Banner */}
      <div className="bg-gradient-to-r from-primary to-secondary py-12 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 className="text-3xl md:text-4xl font-bold mb-4">Find Your Perfect Car</h1>
          <p className="text-lg mb-0 max-w-3xl mx-auto">
            Browse our extensive collection of quality vehicles with detailed specifications and transparent pricing.
          </p>
        </div>
      </div>

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          {/* Search & Filter Section */}
          <div className="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h2 className="text-lg font-semibold mb-4">Filter Cars</h2>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
              {/* Make Filter */}
              <div>
                <label htmlFor="make" className="block text-sm font-medium text-gray-700 mb-1">
                  Make
                </label>
                <select
                  id="make"
                  name="make"
                  className="select select-bordered w-full"
                  value={form.data.make}
                  onChange={handleFilterChange}
                  disabled={isLoading}
                >
                  <option value="">All Makes</option>
                  {makes.map((make) => (
                    <option key={make} value={make}>
                      {make}
                    </option>
                  ))}
                </select>
              </div>

              {/* Model Filter */}
              <div>
                <label htmlFor="model" className="block text-sm font-medium text-gray-700 mb-1">
                  Model
                </label>
                <select
                  id="model"
                  name="model"
                  className="select select-bordered w-full"
                  value={form.data.model}
                  onChange={handleFilterChange}
                  disabled={isLoading || !form.data.make}
                >
                  <option value="">All Models</option>
                  {availableModels.map((model) => (
                    <option key={model} value={model}>
                      {model}
                    </option>
                  ))}
                </select>
              </div>

              {/* Year Filter */}
              <div>
                <label htmlFor="year" className="block text-sm font-medium text-gray-700 mb-1">
                  Year
                </label>
                <select
                  id="year"
                  name="year"
                  className="select select-bordered w-full"
                  value={form.data.year}
                  onChange={handleFilterChange}
                  disabled={isLoading}
                >
                  <option value="">All Years</option>
                  {years.map((year) => (
                    <option key={year} value={year.toString()}>
                      {year}
                    </option>
                  ))}
                </select>
              </div>

              {/* Price Range Filter */}
              <div>
                <label htmlFor="min_price" className="block text-sm font-medium text-gray-700 mb-1">
                  Min Price
                </label>
                <input
                  type="number"
                  id="min_price"
                  name="min_price"
                  className="input input-bordered w-full"
                  placeholder="Min Price"
                  value={form.data.min_price}
                  onChange={handleFilterChange}
                  min={priceRange.min}
                  max={priceRange.max}
                  disabled={isLoading}
                />
              </div>

              <div>
                <label htmlFor="max_price" className="block text-sm font-medium text-gray-700 mb-1">
                  Max Price
                </label>
                <input
                  type="number"
                  id="max_price"
                  name="max_price"
                  className="input input-bordered w-full"
                  placeholder="Max Price"
                  value={form.data.max_price}
                  onChange={handleFilterChange}
                  min={priceRange.min}
                  max={priceRange.max}
                  disabled={isLoading}
                />
              </div>
            </div>

            {/* Search Input */}
            <div className="mt-4">
              <label htmlFor="search" className="block text-sm font-medium text-gray-700 mb-1">
                Search
              </label>
              <div className="flex gap-4">
                <input
                  type="text"
                  id="search"
                  name="search"
                  className="input input-bordered flex-1"
                  placeholder="Search by keywords..."
                  value={form.data.search}
                  onChange={handleFilterChange}
                  disabled={isLoading}
                />
                <button
                  type="button"
                  className="btn btn-primary"
                  onClick={applyFilters}
                  disabled={isLoading}
                >
                  {isLoading ? (
                    <>
                      <span className="loading loading-spinner loading-sm"></span>
                      Searching...
                    </>
                  ) : (
                    'Search'
                  )}
                </button>
                <button
                  type="button"
                  className="btn btn-outline"
                  onClick={resetFilters}
                  disabled={isLoading}
                >
                  Reset Filters
                </button>
              </div>
            </div>
          </div>

          {/* Results Count */}
          {cars.total > 0 && (
            <div className="mb-6">
              <p className="text-gray-600">
                Showing {cars.data.length} of {cars.total} cars
                {Object.values(filters).some(val => val) && ' matching your filters'}
              </p>
            </div>
          )}

          {/* Car Listing */}
          {isLoading ? (
            <div className="flex justify-center items-center py-12">
              <div className="loading loading-spinner loading-lg"></div>
            </div>
          ) : cars.data.length > 0 ? (
            <>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {cars.data.map((car) => (
                  <div key={car.id} className="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                    <figure className="relative h-56">
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
                      <div className="absolute top-2 right-2">
                        <span className="badge badge-primary">{car.registration_year}</span>
                      </div>
                    </figure>
                    <div className="card-body">
                      <h2 className="card-title">
                        {car.make} {car.model}
                      </h2>
                      <div className="flex flex-wrap gap-2 mt-2">
                        {car.transmission && (
                          <span className="badge badge-outline">{car.transmission}</span>
                        )}
                        {car.fuel_type && (
                          <span className="badge badge-outline">{car.fuel_type}</span>
                        )}
                        {car.body_type && (
                          <span className="badge badge-outline">{car.body_type}</span>
                        )}
                      </div>
                      <p className="text-2xl font-bold text-primary mt-2">
                        ${car.price.toLocaleString()}
                      </p>
                      <div className="card-actions justify-end mt-4">
                        <Link
                          href={route('cars.show', car.slug)}
                          className="btn btn-primary"
                        >
                          View Details
                        </Link>
                      </div>
                    </div>
                  </div>
                ))}
              </div>

              {/* Pagination */}
              {cars.last_page > 1 && (
                <div className="mt-8 flex justify-center">
                  <div className="join">
                    {cars.links.map((link, i) => {
                      if (link.url === null) {
                        return (
                          <button key={i} className="join-item btn btn-disabled">
                            <span dangerouslySetInnerHTML={{ __html: link.label.replace('&laquo;', '«').replace('&raquo;', '»') }} />
                          </button>
                        );
                      }

                      return (
                        <Link
                          key={i}
                          href={link.url}
                          className={`join-item btn ${link.active ? 'btn-active' : ''}`}
                        >
                          <span dangerouslySetInnerHTML={{ __html: link.label.replace('&laquo;', '«').replace('&raquo;', '»') }} />
                        </Link>
                      );
                    })}
                  </div>
                </div>
              )}
            </>
          ) : (
            <div className="bg-white p-8 rounded-lg text-center">
              <h3 className="text-lg font-medium text-gray-900">No cars found</h3>
              <p className="mt-2 text-gray-500">
                Try adjusting your filters or check back later for new listings.
              </p>
            </div>
          )}

          {/* Call-to-Action Section */}
          {auth.user && (
            <div className="mt-12 bg-gradient-to-r from-secondary to-primary text-white p-8 rounded-lg shadow-lg">
              <div className="flex flex-col md:flex-row justify-between items-center">
                <div>
                  <h3 className="text-xl font-bold mb-2">Have a car to sell?</h3>
                  <p className="mb-0 md:mb-0">
                    List your car on ABC Cars and reach thousands of potential buyers.
                  </p>
                </div>
                <Link
                  href={route('cars.create')}
                  className="btn btn-secondary mt-4 md:mt-0"
                >
                  List Your Car
                </Link>
              </div>
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}
