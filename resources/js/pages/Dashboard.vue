<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { BookingRecord, BookingRequest, useBooking } from '@/composables/useBooking';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    bookings: BookingRecord[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
];

const { state, existingBookings, checkAvailability, confirmBooking, resetBooking } = useBooking(props.bookings);

const timeslotForm = ref({
    date: new Date().toISOString().split('T')[0],
    start_time: '09:00',
    end_time: '10:00',
});

const bookingDetailsForm = ref({
    room_id: null,
    user_name: '',
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="grid gap-16 px-4 py-6 lg:grid-cols-2">
            <div class="flex flex-col space-y-6">
                <div class="flex flex-col space-y-6">
                    <div class="mb-8 space-y-0.5">
                        <h2 class="text-xl font-semibold tracking-tight">Meeting Room Reservation</h2>
                        <p class="text-muted-foreground text-sm">Select a date and time for your meeting room reservation.</p>
                    </div>
                    <form @submit.prevent="checkAvailability(timeslotForm)">
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label>Reservation Date</Label>
                                <Input
                                    type="date"
                                    name="date"
                                    :disabled="state.type === 'selectRoom' || state.type === 'checkingAvailability'"
                                    v-model="timeslotForm.date"
                                    required
                                    :min="new Date().toISOString().split('T')[0]"
                                />
                            </div>

                            <Label class="grid gap-2">
                                Start Time (9:00 AM - 6:00 PM)
                                <Input
                                    type="time"
                                    name="start_time"
                                    v-model="timeslotForm.start_time"
                                    :disabled="state.type === 'selectRoom' || state.type === 'checkingAvailability'"
                                    required
                                    min="09:00"
                                    max="18:00"
                                />
                            </Label>
                            <Label class="grid gap-2">
                                End Time (9:00 AM - 6:00 PM)
                                <Input
                                    type="time"
                                    name="end_time"
                                    v-model="timeslotForm.end_time"
                                    :disabled="state.type === 'selectRoom' || state.type === 'checkingAvailability'"
                                    required
                                    :min="timeslotForm.start_time"
                                    max="18:00"
                                />
                            </Label>
                        </div>
                        <p v-if="state.type === 'selectTimeslot' && state.error" class="mt-2 text-sm font-medium text-red-600">
                            {{ state.error }}
                        </p>
                        <div class="mt-6 grid md:justify-end">
                            <Button type="submit" :disabled="state.type === 'selectRoom' || state.type === 'checkingAvailability'">
                                Check Availability
                            </Button>
                        </div>
                    </form>
                </div>
                <div v-if="state.type === 'selectRoom' || state.type === 'submittingBooking'" class="flex flex-col space-y-6">
                    <p class="text-muted-foreground text-sm">
                        Complete your reservation for: <b>{{ timeslotForm.date }}, {{ timeslotForm.start_time }} - {{ timeslotForm.end_time }}</b>
                    </p>
                    <form @submit.prevent="confirmBooking({ ...timeslotForm, ...bookingDetailsForm } as unknown as BookingRequest)">
                        <div class="grid gap-4">
                            <Label class="grid gap-2">
                                Select a Room
                                <Select v-model="bookingDetailsForm.room_id" required :disabled="state.type === 'submittingBooking'" name="room_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Choose an available room" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="room in state.availableRooms" :key="room.id" :value="room.id" :dusk="`room-${room.id}`">
                                            {{ room.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </Label>

                            <Label class="grid gap-2">
                                Reserved By
                                <Input
                                    type="text"
                                    name="user_name"
                                    v-model="bookingDetailsForm.user_name"
                                    placeholder="Enter your full name"
                                    required
                                    :disabled="state.type === 'submittingBooking'"
                                />
                            </Label>
                        </div>
                        <p v-if="state.type === 'selectRoom' && state.error" class="mt-2 text-sm font-medium text-red-600">
                            {{ state.error }}
                        </p>
                        <div class="mt-6 grid grid-flow-col items-center gap-2 md:justify-end">
                            <Button type="button" variant="secondary" @click="resetBooking" :disabled="state.type === 'submittingBooking'">
                                Change Date/Time
                            </Button>
                            <Button type="submit" :disabled="state.type === 'submittingBooking'">Book Now</Button>
                        </div>
                    </form>
                </div>
                <p v-if="state.type === 'bookingConfirmed'" class="text-sm font-medium text-green-600">
                    Your meeting room has been successfully reserved. You'll find it listed in the upcoming reservations.
                </p>
            </div>
            <div>
                <div class="mb-8 space-y-0.5">
                    <h2 class="text-xl font-semibold tracking-tight">Upcoming Reservations</h2>
                    <p class="text-muted-foreground text-sm">View all scheduled meeting room reservations.</p>
                </div>
                <div class="grid">
                    <Table>
                        <TableCaption>Current meeting room reservations</TableCaption>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Room Name</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead>Timeslot</TableHead>
                                <TableHead>Reserved By</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="booking in existingBookings" :key="booking.id">
                                <TableCell>{{ booking.room.name }}</TableCell>
                                <TableCell>{{ booking.date }}</TableCell>
                                <TableCell>{{ booking.start_time }} - {{ booking.end_time }}</TableCell>
                                <TableCell>{{ booking.user_name }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
