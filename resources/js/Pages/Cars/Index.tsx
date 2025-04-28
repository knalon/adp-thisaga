import React, { useEffect, useState, FormEvent } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import Layout from '@/Layouts/Layout';
import { PageProps } from '@/types';
import { Car } from '@/types/models';
import Pagination from '@/Components/Pagination';
import SearchIcon from '@/Components/icons/SearchIcon';
import FilterIcon from '@/Components/icons/FilterIcon';
import SortIcon from '@/Components/icons/SortIcon';
import NoDataIllustration from '@/Components/NoDataIllustration';

interface CarsProps extends PageProps {
  cars: {
    data: Car[];
    links: any[];
    from: number;
    to: number;
    total: number;
  };
  filters: {
    make?: string;
    model?: string;
    year?: string;
    min_price?: string;
    max_price?: string;
    fuel_type?: string;
    transmission?: string;
    search?: string;
    sort?: string;
  };
  makes: string[];
  years: number[];
  priceRange: {
    min: number;
    max: number;
  };
  fuelTypes: string[];
  transmissions: string[];
}

type FilterKey = 'make' | 'model' | 'year' | 'min_price' | 'max_price' | 'fuel_type' | 'transmission' | 'search' | 'sort';

export default function Index({ cars, filters, makes, years, priceRange, fuelTypes, transmissions }: CarsProps) {
  const { auth } = usePage<PageProps>().props;
  const [form, setForm] = useState({
    make: filters.make || '',
    model: filters.model || '',
    year: filters.year || '',
    min_price: filters.min_price || '',
    max_price: filters.max_price || '',
    fuel_type: filters.fuel_type || '',
    transmission: filters.transmission || '',
    search: filters.search || '',
    sort: filters.sort || '',
  });

  const [showFilters, setShowFilters] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [availableModels, setAvailableModels] = useState<string[]>([]);

  useEffect(() => {
    // Update URL when form values change (debounced)
    const handler = setTimeout(() => {
      const params = new URLSearchParams();

      Object.entries(form).forEach(([key, value]) => {
        if (value) {
          params.append(key, value);
        }
      });

      router.get(route('cars.index'), Object.fromEntries(params), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      });
    }, 500);

    return () => clearTimeout(handler);
  }, [form]);

  // Fetch models when make changes
  useEffect(() => {
    if (form.make) {
      setIsLoading(true);
      fetch(`/api/cars/models?make=${encodeURIComponent(form.make)}`)
        .then(response => response.json())
        .then(data => {
          setAvailableModels(data);
          setIsLoading(false);
        })
        .catch(error => {
          console.error('Error fetching models:', error);
          setIsLoading(false);
        });
    } else {
      setAvailableModels([]);
    }
  }, [form.make]);

  const handleFilterChange = (key: FilterKey, value: string) => {
    // Reset model if make is changed
    if (key === 'make' && value !== form.make) {
      setForm({ ...form, [key]: value, model: '' });
    } else {
      setForm({ ...form, [key]: value });
    }
  };

  const resetFilters = () => {
    setForm({
      make: '',
      model: '',
      year: '',
      min_price: '',
      max_price: '',
      fuel_type: '',
      transmission: '',
      search: '',
      sort: '',
    });
  };

  const handleSearch = (e: FormEvent) => {
    e.preventDefault();

    router.get(route('cars.index'), form, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  return (
    <Layout>
      <Head title="Cars For Sale" />

      {/* Hero Banner */}
      <div className="bg-gradient-to-r from-blue-800 to-indigo-900 py-12 px-4 sm:px-6">
        <div className="max-w-7xl mx-auto text-center">
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

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
              {/* Make Filter */}
              <div>
                <label htmlFor="make" className="block text-sm font-medium text-gray-700 mb-1">
                  Make
                </label>
                <select
                  id="make"
                  name="make"
                  className="select select-bordered w-full"
                  value={form.make}
                  onChange={(e) => handleFilterChange('make', e.target.value)}
                  disabled={isLoading}
                >
                  <option value="">All Makes</option>
                  {makes && makes.map((make) => (
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
                  value={form.model}
                  onChange={(e) => handleFilterChange('model', e.target.value)}
                  disabled={isLoading || !form.make}
                >
                  <option value="">All Models</option>
                  {availableModels && availableModels.map((model) => (
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
                  value={form.year}
                  onChange={(e) => handleFilterChange('year', e.target.value)}
                  disabled={isLoading}
                >
                  <option value="">All Years</option>
                  {years && years.map((year) => (
                    <option key={year} value={year.toString()}>
                      {year}
                    </option>
                  ))}
                </select>
              </div>

              {/* Fuel Type Filter */}
              <div>
                <label htmlFor="fuel_type" className="block text-sm font-medium text-gray-700 mb-1">
                  Fuel Type
                </label>
                <select
                  id="fuel_type"
                  name="fuel_type"
                  className="select select-bordered w-full"
                  value={form.fuel_type}
                  onChange={(e) => handleFilterChange('fuel_type', e.target.value)}
                  disabled={isLoading}
                >
                  <option value="">All Fuel Types</option>
                  {fuelTypes && fuelTypes.map((fuelType) => (
                    <option key={fuelType} value={fuelType}>
                      {fuelType}
                    </option>
                  ))}
                </select>
              </div>

              {/* Transmission Filter */}
              <div>
                <label htmlFor="transmission" className="block text-sm font-medium text-gray-700 mb-1">
                  Transmission
                </label>
                <select
                  id="transmission"
                  name="transmission"
                  className="select select-bordered w-full"
                  value={form.transmission}
                  onChange={(e) => handleFilterChange('transmission', e.target.value)}
                  disabled={isLoading}
                >
                  <option value="">All Transmissions</option>
                  {transmissions && transmissions.map((transmission) => (
                    <option key={transmission} value={transmission}>
                      {transmission}
                    </option>
                  ))}
                </select>
              </div>

              {/* Price Range Filter */}
              <div className="md:col-span-2 lg:col-span-1">
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Price Range
                </label>
                <div className="flex items-center space-x-2">
                  <input
                    type="number"
                    id="min_price"
                    name="min_price"
                    placeholder="Min"
                    className="input input-bordered w-full"
                    value={form.min_price}
                    onChange={(e) => handleFilterChange('min_price', e.target.value)}
                    disabled={isLoading}
                    min={priceRange?.min || 0}
                  />
                  <span>-</span>
                  <input
                    type="number"
                    id="max_price"
                    name="max_price"
                    placeholder="Max"
                    className="input input-bordered w-full"
                    value={form.max_price}
                    onChange={(e) => handleFilterChange('max_price', e.target.value)}
                    disabled={isLoading}
                    max={priceRange?.max || 999999}
                  />
                </div>
              </div>
            </div>

            <div className="flex flex-col sm:flex-row gap-2 mt-4">
              <button
                type="button"
                className="btn btn-primary flex-1"
                onClick={applyFilters}
                disabled={isLoading}
              >
                {isLoading ? (
                  <>
                    <span className="loading loading-spinner loading-sm mr-2"></span>
                    Filtering...
                  </>
                ) : (
                  'Apply Filters'
                )}
              </button>
              <button
                type="button"
                className="btn btn-ghost flex-1"
                onClick={resetFilters}
                disabled={isLoading}
              >
                Reset Filters
              </button>
            </div>
          </div>

          {/* Results Section */}
          <div className="bg-white shadow-sm rounded-lg p-6">
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-lg font-semibold">Available Cars ({cars.total})</h2>
              <div className="dropdown dropdown-end">
                <label tabIndex={0} className="btn btn-ghost btn-sm">
                  Sort by <span className="ml-1">↓</span>
                </label>
                <ul tabIndex={0} className="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                  <li>
                    <button onClick={() => router.get('/cars', { ...form, sort: 'price_asc' })}>
                      Price: Low to High
                    </button>
                  </li>
                  <li>
                    <button onClick={() => router.get('/cars', { ...form, sort: 'price_desc' })}>
                      Price: High to Low
                    </button>
                  </li>
                  <li>
                    <button onClick={() => router.get('/cars', { ...form, sort: 'year_desc' })}>
                      Year: Newest First
                    </button>
                  </li>
                  <li>
                    <button onClick={() => router.get('/cars', { ...form, sort: 'year_asc' })}>
                      Year: Oldest First
                    </button>
                  </li>
                </ul>
              </div>
            </div>

            {cars.data.length === 0 ? (
              <div className="text-center py-12">
                <svg
                  className="mx-auto h-12 w-12 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  aria-hidden="true"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                  />
                </svg>
                <h3 className="mt-2 text-lg font-medium text-gray-900">No cars found</h3>
                <p className="mt-1 text-sm text-gray-500">
                  Try adjusting your search or filter criteria to find what you're looking for.
                </p>
                <div className="mt-6">
                  <button onClick={resetFilters} className="btn btn-outline btn-primary">
                    Clear filters
                  </button>
                </div>
              </div>
            ) : (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                  {cars.data.map((car) => (
                    <Link key={car.id} href={`/cars/${car.id}`} className="card bg-base-100 shadow-md hover:shadow-lg transition-shadow">
                      <figure className="relative h-48 bg-gray-200">
                        {car.primary_image ? (
                          <img
                            src={car.primary_image.startsWith('http')
                              ? car.primary_image
                              : `/storage/${car.primary_image}`}
                            alt={`${car.make} ${car.model}`}
                            className="object-cover w-full h-full"
                          />
                        ) : (
                          <div className="flex items-center justify-center h-full w-full bg-gray-100">
                            <span className="text-gray-400">No image available</span>
                          </div>
                        )}
                        <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                          <h2 className="text-xl font-bold text-white truncate">
                            {car.make} {car.model}
                          </h2>
                          <p className="text-white/90 text-sm">{car.year}</p>
                        </div>
                      </figure>
                      <div className="card-body p-4">
                        <div className="flex justify-between items-center">
                          <span className="text-2xl font-bold text-secondary">${Number(car.price).toLocaleString()}</span>
                          <div className="badge badge-outline">{car.fuel_type}</div>
                        </div>
                        <div className="flex gap-2 text-sm text-gray-500 mt-2">
                          <div className="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {car.mileage ? `${Number(car.mileage).toLocaleString()} km` : 'N/A'}
                          </div>
                          <div className="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {car.transmission || 'N/A'}
                          </div>
                        </div>
                        <p className="text-sm text-gray-600 line-clamp-2 mt-2">{car.description}</p>
                        <div className="card-actions justify-end mt-4">
                          <button className="btn btn-primary btn-sm">View Details</button>
                        </div>
                      </div>
                    </Link>
                  ))}
                </div>

                {/* Pagination */}
                {cars.links && cars.links.length > 3 && (
                  <div className="flex justify-center mt-8">
                    <div className="btn-group">
                      {cars.links.map((link, i) => (
                        <button
                          key={i}
                          className={`btn ${link.active ? 'btn-active' : ''} ${!link.url ? 'btn-disabled' : ''}`}
                          disabled={!link.url}
                          onClick={() => {
                            if (link.url) {
                              setIsLoading(true);
                              router.visit(link.url, {
                                preserveScroll: true,
                                onSuccess: () => setIsLoading(false),
                                onError: () => setIsLoading(false),
                              });
                            }
                          }}
                          dangerouslySetInnerHTML={{ __html: link.label }}
                        ></button>
                      ))}
                    </div>
                  </div>
                )}
              </>
            )}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
