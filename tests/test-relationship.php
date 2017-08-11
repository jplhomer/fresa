<?php
/**
 * Class RelationshipTest
 *
 * @package Fresa
 */

use Carbon\Carbon;

/**
 * Sample test case.
 */
class RelationshipTest extends WP_UnitTestCase {
	protected function createEvent($args = [])
	{
		return Event::create( wp_parse_args($args, [
			'name' => "Test Event",
			'start' => Carbon::now(),
			'end' => Carbon::tomorrow(),
		]));
	}

	public function test_events_can_have_categories()
	{
		$event = $this->createEvent();

		$this->assertTrue($event->categories instanceof Illuminate\Support\Collection);
	}

	public function test_categories_must_exist_when_added()
	{
		$event = $this->createEvent();

		$this->expectException(\Exception::class);

		$event->categories()->save(
			new Category([
				'name' => "Foo",
			])
		);
	}

	public function test_events_can_add_categories()
	{
		$event = $this->createEvent();

		$event->categories()->save(
			Category::create([
				'name' => "Foo",
			])
		);

		$this->assertCount(1, $event->categories);
		$this->assertTrue($event->categories->first() instanceof Category);
	}

	public function test_events_can_persist_categories()
	{
		$event = $this->createEvent();
		$category = Category::create([
			'name' => "Foo",
			'slug' => 'foo',
		]);

		$event->categories()->save($category);

		$ev = Event::find($event->id);

		$this->assertCount(1, $ev->categories);
		$this->assertEquals($category, $ev->categories->first());

		$cat2 = Category::create([
			'name' => "Bar",
		]);

		$event->categories()->save($cat2);

		$this->assertCount(2, Event::find($event->id)->categories);

		// Make sure we aren't hydrating the taxonomy too often
		$this->assertCount(2, $event->categories);
	}

	public function test_duplicate_categories_cannot_be_added_to_relationship()
	{
		$event = $this->createEvent();
		$category = Category::create([
			'name' => "Foo",
			'slug' => 'foo',
		]);

		$event->categories()->save($category);
		$event->categories()->save($category);

		$this->assertCount(1, $event->categories);
	}

	public function test_posts_can_by_queried_by_category()
	{
		$category = Category::create([
			'name' => "Foo",
		]);

		$this->assertTrue($category->events instanceof Illuminate\Support\Collection);

		$event = $this->createEvent();

		$category->events()->save($event);

		$this->assertCount(1, $category->events);

		$posts = Category::find($category->id)->events()->get();

		// Create another event
		$this->createEvent();

		$this->assertCount(1, $category->events);
		$this->assertEquals($event->id, $category->events->first()->id);
	}
}
