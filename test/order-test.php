<?php
// first, require the SimpleTest framework <http://www.simpletest.org/>
require_once("/usr/lib/php5/simpletest/autorun.php");

// the class to test
require_once("../php/classes/order.php");

// require the encrypted configuration functions
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");


/**
 * Unit test for the Order class
 *
 * This is a SimpleTest test case for the CRUD methods of the Order class.
 *
 * @see Order
 * @author Dylan McDonald <dmcdonald21@cnm.edu>
 **/
class OrderTest extends UnitTestCase {
	/**
	 * mysqli object shared amongst all tests
	 **/
	private $mysqli = null;
	/**
	 * instance of the object we are testing with
	 **/
	private $order = null;

	// this section contains member variables with constants needed for creating a new order
	/**
	 * @var int $profileId id for the profile. This is a foreign key to the profile entity.
	 */
	private $orderId = 1;

	/**
	 * @var int $profileId id for the profile. This is a foreign key to the profile entity.
	 */
	private $profileId = 1;

	/**
	 * @var string $orderDate name of the order
	 */
	private $orderDate = null;
	/**
	 * sets up the mySQL connection for this test
	 **/
	public function setUp() {
		// get the credentials information from the server
		$configFile = "/etc/apache2/capstone-mysql/farmtoyou.ini";
		$configArray = readConfig($configFile);

		// connection
		mysqli_report(MYSQLI_REPORT_STRICT);
		$this->mysqli = new mysqli($configArray["hostname"], $configArray["username"], $configArray["password"],
			$configArray["database"]);

		// instance of order
		$this->orderDate = new DateTime();
		$this->order = new Order(null, $this->profileId, $this->orderDate);
	}

	/**
	 * tears down the connection to mySQL and deletes the test instance object
	 **/
	public function tearDown() {
		// destroy the object if it was created
		if($this->order !== null) {
			$this->order->delete($this->mysqli);
			$this->order = null;
		}

		// disconnect from mySQL
		if($this->mysqli !== null) {
			$this->mysqli->close();
			$this->mysqli = null;
		}
	}

	/**
	 * test inserting a valid Order into mySQL
	 **/
	public function testInsertValidOrder() {
		// zeroth, ensure the Order and mySQL class are sane
		$this->assertNotNull($this->order);
		$this->assertNotNull($this->mysqli);

		// first, insert the Order into mySQL
		$this->order->insert($this->mysqli);

		// second, grab a Order from mySQL
		$mysqlOrder = Order::getOrderByOrderId($this->mysqli, $this->order->getOrderId());

		// third, assert the Order we have created and mySQL's Order are the same object
		$this->assertIdentical($this->order->getOrderId(), $mysqlOrder->getOrderId());
		$this->assertIdentical($this->order->getProfileId(), $mysqlOrder->getProfileId());
		$this->assertIdentical($this->order->getOrderDate(), $mysqlOrder->getOrderDate());
	}

	/**
	 * test inserting an invalid Order into mySQL
	 **/
	public function testInsertInvalidOrder() {
		$this->assertNotNull($this->order);
		$this->assertNotNull($this->mysqli);

		// first, set the order id to an invented value that should never insert in the first place
		$this->order->setOrderId(42);

		// second, try to insert the Order and ensure the exception is thrown
		$this->expectException("mysqli_sql_exception");
		$this->order->insert($this->mysqli);

		// third, set the Order to null to prevent tearDown() from deleting a Tweet that never existed
		$this->order = null;
	}

	/**
	 * test deleting a Order from mySQL
	 **/
	public function testDeleteValidOrder() {
		$this->assertNotNull($this->order);
		$this->assertNotNull($this->mysqli);

		// first, assert the Order is inserted into mySQL by grabbing it from mySQL and asserting the primary key
		$this->order->insert($this->mysqli);
		$mysqlOrder = Order::getOrderByOrderId($this->mysqli, $this->order->getOrderId());
		$this->assertIdentical($this->order->getOrderId(), $mysqlOrder->getOrderId());

		// second, delete the Order from mySQL and re-grab it from mySQL and assert it does not exist
		$this->order->delete($this->mysqli);
		$mysqlOrder = Order::getOrderByOrderId($this->mysqli, $this->order->getOrderId());
		$this->assertNull($mysqlOrder);

		// third, set the Order to null to prevent tearDown() from deleting a Order that has already been deleted
		$this->order = null;
	}

	/**
	 * test deleting a Order from mySQL that does not exist
	 **/
	public function testDeleteInvalidOrder() {
		$this->assertNotNull($this->order);
		$this->assertNotNull($this->mysqli);

		// first, try to delete the Order before inserting it and ensure the exception is thrown
		$this->expectException("mysqli_sql_exception");
		$this->order->delete($this->mysqli);

		$this->order = null;
	}

	/**
	 * test updating a Order from mySQL
	 **/
	public function testUpdateValidOrder() {
		$this->assertNotNull($this->order);
		$this->assertNotNull($this->mysqli);

		$this->order->insert($this->mysqli);
		$mysqlOrder = Order::getOrderByOrderId($this->mysqli, $this->order->getOrderId());

		$this->assertIdentical($this->order->getOrderId(), $mysqlOrder->getOrderId());

		// second, change the Order, update it mySQL
		$newDate = new DateTime();
		$this->order->setOrderDate($newDate);
		$this->order->update($this->mysqli);

		$mysqlOrder = Order::getOrderByOrderId($this->mysqli, $this->order->getOrderId());
		$this->assertNotNull($mysqlOrder);

		// fourth, assert the Order we have updated and mySQL's Order are the same object
		$this->assertIdentical($this->order->getOrderId(), $mysqlOrder->getOrderId());
		$this->assertIdentical($this->order->getProfileId(), $mysqlOrder->getProfileId());
		$this->assertIdentical($this->order->getOrderDate(), $mysqlOrder->getOrderDate());
	}

	/**
	 * test updating a Order from mySQL that does not exist
	 **/
	public function testUpdateInvalidOrder() {
		$this->assertNotNull($this->order);
		$this->assertNotNull($this->mysqli);

		// first, try to update the Order before inserting it and ensure the exception is thrown
		$this->expectException("mysqli_sql_exception");
		$this->order->update($this->mysqli);

		// second, set the Order to null to prevent tearDown() from deleting a Order that has already been deleted
		$this->order = null;
	}

	/**
	 * test getting all the orders
	 **/
	public function testGetAllOrders() {
		$this->assertNotNull($this->order);
		$this->assertNotNull($this->mysqli);

		$this->order->insert($this->mysqli);

		// create a new order
		$this->order->setProfileId(2);
		$this->order->setOrderDate(new DateTime());
		$this->order->insert($this->mysqli);

		$mysqlOrder = Order::getAllOrders($this->mysqli);
		$this->assertIdentical($this->order->getOrderId(), $mysqlOrder->getOrderId());

		// second, change the Order, update it mySQL
		$newDate = new DateTime();
		$this->order->setOrderDate($newDate);
		$this->order->update($this->mysqli);

		$mysqlOrder = Order::getOrderByOrderId($this->mysqli, $this->order->getOrderId());
		$this->assertNotNull($mysqlOrder);

		// fourth, assert the Order we have updated and mySQL's Order are the same object
		$this->assertIdentical($this->order->getOrderId(), $mysqlOrder->getOrderId());
		$this->assertIdentical($this->order->getProfileId(), $mysqlOrder->getProfileId());
		$this->assertIdentical($this->order->getOrderDate(), $mysqlOrder->getOrderDate());

		$this->order = null;
	}

}
?>