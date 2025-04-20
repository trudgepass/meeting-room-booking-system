<?php

namespace Tests\Browser;

use App\Models\Room;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BookingTest extends DuskTestCase
{
    public function testCompleteRoomBookingFlow()
    {
        $this->browse(function (Browser $browser) {
            // Step 1: Visit the homepage
            $browser->visit('/')
                ->waitForText('Meeting Room Reservation')
                ->assertSee('Select a date and time for your meeting room reservation');

            // Step 2: Set the date and time for booking
            $tomorrow = Carbon::tomorrow()->format('Y-m-d');
            $tomorrowInput = Carbon::tomorrow()->format('d-m-Y');
            $browser->type('date', $tomorrowInput)
                ->type('start_time', '10:00')
                ->type('end_time', '11:00');

            // Step 3: Check availability
            $browser->press('Check Availability')
                ->waitForText('Complete your reservation for');

            // Step 4: Select a room and enter user information
            $browser->press('Choose an available room')
                ->click('@room-1')
                ->type('user_name', 'John Doe');

            // Step 5: Complete the booking
            $browser->press('Book Now')
                ->waitForText('Your meeting room has been successfully reserved');

            // Step 6: Verify the booking appears in the list
            $browser->assertSee('Upcoming Reservations')
                ->assertSee('John Doe')
                ->assertSee('10:00 - 11:00')
                ->assertSee($tomorrow);
        });
    }
}
