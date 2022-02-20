<?php

namespace Tests\Feature;


use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that a ticket can be added.
     *
     * @return void
     * @test
     */
    public function can_add_ticket()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'Name of Ticket Owner',
            'email' => 'Email of submission User'
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Ticket::all());
    }

    /**
     * A ticket must contain a subject
     * @test
    */

    public function a_ticket_has_subject(){
        //$this->withoutExceptionHandling();
        $response = $this->post('/tickets', [
            'subject' => '',
            'content' => 'Ticket Content',
            'author' => 'Name of Ticket Owner',
            'email' => 'Email of submission User'
        ]);

        $response->assertSessionHasErrors('subject');
    }

    /**
     * A ticket has content
     * @test
     */

    public function a_ticket_has_content(){
        //$this->withoutExceptionHandling();
        $response = $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => '',
            'author' => 'Name of Ticket Owner',
            'email' => 'Email of submission User'
        ]);

        $response->assertSessionHasErrors('content');
    }

    /**
     * A ticket has author
     * @test
     */

    public function a_ticket_has_author(){
        //$this->withoutExceptionHandling();
        $response = $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => '',
            'email' => 'Email of submission User'
        ]);

        $response->assertSessionHasErrors('author');
    }

    /**
     * A ticket has email
     * @test
     */

    public function a_ticket_has_email(){
        //$this->withoutExceptionHandling();
        $response = $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'Author Name',
            'email' => ''
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * A ticket status should default to false
     * @test
     */

    public function a_ticket_has_default_status(){
        //$this->withoutExceptionHandling();
        $response = $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'Author Name',
            'email' => 'Email Address'
        ]);

        $this->assertEquals(false, Ticket::first()->status);
    }

    /**
     * I can process a ticket
     * @test
     */

    public function can_process_ticket(){
        //$this->withoutExceptionHandling();
        $response = $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'Author Name',
            'email' => 'Email Address'
        ]);

        //should be false by default
        $this->assertEquals(false, Ticket::first()->status);

        //I will update the ticket
        $response = $this->patch('/tickets/'.Ticket::first()->id, [
            'status' => true
        ]);

        $response->assertStatus(200);

        //now should change o true
        $this->assertEquals(true, Ticket::first()->status);
    }

    /**
     * Get tickets by status
     * @test
     */
    public function get_tickets_by_status(){
        $this->withoutExceptionHandling();

        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'Author Name',
            'email' => 'Email Address'
        ]);
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'Author Name',
            'email' => 'Email Address'
        ]);
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'Author Name',
            'email' => 'Email Address',
            'status' => true
        ]);

        //get open tickets
        $response = $this->get('/tickets/open');
        $response->assertStatus(200);
        $tickets = $response->json();

        $this->assertCount(2, $tickets['data']);

        //get closed tickets
        $response = $this->get('/tickets/closed');
        $response->assertStatus(200);
        $tickets = $response->json();

        $this->assertCount(1, $tickets['data']);
    }

    /**
     * get tickets by author
     * @test
     */
    public function get_tickets_by_author(){
        $this->withoutExceptionHandling();

        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 1',
            'email' => 'user1@gmail.com'
        ]);
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 2',
            'email' => 'user2@gmail.com'
        ]);
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 1',
            'email' => 'user1@gmail.com'
        ]);

        //get tickets by user
        $response = $this->get('/users/user1@gmail.com/tickets');
        $response->assertStatus(200);
        $tickets = $response->json();
        $this->assertCount(2, $tickets['data']);
    }

    /**
     * test to check ticket stats - total count
     * @test
     */
    public function check_ticket_stats_total(){
        $this->withoutExceptionHandling();
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 2',
            'email' => 'user2@gmail.com'
        ]);
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 1',
            'email' => 'user1@gmail.com'
        ]);

        $response = $this->get('/stats');
        $response->assertStatus(200);

        $stats = $response->json();

        //should expect 2
        $this->assertEquals(2, $stats['total']);
    }

    /**
     * test to check ticket stats - unprocessed
     * @test
     */
    public function check_ticket_stats_unprocessed(){
        $this->withoutExceptionHandling();
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 2',
            'email' => 'user2@gmail.com',
            'status' => true
        ]);
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 1',
            'email' => 'user1@gmail.com'
        ]);

        $response = $this->get('/stats');
        $response->assertStatus(200);

        $stats = $response->json();

        //should expect 2
        $this->assertEquals(1, $stats['unprocessed']);
    }

    /**
     * test to check ticket stats - last processed
     * @test
     */
    public function check_ticket_stats_last_processed(){
        $this->withoutExceptionHandling();
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 2',
            'email' => 'user2@gmail.com'
        ]);
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 1',
            'email' => 'user1@gmail.com'
        ]);

        $response = $this->get('/stats');
        $response->assertStatus(200);

        $stats = $response->json();

        //should expect Never as none have been processed
        $this->assertEquals('Never', $stats['last_processed']);

        //I will update the ticket
        $this->patch('/tickets/'.Ticket::first()->id, [
            'status' => true
        ]);

        $response = $this->get('/stats');
        $stats = $response->json();

        //should expect a date as none have been processed
        $this->assertNotEquals('Never', $stats['last_processed']);
    }

    /**
     * get largest author contributor
     * @test
     */
    public function get_largest_contributor(){
        $this->withoutExceptionHandling();

        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 2',
            'email' => 'user2@gmail.com'
        ]);
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 1',
            'email' => 'user1@gmail.com'
        ]);
        $this->post('/tickets', [
            'subject' => 'Ticket Subject',
            'content' => 'Ticket Content',
            'author' => 'User 2',
            'email' => 'user2@gmail.com'
        ]);

        $response = $this->get('/stats');
        $response->assertStatus(200);

        $stats = $response->json();

        //should expect 'User 2'
        $this->assertEquals('User 2', $stats['author']);
    }
}
