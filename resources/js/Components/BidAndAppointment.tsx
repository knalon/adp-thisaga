import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { DateTimePicker } from '@/Components/ui/date-time-picker';
import { Alert } from '@/Components/ui/alert';
import { Card } from '@/Components/ui/card';

interface Props {
    carId: number;
    currentUserId: number;
    carOwnerId: number;
}

interface Appointment {
    id: number;
    car_id: number;
    user_id: number;
    appointment_date: string;
    bid_amount: number;
    status: string;
    notes: string;
}

export default function BidAndAppointment({ carId, currentUserId, carOwnerId }: Props) {
    const [highestBid, setHighestBid] = useState<number | null>(null);
    const [userAppointment, setUserAppointment] = useState<Appointment | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState<string | null>(null);

    const { data, setData, post, processing } = useForm({
        appointment_date: '',
        bid_amount: '',
        notes: '',
    });

    useEffect(() => {
        fetchHighestBid();
        fetchUserAppointments();
    }, []);

    const fetchHighestBid = async () => {
        try {
            const response = await axios.get(`/api/cars/${carId}/highest-bid`);
            setHighestBid(response.data.highest_bid);
        } catch (error) {
            console.error('Error fetching highest bid:', error);
        }
    };

    const fetchUserAppointments = async () => {
        try {
            const response = await axios.get('/api/user/appointments');
            const appointment = response.data.appointments.find(
                (a: Appointment) => a.car_id === carId && a.status === 'pending'
            );
            setUserAppointment(appointment || null);
        } catch (error) {
            console.error('Error fetching user appointments:', error);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);
        setSuccess(null);

        try {
            if (userAppointment) {
                // Update existing bid
                await axios.post(`/api/cars/${carId}/bids`, {
                    bid_amount: parseFloat(data.bid_amount),
                });
                setSuccess('Bid updated successfully!');
            } else {
                // Create new appointment with bid
                await axios.post(`/api/cars/${carId}/appointments`, {
                    appointment_date: data.appointment_date,
                    bid_amount: parseFloat(data.bid_amount),
                    notes: data.notes,
                });
                setSuccess('Appointment scheduled and bid placed successfully!');
            }

            // Refresh data
            fetchHighestBid();
            fetchUserAppointments();

            // Clear form
            setData({
                appointment_date: '',
                bid_amount: '',
                notes: '',
            });
        } catch (error: any) {
            setError(error.response?.data?.message || 'An error occurred. Please try again.');
        }
    };

    const handleCancel = async () => {
        try {
            await axios.post(`/api/cars/${carId}/appointments/cancel`);
            setSuccess('Appointment cancelled successfully!');
            setUserAppointment(null);
            fetchHighestBid();
        } catch (error: any) {
            setError(error.response?.data?.message || 'An error occurred while cancelling.');
        }
    };

    if (currentUserId === carOwnerId) {
        return null; // Don't show bidding form to car owner
    }

    return (
        <Card className="p-6">
            <h2 className="text-2xl font-bold mb-4">Schedule Test Drive & Place Bid</h2>

            {highestBid && (
                <Alert className="mb-4">
                    Current Highest Bid: ${highestBid.toLocaleString()}
                </Alert>
            )}

            {error && (
                <Alert variant="destructive" className="mb-4">
                    {error}
                </Alert>
            )}

            {success && (
                <Alert variant="success" className="mb-4">
                    {success}
                </Alert>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
                {!userAppointment && (
                    <>
                        <div>
                            <label className="block text-sm font-medium mb-1">
                                Preferred Date & Time
                            </label>
                            <DateTimePicker
                                value={data.appointment_date}
                                onChange={(date) => setData('appointment_date', date)}
                                minDate={new Date()}
                                required
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1">
                                Notes
                            </label>
                            <Textarea
                                value={data.notes}
                                onChange={(e) => setData('notes', e.target.value)}
                                placeholder="Any special requests or questions?"
                            />
                        </div>
                    </>
                )}

                <div>
                    <label className="block text-sm font-medium mb-1">
                        {userAppointment ? 'Update Your Bid' : 'Your Bid Amount'}
                    </label>
                    <Input
                        type="number"
                        value={data.bid_amount}
                        onChange={(e) => setData('bid_amount', e.target.value)}
                        min="0"
                        step="0.01"
                        required
                        placeholder="Enter amount in USD"
                    />
                </div>

                <div className="flex gap-4">
                    <Button
                        type="submit"
                        disabled={processing}
                        className="w-full"
                    >
                        {userAppointment ? 'Update Bid' : 'Schedule & Bid'}
                    </Button>

                    {userAppointment && (
                        <Button
                            type="button"
                            variant="destructive"
                            onClick={handleCancel}
                            className="w-full"
                        >
                            Cancel Appointment
                        </Button>
                    )}
                </div>
            </form>
        </Card>
    );
} 