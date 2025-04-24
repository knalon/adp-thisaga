import React from 'react';
import { Link } from '@inertiajs/react';
import { Car } from '@/types';

interface CarCardProps {
  car: Car;
}

const CarCard: React.FC<CarCardProps> = ({ car }) => {
  return (
    <div className="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
      <figure className="relative h-48 bg-gray-100">
        {car.media && car.media[0] ? (
          <img 
            src={car.media[0].original_url} 
            alt={`${car.make} ${car.model}`} 
            className="w-full h-full object-cover"
          />
        ) : (
          <img 
            src="/images/default-car.jpg" 
            alt={`${car.make} ${car.model}`} 
            className="w-full h-full object-cover"
          />
        )}
        {car.is_approved && (
          <div className="absolute top-2 right-2 badge badge-secondary">Available</div>
        )}
      </figure>
      
      <div className="card-body">
        <h2 className="card-title">
          {car.make} {car.model}
          <div className="badge badge-primary">{car.registration_year}</div>
        </h2>
        
        <div className="flex flex-wrap gap-2 mt-2">
          {car.color && (
            <span className="badge badge-outline">Color: {car.color}</span>
          )}
          {car.transmission && (
            <span className="badge badge-outline">Trans: {car.transmission}</span>
          )}
          {car.fuel_type && (
            <span className="badge badge-outline">Fuel: {car.fuel_type}</span>
          )}
        </div>
        
        <div className="mt-2 text-xl font-bold text-primary">
          ${car.price.toLocaleString()}
        </div>
        
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
  );
};

export default CarCard;
