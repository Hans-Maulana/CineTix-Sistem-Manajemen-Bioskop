<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Studio;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic roles needed for user creation
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'customer']);
    }

    /**
     * Test that overlapping schedules in the same studio on the same day are rejected,
     * while schedules with touching boundaries are allowed.
     */
    public function test_cannot_create_overlapping_schedule(): void
    {
        // 1. Create admin user and authenticate
        $admin = User::create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'name'     => 'Admin Test',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'contact'  => '08123456789',
        ]);

        $this->actingAs($admin);

        // 2. Create Films & Studio
        $film1 = Film::create([
            'title'          => 'Test Film 1',
            'synopsis'       => 'Synopsis test 1',
            'duration'       => 120,
            'rating'         => 0,
            'actors'         => 'Actor 1',
            'director'       => 'Director 1',
            'production'     => 'Prod 1',
            'status'         => 'now_playing',
            'classification' => 'SU',
            'release_date'   => '2026-01-01',
        ]);

        $film2 = Film::create([
            'title'          => 'Test Film 2',
            'synopsis'       => 'Synopsis test 2',
            'duration'       => 90,
            'rating'         => 0,
            'actors'         => 'Actor 2',
            'director'       => 'Director 2',
            'production'     => 'Prod 2',
            'status'         => 'now_playing',
            'classification' => 'SU',
            'release_date'   => '2026-01-01',
        ]);

        $studio = Studio::create([
            'name'     => 'Studio Test',
            'capacity' => 50,
        ]);

        // 3. Create initial schedule (10:00 - 12:00) via raw DB insert to avoid
        //    the datetime:H:i Eloquent cast storing a full datetime string in SQLite,
        //    which would break time-string comparisons in the overlap query.
        DB::table('schedules')->insert([
            'film_id'       => $film1->id,
            'studio_id'     => $studio->id,
            'schedule_date' => '2026-06-01',
            'start_time'    => '10:00:00',
            'end_time'      => '12:00:00',
            'ticket_price'  => 50000,
            'status'        => 'on schedule',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->assertEquals(1, Schedule::count());

        // 4. Try to add an overlapping schedule (11:00 - 13:00) — should FAIL
        $overlap = $this->post(route('admin.schedules.store'), [
            'film_id'       => $film2->id,
            'studio_id'     => $studio->id,
            'schedule_date' => '2026-06-01',
            'start_time'    => '11:00',
            'end_time'      => '13:00',
            'ticket_price'  => 50000,
        ]);

        $overlap->assertSessionHas('error');
        $this->assertEquals(1, Schedule::count(), 'Overlapping schedule should NOT be created');

        // 5. Try to add a different studio schedule with same time — should SUCCEED
        $studio2 = Studio::create(['name' => 'Studio 2', 'capacity' => 50]);

        $diffStudio = $this->post(route('admin.schedules.store'), [
            'film_id'       => $film2->id,
            'studio_id'     => $studio2->id,
            'schedule_date' => '2026-06-01',
            'start_time'    => '11:00',
            'end_time'      => '13:00',
            'ticket_price'  => 50000,
        ]);

        $diffStudio->assertSessionHas('success');
        $this->assertEquals(2, Schedule::count(), 'Different studio schedule should be created');

        // 6. Try to add a touching-boundary schedule (12:00 - 14:00) — should SUCCEED
        //    because 12:00 == 12:00 is NOT overlap (strictly greater-than, not >=)
        DB::table('schedules')->delete(); // reset to just the first schedule
        DB::table('schedules')->insert([
            'film_id'       => $film1->id,
            'studio_id'     => $studio->id,
            'schedule_date' => '2026-06-01',
            'start_time'    => '10:00:00',
            'end_time'      => '12:00:00',
            'ticket_price'  => 50000,
            'status'        => 'on schedule',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $touching = $this->post(route('admin.schedules.store'), [
            'film_id'       => $film2->id,
            'studio_id'     => $studio->id,
            'schedule_date' => '2026-06-01',
            'start_time'    => '12:00',
            'end_time'      => '14:00',
            'ticket_price'  => 50000,
        ]);

        $touching->assertSessionHas('success');
        $this->assertEquals(2, Schedule::count(), 'Touching-boundary schedule should be created');
    }
}
