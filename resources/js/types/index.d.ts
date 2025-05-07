import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    created_at: string;
    updated_at: string;
    roles?: { name: string }[];
    isBanned?: boolean;
    stripe_account_active: boolean;
}

export interface Role {
  id: number;
  name: string;
}

export interface Car {
  id: number;
  user_id: number;
  make: string;
  model: string;
  registration_year: number;
  price: number;
  description: string | null;
  color: string | null;
  mileage: string | null;
  transmission: string | null;
  fuel_type: string | null;
  body_type: string | null;
  is_active: boolean;
  is_approved: boolean;
  slug: string;
  created_at: string;
  updated_at: string;
  user?: User;
  media?: Media[];
}

export interface Media {
  id: number;
  model_type: string;
  model_id: number;
  uuid: string;
  collection_name: string;
  name: string;
  file_name: string;
  mime_type: string;
  disk: string;
  conversions_disk: string;
  size: number;
  manipulations: any[];
  custom_properties: any;
  generated_conversions: any;
  responsive_images: any[];
  order_column: number;
  created_at: string;
  updated_at: string;
  original_url: string;
  preview_url: string;
}

export interface Appointment {
  id: number;
  user_id: number;
  car_id: number;
  appointment_date: string;
  bid_price: number | null;
  status: 'pending' | 'approved' | 'rejected' | 'completed';
  notes: string | null;
  created_at: string;
  updated_at: string;
  user?: User;
  car?: Car;
}

export interface Transaction {
  id: number;
  user_id: number;
  car_id: number;
  appointment_id: number | null;
  final_price: number;
  status: 'pending' | 'completed' | 'cancelled';
  transaction_reference: string | null;
  created_at: string;
  updated_at: string;
  user?: User;
  car?: Car;
  appointment?: Appointment;
}

export interface Image {
  id: number;
  thumb: string;
  large: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = {
  auth: {
    user: User;
  };
  flash: {
    message: string;
    success: string;
    error: string;
  };
  errors: Record<string, string>;
} & T;
