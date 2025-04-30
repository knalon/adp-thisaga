import React, { useEffect, useState, FormEvent } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
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
    <AppLayout>
      <Head title="Cars" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          {/* Hero Section */}
          <div className="bg-primary text-white rounded-lg shadow-lg overflow-hidden mb-8 py-12">
            <div className="max-w-7xl mx-auto text-center px-4">
              <h1 className="text-5xl font-bold mb-6">Find Your Perfect Used Car</h1>
              <p className="text-2xl mb-10">Browse our selection of quality used cars at competitive prices</p>

              {/* Search Bar */}
              <div className="max-w-3xl mx-auto mb-4">
                <form onSubmit={handleSearch} className="flex">
                  <input
                    type="text"
                    placeholder="Search by make, model, or keyword..."
                    className="flex-1 p-3 rounded-l-lg text-gray-900"
                    value={form.search}
                    onChange={(e) => setForm({ ...form, search: e.target.value })}
                  />
                  <button
                    type="submit"
                    className="bg-secondary text-gray-900 px-6 py-3 rounded-r-lg font-medium hover:bg-opacity-90"
                  >
                    <SearchIcon className="h-5 w-5 inline-block mr-2" />
                    Search
                  </button>
                </form>
              </div>
            </div>
          </div>

          {/* Filters and Results */}
          <div className="flex flex-col lg:flex-row gap-8">
            {/* Filters Sidebar */}
            <div className="lg:w-1/4">
              <div className="bg-white rounded-lg shadow p-6">
                <div className="flex justify-between items-center mb-4">
                  <h2 className="text-lg font-semibold">Filters</h2>
                  <button
                    onClick={resetFilters}
                    className="text-sm text-primary hover:underline"
                  >
                    Reset All
                  </button>
                </div>

                {/* Make Filter */}
                <div className="mb-4">
                  <label className="block text-sm font-medium text-gray-700 mb-1">Make</label>
                  <select
                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    value={form.make}
                    onChange={(e) => handleFilterChange('make', e.target.value)}
                  >
                    <option value="">All Makes</option>
                    {makes.map((make) => (
                      <option key={make} value={make}>{make}</option>
                    ))}
                  </select>
                </div>

                {/* Model Filter */}
                <div className="mb-4">
                  <label className="block text-sm font-medium text-gray-700 mb-1">Model</label>
                  <input
                    type="text"
                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    placeholder="Enter model"
                    value={form.model}
                    onChange={(e) => handleFilterChange('model', e.target.value)}
                  />
                </div>

                {/* Year Filter */}
                <div className="mb-4">
                  <label className="block text-sm font-medium text-gray-700 mb-1">Year</label>
                  <select
                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    value={form.year}
                    onChange={(e) => handleFilterChange('year', e.target.value)}
                  >
                    <option value="">All Years</option>
                    {years.map((year) => (
                      <option key={year} value={year}>{year}</option>
                    ))}
                  </select>
                </div>

                {/* Price Range Filter */}
                <div className="mb-4">
                  <label className="block text-sm font-medium text-gray-700 mb-1">Price Range</label>
                  <div className="flex gap-2">
                    <input
                      type="number"
                      className="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                      placeholder="Min"
                      value={form.min_price}
                      onChange={(e) => handleFilterChange('min_price', e.target.value)}
                    />
                    <input
                      type="number"
                      className="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                      placeholder="Max"
                      value={form.max_price}
                      onChange={(e) => handleFilterChange('max_price', e.target.value)}
                    />
                  </div>
                </div>

                {/* Fuel Type Filter */}
                <div className="mb-4">
                  <label className="block text-sm font-medium text-gray-700 mb-1">Fuel Type</label>
                  <select
                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    value={form.fuel_type}
                    onChange={(e) => handleFilterChange('fuel_type', e.target.value)}
                  >
                    <option value="">All Fuel Types</option>
                    {fuelTypes.map((type) => (
                      <option key={type} value={type}>{type}</option>
                    ))}
                  </select>
                </div>

                {/* Transmission Filter */}
                <div className="mb-4">
                  <label className="block text-sm font-medium text-gray-700 mb-1">Transmission</label>
                  <select
                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    value={form.transmission}
                    onChange={(e) => handleFilterChange('transmission', e.target.value)}
                  >
                    <option value="">All Transmissions</option>
                    {transmissions.map((type) => (
                      <option key={type} value={type}>{type}</option>
                    ))}
                  </select>
                </div>
              </div>
            </div>

            {/* Results */}
            <div className="lg:w-3/4">
              {/* Sort Options */}
              <div className="bg-white rounded-lg shadow p-4 mb-6 flex justify-between items-center">
                <div>
                  <span className="text-sm text-gray-500">
                    Showing {cars.from} to {cars.to} of {cars.total} results
                  </span>
                </div>
                <div className="flex items-center">
                  <label className="text-sm text-gray-700 mr-2">Sort by:</label>
                  <select
                    className="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    value={form.sort}
                    onChange={(e) => handleFilterChange('sort', e.target.value)}
                  >
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                  </select>
                </div>
              </div>

              {/* Cars Grid */}
              {cars.data.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {cars.data.map((car) => (
                    <div key={car.id} className="bg-white rounded-lg shadow overflow-hidden">
                      <div className="relative">
                        <img
                          src={car.images && car.images.length > 0 && typeof car.images[0] === 'object' ? car.images[0].url : '/images/car-placeholder.jpg'}
                          alt={`${car.make} ${car.model}`}
                          className="w-full h-48 object-cover"
                        />
                        <div className="absolute top-2 right-2 bg-primary text-white px-2 py-1 rounded text-sm">
                          ${car.price.toLocaleString()}
                        </div>
                      </div>
                      <div className="p-4">
                        <h3 className="text-lg font-semibold mb-1">{car.make} {car.model}</h3>
                        <p className="text-gray-600 mb-2">{car.year}</p>
                        <div className="flex justify-between text-sm text-gray-500 mb-4">
                          <span>{car.mileage.toLocaleString()} miles</span>
                          <span>{car.fuel_type}</span>
                          <span>{car.transmission}</span>
                        </div>
                        <Link
                          href={route('cars.show', car.id)}
                          className="block w-full text-center bg-primary text-white py-2 rounded hover:bg-opacity-90"
                        >
                          View Details
                        </Link>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="bg-white rounded-lg shadow p-8 text-center">
                  <NoDataIllustration className="w-48 h-48 mx-auto mb-4" />
                  <h3 className="text-xl font-semibold mb-2">No cars found</h3>
                  <p className="text-gray-600 mb-4">Try adjusting your filters or search criteria</p>
                  <button
                    onClick={resetFilters}
                    className="bg-primary text-white px-4 py-2 rounded hover:bg-opacity-90"
                  >
                    Reset Filters
                  </button>
                </div>
              )}

              {/* Pagination */}
              {cars.data.length > 0 && (
                <div className="mt-8">
                  <Pagination links={cars.links} />
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
