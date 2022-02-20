## About RISKHUB App

Custom command lines (create:ticket and process:ticket are scheduled at every 1 and every 5 minutes respectively)

- php artisan create:ticket (Generates dummy ticket)
- php artisan process:ticket (Processes ticket)
- php artisan read:stats (Outputs stats to screen)

Tests to be found at
- tests/Feature/TicketTest.php
- run using "phpunit"
- you'll notice a @test comment above each test, this allows my tests custom names

##Additional Notes:

app\Traits folder is not my code, but something I use to generate uuids instead of the default incremented integer
You will be able to see these uuids in the tickets table

This project does not include a front end at this stage

My riskhub database is MySQL, testing database is sqlite

I've used Faker to generate user name and email in the create:ticket command, however I assume these will rarely
be repeated, so a fixed set of names would have been better to use to demonstrate the author stat

