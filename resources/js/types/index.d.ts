import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    roles: Role[];
    stripe_account_active: boolean;
    vendor: {
      status: string;
      status_label: string;
      store_name: string;
      store_address: string;
      cover_image: string;
    }
}

export interface Role {
  id: number;
  name: string;
}

export type Image = {
    id: number;
    thumb: string;
    small: string;
    large: string;

}

export type VariationTypeOption = {
    id: number;
    name: string;
    images: Image[];
    type: VariationType
}

export type VariationType = {
    id: number;
    name: string;
    type: 'Select' | 'Radio' | 'Image';
    options: VariationTypeOption[]
}

export type Product = {
    id: number;
    title: string;
    slug: string;
    price: number;
    quantity: number;
    image: string;
    images: Image[];
    short_description: string;
    description: string;
    meta_title: string;
    meta_description: string;
    user:{
      id: number;
      name: string;
      store_name: string;
    };
    department: {
      id: number;
      name: string;
      slug: string;
    };
    variationTypes: VariationType[],
    variations: Array<{
      id: number;
      variation_type_option_ids: number[];
      quantity: number;
      price: number;
    }>
}

export type CartItem = {
  id: number;
  product_id: number;
  title: string;
  slug: string;
  price: number;
  quantity: number;
  image: string;
  option_ids: Record<string, number>;
  options: VariationTypeOption[]
}

export type GroupedCartItems = {
  user: User;
  items: CartItem[];
  totalQuantity: number;
  totalPrice: number;
}

export type PaginationProps<T> = {
  data: Array<T>
}

// Fix for line 110
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


export type OrderItem= {
  id: number;
  quantity: number;
  price: number;
  variation_type_option_ids: number[];
  product: {
    id: number;
    title: string;
    slug: string;
    description: string;
    image: string;
  }
}

export type Order = {
  id: number;
  total_price: number;
  status: string;
  created_at: string;
  vendorUser: {
    id: string;
    name: string;
    email: string;
    store_name: string;
    store_address: string;
  };
  orderItems: OrderItem[]
}

export type Vendor = {
  id: number;
  store_name: string;
  store_address: string;
}

export type Category = {
  id: number;
  name: string;
}

export type Department = {
  id: number;
  name: string;
  slug: string;
  meta_title: string;
  meta_description: string;
  categories: Category[]
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
