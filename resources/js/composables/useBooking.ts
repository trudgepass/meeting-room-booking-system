import axios from 'axios';
import { ref } from 'vue';

export interface BookingRecord {
    id: number;
    room: RoomDetails;
    date: string;
    start_time: string;
    end_time: string;
    user_name: string;
}

export interface RoomDetails {
    id: number;
    name: string;
}

export interface TimeslotRequest {
    date: string;
    start_time: string;
    end_time: string;
}

export interface BookingRequest extends TimeslotRequest {
    room_id: number;
    user_name: string;
}

type BookingState =
    | { type: 'selectTimeslot'; error?: string }
    | { type: 'checkingAvailability' }
    | {
          type: 'selectRoom';
          timeslot: TimeslotRequest;
          availableRooms: RoomDetails[];
          error?: string;
      }
    | {
          type: 'submittingBooking';
          timeslot: TimeslotRequest;
          availableRooms: RoomDetails[];
      }
    | { type: 'bookingConfirmed' };

export function useBooking(initialBookings: BookingRecord[] = []) {
    const state = ref<BookingState>({ type: 'selectTimeslot' });
    const existingBookings = ref<BookingRecord[]>(initialBookings);

    const checkAvailability = async (timeslot: TimeslotRequest) => {
        state.value = { type: 'checkingAvailability' };

        try {
            const { data } = await axios.get<RoomDetails[]>('/api/available-rooms', { params: timeslot });

            if (data.length === 0) {
                state.value = {
                    type: 'selectTimeslot',
                    error: 'No rooms available for the selected date and time. Please try a different timeslot.',
                };
            } else {
                state.value = { type: 'selectRoom', timeslot, availableRooms: data };
            }
        } catch (err: any) {
            state.value = {
                type: 'selectTimeslot',
                error: err?.response?.data?.message || 'Unable to check room availability. Please try again later.',
            };
        }
    };

    const confirmBooking = async (bookingDetails: BookingRequest) => {
        if (state.value.type !== 'selectRoom') {
            throw new Error(`Invalid application state: Cannot confirm booking from ${state.value.type} state`);
        }

        const currentState = state.value;

        state.value = { ...currentState, type: 'submittingBooking' };

        try {
            const { data } = await axios.post<BookingRecord>('/api/bookings', bookingDetails);

            existingBookings.value.unshift(data);

            state.value = { type: 'bookingConfirmed' };
        } catch (err: any) {
            state.value = {
                ...currentState,
                type: 'selectRoom',
                error: err?.response?.data?.message || 'Unable to complete your booking. Please try again.',
            };
        }
    };

    const resetBooking = () => {
        state.value = { type: 'selectTimeslot' };
    };

    return {
        state,
        existingBookings,
        checkAvailability,
        confirmBooking,
        resetBooking,
    };
}
