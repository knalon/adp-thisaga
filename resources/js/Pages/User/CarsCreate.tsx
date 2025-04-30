import React, { useState, useRef } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { toast } from 'react-hot-toast';

interface CarFormData {
    make: string;
    model: string;
    year: string;
    price: string;
    mileage: string;
    color: string;
    transmission: 'automatic' | 'manual';
    fuel_type: 'petrol' | 'diesel' | 'electric' | 'hybrid';
    description: string;
    features: string[];
    images: File[];
}

const CarsCreate: React.FC<PageProps> = ({ auth }) => {
    const fileInputRef = useRef<HTMLInputElement>(null);
    const [previewImages, setPreviewImages] = useState<string[]>([]);
    const [featureInput, setFeatureInput] = useState('');

    const { data, setData, post, processing, errors, reset } = useForm<CarFormData>({
        make: '',
        model: '',
        year: '',
        price: '',
        mileage: '',
        color: '',
        transmission: 'automatic',
        fuel_type: 'petrol',
        description: '',
        features: [],
        images: [],
    });

    const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = Array.from(e.target.files || []);
        if (files.length > 5) {
            toast.error('Maximum 5 images allowed');
            return;
        }

        // Create preview URLs
        const previews = files.map(file => URL.createObjectURL(file));
        setPreviewImages(previews);

        setData('images', files);
    };

    const handleFeatureAdd = () => {
        if (featureInput.trim()) {
            setData('features', [...data.features, featureInput.trim()]);
            setFeatureInput('');
        }
    };

    const handleFeatureRemove = (index: number) => {
        setData('features', data.features.filter((_, i) => i !== index));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        // Create FormData object for file upload
        const formData = new FormData();

        // Add all form fields to FormData
        formData.append('make', data.make);
        formData.append('model', data.model);
        formData.append('year', data.year);
        formData.append('price', data.price);
        formData.append('mileage', data.mileage);
        formData.append('color', data.color);
        formData.append('transmission', data.transmission);
        formData.append('fuel_type', data.fuel_type);
        formData.append('description', data.description);

        // Add features array
        data.features.forEach((feature, index) => {
            formData.append(`features[${index}]`, feature);
        });

        // Add images
        data.images.forEach((file, index) => {
            formData.append(`images[${index}]`, file);
        });

        post(route('cars.store'), formData, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Car listing submitted successfully! Waiting for admin approval.');
                reset();
                setPreviewImages([]);
                router.visit(route('user.cars'));
            },
            onError: (errors) => {
                Object.entries(errors).forEach(([field, message]) => {
                    toast.error(`${field}: ${message}`);
                });
            }
        });
    };

    return (
        <AppLayout>
            <Head title="List Your Car" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h1 className="text-3xl font-bold text-gray-900 mb-6">List Your Car for Sale</h1>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* Basic Information */}
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label htmlFor="make" className="block text-sm font-medium text-gray-700 mb-1">
                                            Make
                                        </label>
                                        <input
                                            id="make"
                                            type="text"
                                            className="input input-bordered w-full"
                                            value={data.make}
                                            onChange={e => setData('make', e.target.value)}
                                            required
                                        />
                                        {errors.make && <div className="text-error text-sm mt-1">{errors.make}</div>}
                                    </div>

                                    <div>
                                        <label htmlFor="model" className="block text-sm font-medium text-gray-700 mb-1">
                                            Model
                                        </label>
                                        <input
                                            id="model"
                                            type="text"
                                            className="input input-bordered w-full"
                                            value={data.model}
                                            onChange={e => setData('model', e.target.value)}
                                            required
                                        />
                                        {errors.model && <div className="text-error text-sm mt-1">{errors.model}</div>}
                                    </div>

                                    <div>
                                        <label htmlFor="year" className="block text-sm font-medium text-gray-700 mb-1">
                                            Registration Year
                                        </label>
                                        <input
                                            id="year"
                                            type="number"
                                            className="input input-bordered w-full"
                                            value={data.year}
                                            onChange={e => setData('year', e.target.value)}
                                            required
                                            min="1900"
                                            max={new Date().getFullYear()}
                                        />
                                        {errors.year && (
                                            <div className="text-error text-sm mt-1">{errors.year}</div>
                                        )}
                                    </div>

                                    <div>
                                        <label htmlFor="price" className="block text-sm font-medium text-gray-700 mb-1">
                                            Price ($)
                                        </label>
                                        <input
                                            id="price"
                                            type="number"
                                            className="input input-bordered w-full"
                                            value={data.price}
                                            onChange={e => setData('price', e.target.value)}
                                            required
                                            min="0"
                                        />
                                        {errors.price && <div className="text-error text-sm mt-1">{errors.price}</div>}
                                    </div>
                                </div>

                                {/* Additional Details */}
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label htmlFor="mileage" className="block text-sm font-medium text-gray-700 mb-1">
                                            Mileage (km)
                                        </label>
                                        <input
                                            id="mileage"
                                            type="number"
                                            className="input input-bordered w-full"
                                            value={data.mileage}
                                            onChange={e => setData('mileage', e.target.value)}
                                            required
                                            min="0"
                                        />
                                        {errors.mileage && <div className="text-error text-sm mt-1">{errors.mileage}</div>}
                                    </div>

                                    <div>
                                        <label htmlFor="color" className="block text-sm font-medium text-gray-700 mb-1">
                                            Color
                                        </label>
                                        <input
                                            id="color"
                                            type="text"
                                            className="input input-bordered w-full"
                                            value={data.color}
                                            onChange={e => setData('color', e.target.value)}
                                            required
                                        />
                                        {errors.color && <div className="text-error text-sm mt-1">{errors.color}</div>}
                                    </div>

                                    <div>
                                        <label htmlFor="transmission" className="block text-sm font-medium text-gray-700 mb-1">
                                            Transmission
                                        </label>
                                        <select
                                            id="transmission"
                                            className="select select-bordered w-full"
                                            value={data.transmission}
                                            onChange={e => setData('transmission', e.target.value as 'automatic' | 'manual')}
                                            required
                                        >
                                            <option value="automatic">Automatic</option>
                                            <option value="manual">Manual</option>
                                        </select>
                                        {errors.transmission && (
                                            <div className="text-error text-sm mt-1">{errors.transmission}</div>
                                        )}
                                    </div>

                                    <div>
                                        <label htmlFor="fuel_type" className="block text-sm font-medium text-gray-700 mb-1">
                                            Fuel Type
                                        </label>
                                        <select
                                            id="fuel_type"
                                            className="select select-bordered w-full"
                                            value={data.fuel_type}
                                            onChange={e =>
                                                setData(
                                                    'fuel_type',
                                                    e.target.value as 'petrol' | 'diesel' | 'electric' | 'hybrid'
                                                )
                                            }
                                            required
                                        >
                                            <option value="petrol">Petrol</option>
                                            <option value="diesel">Diesel</option>
                                            <option value="electric">Electric</option>
                                            <option value="hybrid">Hybrid</option>
                                        </select>
                                        {errors.fuel_type && <div className="text-error text-sm mt-1">{errors.fuel_type}</div>}
                                    </div>
                                </div>

                                {/* Description */}
                                <div>
                                    <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea
                                        id="description"
                                        className="textarea textarea-bordered w-full h-32"
                                        value={data.description}
                                        onChange={e => setData('description', e.target.value)}
                                        required
                                    />
                                    {errors.description && (
                                        <div className="text-error text-sm mt-1">{errors.description}</div>
                                    )}
                                </div>

                                {/* Features */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Features</label>
                                    <div className="flex gap-2 mb-2">
                                        <input
                                            type="text"
                                            className="input input-bordered flex-1"
                                            value={featureInput}
                                            onChange={e => setFeatureInput(e.target.value)}
                                            placeholder="Add a feature"
                                        />
                                        <button
                                            type="button"
                                            className="btn btn-primary"
                                            onClick={handleFeatureAdd}
                                        >
                                            Add
                                        </button>
                                    </div>
                                    <div className="flex flex-wrap gap-2">
                                        {data.features.map((feature, index) => (
                                            <div
                                                key={index}
                                                className="badge badge-primary gap-2 p-3"
                                            >
                                                {feature}
                                                <button
                                                    type="button"
                                                    className="btn btn-ghost btn-xs"
                                                    onClick={() => handleFeatureRemove(index)}
                                                >
                                                    ×
                                                </button>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                {/* Images */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Images (Max 5)
                                    </label>
                                    <input
                                        type="file"
                                        ref={fileInputRef}
                                        className="hidden"
                                        accept="image/*"
                                        multiple
                                        onChange={handleImageChange}
                                    />
                                    <button
                                        type="button"
                                        className="btn btn-outline btn-primary"
                                        onClick={() => fileInputRef.current?.click()}
                                    >
                                        Choose Images
                                    </button>
                                    {previewImages.length > 0 && (
                                        <div className="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4">
                                            {previewImages.map((preview, index) => (
                                                <div key={index} className="relative">
                                                    <img
                                                        src={preview}
                                                        alt={`Preview ${index + 1}`}
                                                        className="w-full h-32 object-cover rounded-lg"
                                                    />
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                    {errors.images && <div className="text-error text-sm mt-1">{errors.images}</div>}
                                </div>

                                {/* Submit Button */}
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
            </div>
        </AppLayout>
    );
};

export default CarsCreate;
