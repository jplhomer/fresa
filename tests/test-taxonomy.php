<?php
/**
 * Class TaxonomyTest
 *
 * @package Fresa
 */

/**
 * Sample test case.
 */
class TaxonomyTest extends WP_UnitTestCase {
	public function test_category_can_register_as_taxonomy()
	{
		$taxonomies = get_taxonomies();

		$this->assertTrue( array_key_exists((new Category)->getTaxonomy(), $taxonomies));
	}

	public function test_categories_can_be_created()
	{
		$cat = Category::create([
			'name' => "Fundraiser",
		]);

		$this->assertFalse(empty($cat->id));
		$this->assertEquals('Fundraiser', $cat->name);
	}

	public function test_categories_can_be_looked_up()
	{
		$cat = Category::create([
			'name' => "Fundraiser",
		]);

		$category = Category::find($cat->id);

		$this->assertEquals('Fundraiser', $cat->name);
	}

	public function test_description_can_be_persisted()
	{
		$cat = Category::create([
			'name' => "Fundraiser",
			'description' => "A fun time",
		]);

		$category = Category::find($cat->id);

		$this->assertEquals("A fun time", $category->description);
	}

	public function test_category_name_is_required()
	{
		$cat = new Category;

		$this->expectException(\Exception::class);

		$cat->save();
	}

	public function test_category_saving_with_custom_taxonomy()
	{
		$taxonomy = (new Category)->getTaxonomy();

		$this->assertNotEquals('category', $taxonomy);
	}

	public function test_call_categories_can_be_fetched()
	{
		$cat = Category::create([
			'name' => "Fundraiser",
			'description' => "A fun time",
			'slug' => 'fundraiser',
		]);

		$categories = Category::all();

		$this->assertCount(1, $categories);
		$this->assertEquals($cat, $categories->first());
	}

	public function test_categories_can_be_queried_by_slug()
	{
		$cat = Category::create([
			'name' => "Foo",
			'slug' => 'foo',
		]);

		$cats = Category::where('slug', 'foo');

		$this->assertCount(1, $cats);
		$this->assertEquals($cat->id, $cats->first()->id);
	}
}
