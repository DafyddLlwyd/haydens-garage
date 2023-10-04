<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private $postData; // Variable to hold the posted data

    protected function setUp(): void
    {
        parent::setUp();

        // set date to next monday at 9:00am
        $date = new \DateTime('next monday 9:00am');

        // Define the posted data here
        $this->postData = [
            'name' => 'John Doe',
            'email' => 'John.Doe@demo.com',
            'phone' => '0123456789',
            'vehicleMake' => 'Toyota',
            'vehicleModel' => 'Camry',
            'bookingDateTime' => $date->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }

    public function test_booking_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_admin_page(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(200);
    }

    public function test_booking(): void
    {
        $response = $this->post('api/book', $this->postData);

        // format the date
        $date = \DateTime::createFromFormat("Y-m-d\TH:i:s.u\Z", $this->postData['bookingDateTime'])->format('Y-m-d H:i:s');

        // Check if the booking was created
        $this->assertDatabaseHas('bookings', [
            'name' => $this->postData['name'],
            'email' => $this->postData['email'],
            'phone' => $this->postData['phone'],
            'vehicle_make' => $this->postData['vehicleMake'],
            'vehicle_model' => $this->postData['vehicleModel'],
            'booking_datetime' => $date,
        ]);
    }

    public function test_booking_time_between_nine_am_and_five_thirty_pm(): void
    {
        $data = $this->postData;
        // change the time to 8:00am
        $data['bookingDateTime'] = '2023-10-01T08:00:00.000Z';

        $response = $this->post('api/book', $data);

        $response->assertSessionHasErrors([
            'bookingDateTime' => 'The booking date and time must be between 9:00am and 5:30pm.',
        ]);

        // change the time to 5:31pm
        $data['bookingDateTime'] = '2023-10-01T17:31:00.000Z';

        $response = $this->post('api/book', $data);

        $response->assertSessionHasErrors([
            'bookingDateTime' => 'The booking date and time must be between 9:00am and 5:30pm.',
        ]);
    }

    public function test_booking_one_booking_per_slot(): void
    {
        DB::table('bookings')->insert([
            'name' => $this->postData['name'],
            'email' => $this->postData['email'],
            'phone' => $this->postData['phone'],
            'vehicle_make' => $this->postData['vehicleMake'],
            'vehicle_model' => $this->postData['vehicleModel'],
            'booking_datetime' => $this->postData['bookingDateTime'],
        ]);

        // Check if the booking was created
        $this->assertDatabaseHas('bookings', [
            'name' => $this->postData['name'],
            'email' => $this->postData['email'],
            'phone' => $this->postData['phone'],
            'vehicle_make' => $this->postData['vehicleMake'],
            'vehicle_model' => $this->postData['vehicleModel'],
            'booking_datetime' => $this->postData['bookingDateTime'],
        ]);

        $response = $this->post('api/book', $this->postData);

        $response->assertSessionHasErrors([
            'bookingDateTime' => 'There is already a booking for this date and time.',
        ]);
    }

    public function test_cannot_book_blocked_date(): void
    {
        DB::table('blocked_dates')->insert([
            'locked_date' => '2023-10-04',
        ]);

        $data = $this->postData;
        // change the date to 2023-10-04
        $data['bookingDateTime'] = '2023-10-04T09:00:00.000Z';

        $response = $this->post('api/book', $data);

        $response->assertSessionHasErrors([
            'bookingDateTime' => 'This date is unavailable and cannot be booked. Please choose another date.',
        ]);
    }

    public function test_cannot_book_weekends(): void
    {
        $data = $this->postData;
        // change the date to 2023-10-07 (Saturday)
        $data['bookingDateTime'] = '2023-10-07T09:00:00.000Z';

        $response = $this->post('api/book', $data);

        $response->assertSessionHasErrors([
            'bookingDateTime' => 'The garage is closed on weekends. Please choose another date.',
        ]);

        // change the date to 2023-10-08 (Sunday)
        $data['bookingDateTime'] = '2023-10-08T09:00:00.000Z';

        $response = $this->post('api/book', $data);

        $response->assertSessionHasErrors([
            'bookingDateTime' => 'The garage is closed on weekends. Please choose another date.',
        ]);
    }
}
