<?php
/**
 * Class PostModelTest.
 */
use Carbon\Carbon;

/**
 * Sample test case.
 */
class PostModelTest extends WP_UnitTestCase
{
    protected function newEvent($args = [])
    {
        return new Event(wp_parse_args($args, [
            'title' => 'Test Event',
            'start' => Carbon::now(),
            'end'   => Carbon::tomorrow(),
        ]));
    }

    protected function createEvent($args = [])
    {
        return ($this->newEvent($args))->save();
    }

    public function test_events_can_have_title()
    {
        $event = new Event([
            'title' => 'Pit Stop 2017',
        ]);

        $this->assertEquals('Pit Stop 2017', $event->title);
    }

    public function test_events_can_have_a_start_and_end()
    {
        $start = Carbon::today();
        $end = Carbon::tomorrow();

        $event = new Event([
            'start' => $start,
            'end'   => $end,
        ]);

        $this->assertEquals($start, $event->start);
        $this->assertEquals($end, $event->end);
    }

    public function test_events_can_be_persisted()
    {
        $event = $this->newEvent();
        $event->save();

        $this->assertFalse(empty($event->id));
    }

    public function test_events_can_be_retrieved()
    {
        $original = $this->createEvent();

        $event = Event::find($original->id);

        $this->assertFalse(empty($event));
        $this->assertEquals('Test Event', $event->title);
    }

    public function test_events_are_stored_with_proper_post_type()
    {
        $event = $this->createEvent();

        $this->assertEquals('event', get_post_type($event->id));
    }

    public function test_dates_are_casted_to_carbon_attributes()
    {
        $ev1 = $this->createEvent();

        $event = Event::find($ev1->id);

        $this->assertInstanceOf(Carbon::class, $event->start);
    }

    public function test_dates_are_casted_from_carbon_attributes()
    {
        $date = Carbon::now();
        $ev1 = $this->createEvent([
            'start' => $date,
        ]);

        $event = Event::find($ev1->id);

        $this->assertInstanceOf(Carbon::class, $event->start);
        $meta = get_post_meta($event->id, 'start', true);

        $this->assertEquals($date->toDateTimeString(), $meta, 'Attributes should be casted back when stored');
    }

    public function test_titles_can_be_updated()
    {
        $ev1 = $this->createEvent();

        $ev1->title = 'Foo Event';
        $ev1->save();

        $event = Event::find($ev1->id);

        $this->assertEquals('Foo Event', $event->title);
    }

    public function test_content_can_be_persisted()
    {
        $ev1 = $this->createEvent([
            'content' => 'This is an event',
        ]);

        $this->assertEquals('This is an event', $ev1->content);

        $event = Event::find($ev1->id);

        $this->assertEquals('This is an event', $event->content);
    }

    public function test_all_day_casted_to_boolean()
    {
        $ev1 = $this->createEvent();

        $this->assertSame(false, $ev1->all_day);

        $ev1->all_day = true;

        $this->assertSame(true, $ev1->all_day);

        $ev1->save();

        $meta = get_post_meta($ev1->id, 'all_day', true);

        $this->assertSame('1', $meta);

        $event = Event::find($ev1->id);

        $this->assertSame(true, $event->all_day);
    }

    public function test_post_type_can_be_registered()
    {
        $postTypes = get_post_types();

        $this->assertTrue(array_key_exists((new Event)->getPostType(), $postTypes));
    }

    public function test_events_can_be_paginated()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->createEvent([
                'title' => "Event $i",
            ]);
        }

        $events = Event::limit(5)->get();

        $this->assertCount(5, $events);

        $moreEvents = Event::limit(5)
                            ->offset(5)
                            ->get();

        $this->assertCount(5, $moreEvents);
        $this->assertCount(0, $events->intersect($moreEvents));
    }
}
