export interface Car {
    id: number;
    make: string;
    model: string;
    year: number;
    price: number;
    mileage: number;
    fuel_type: string;
    transmission: string;
    color: string;
    description: string;
    features: string[];
    images: { url: string }[];
    status: 'pending' | 'approved' | 'rejected';
    created_at: string;
    updated_at: string;
    user_id: number;
}

export interface User {
    id: number;
    name: string;
    email: string;
    role: 'user' | 'admin';
    created_at: string;
    updated_at: string;
}

export interface Bid {
    id: number;
    car_id: number;
    user_id: number;
    amount: number;
    status: 'pending' | 'approved' | 'rejected';
    created_at: string;
    updated_at: string;
}

export interface Appointment {
    id: number;
    car_id: number;
    user_id: number;
    date: string;
    time: string;
    status: 'pending' | 'approved' | 'rejected';
    created_at: string;
    updated_at: string;
}
