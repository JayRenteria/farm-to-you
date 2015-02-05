<?php
// first, require the SimpleTest framework <http://www.simpletest.org/>
// this path is *NOT* universal, but deployed on the bootcamp-coders server
require_once("/usr/lib/php5/simpletest/autorun.php");

// next, require the class from the project under scrutiny
require_once("../php/classes/categoryProduct.php");
require_once ("../php/classes/user.php");
require_once ("../php/classes/profile.php");
require_once ("../php/classes/product.php");
require_once("../php/classes/category.php");

require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

/**
 * unit test for the CategoryProduct class
 *
 * This is a simpletest test case for the CRUD methods of the Category class
 *
 * @see category
 * @author Jay Renteria <jay@jayrenteria.com>
 *
 **/

class CategoryProductTest extends UnitTestCase {
	/**
	 * mysqli object shared amongst all tests
	 **/
	private $mysqli = null;
	/**
	 * instance of the object we are testing with
	 **/
	private $categoryProduct = null;

	// this section contains member variables with constants needed for creating a new category

	private $category = null;
	private $product = null;
	private $user = null;
	private $profile = null;


	/**
	 * sets up the mySQL connection for this test
	 **/
	public function setUp() {
		// first connect to mysqli
		mysqli_report(MYSQLI_REPORT_STRICT);
		$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
		$this->mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

		// second create an instance of the objects under scrutiny
		$this->user = new User(null, "test@test.com", 'AB10BC99AB10BC99AB10BC99AB10BC99AB10BC99AB10BC99AB10BC99AB10BC99AB10BC99AB10BC99AB0BC99AB10BC99AC99AB0BC99AB10BC99AB10BC99AB1010', '99AB10BC99AB10BC99AB10BC99AB10BC', '99AB10BC99AB10BC');
		$this->user->insert($this->mysqli);
		$this->profile = new Profile(null, "Test", "Test2", "5555555555", "m", "012345", "http://www.cats.com/cat.jpg", $this->user->getUserId());
		$this->profile->insert($this->mysqli);
		$this->product = new Product(null, $this->profile->getProfileId(), "http://www.cats.com/cat.jpg", "test", "1.20", "test", 1.20 );
		$this->product->insert($this->mysqli);
		$this->category = new Category(null, "category");
		$this->category->insert($this->mysqli);

		// main object created here
		$this->categoryProduct = new CategoryProduct($this->category->getCategoryId(), $this->product->getProductId());
	}

	/**
	 * tears down the connection to mysql and deletes the test instance object
	 **/
	public function tearDown() {
		if($this->categoryProduct !== null) {
			$this->categoryProduct->delete($this->mysqli);
			$this->categoryProduct = null;
		}

		if($this->category !== null) {
			$this->category->delete($this->mysqli);
			$this->category = null;
		}

		if($this->product !== null) {
			$this->product->delete($this->mysqli);
			$this->product = null;
		}

		if($this->profile !== null) {
			$this->profile->delete($this->mysqli);
			$this->profile = null;
		}

		if($this->user !== null) {
			$this->user->delete($this->mysqli);
			$this->user = null;
		}

		// disconnect from mySQL
		if($this->mysqli !== null) {
			$this->mysqli->close();
			$this->mysqli = null;
		}
	}

	/**
	 * test inserting a valid category into mySQL
	 **/
	public function testInsertValidCategoryProduct() {
		// zeroth, ensure that the category and mySQL class are sane
		$this->assertNotNull($this->categoryProduct);
		$this->assertNotNull($this->mysqli);

		// first insert the category into mySQL
		$this->categoryProduct->insert($this->mysqli);

		// second, grab a categoryproduct from mySQL
		$mysqlCategoryProduct = CategoryProduct::getCategoryProductByCategoryIdAndProductId($this->mysqli, $this->categoryProduct->getCategoryId(),
			$this->categoryProduct->getProductId());

		// third, assert the categoryproduct we have created and mySQL's category are the same object
		$this->assertIdentical($this->categoryProduct->getCategoryId(), $mysqlCategoryProduct->getCategoryId());
		$this->assertIdentical($this->categoryProduct->getProductId(), $mysqlCategoryProduct->getProductId());
	}

	/**
	 * test inserting an invalid category into mySQL
	 **/
	public function testInsertInvalidCategoryProduct() {
		// zeroth, ensure that the category and mySQL class are sane
		$this->assertNotNull($this->categoryProduct);
		$this->assertNotNull($this->mysqli);

		// first, set the category id to an invented value that should never insert in the first place
		$this->categoryProduct->setCategoryId(42);
		$this->categoryProduct->setProductId(42);

		// second, try to insert the category and ensure the execption is thrown
		$this->expectException("mysqli_sql_exception");
		$this->categoryProduct->insert($this->mysqli);

		// third, set the category to null to prevent tearDown() from deleting a category that never existed
		$this->categoryProduct = null;
	}

	/**
	 * test deleting a category in mySQL
	 **/
	public function testDeleteValidCategoryProduct() {
		// zeroth, ensure that the category and mySQL class are sane
		$this->assertNotNull($this->categoryProduct);
		$this->assertNotNull($this->mysqli);

		// first assert the category is inserted into mySQL by grabbing it from mySQL and asserting the primary key
		$this->categoryProduct->insert($this->mysqli);
		$mysqlCategoryProduct = CategoryProduct::getCategoryProductByCategoryIdAndProductId($this->mysqli, $this->categoryProduct->getCategoryId(),
			$this->categoryProduct->getProductId());

		$this->assertIdentical($this->categoryProduct->getCategoryId(), $mysqlCategoryProduct->getCategoryId());
		$this->assertIdentical($this->categoryProduct->getProductId(), $mysqlCategoryProduct->getProductId());

		// second delete the category from mySQL and re-grab it from mySQL and assert it does not exist
		$this->categoryProduct->delete($this->mysqli);
		$mysqliCategoryProduct = CategoryProduct::getCategoryProductByCategoryIdAndProductId($this->mysqli, $this->categoryProduct->getCategoryId(), $this->categoryProduct->getProductId());
		$this->assertNull($mysqliCategoryProduct);

		// third set the category to null to prevent tearDown() from deleting a category that has already been deleted
		$this->categoryProduct = null;
	}
}
?>
