<?php

// first, require SimpleTest framework <http://www.simpletest.org/>
// this path is *NOT* universal, but deployed on the bootcamp-coders server
require_once ("/usr/lib/php5/simpletest/autorun.php");

// next, require the class from the project under scrutiny

require_once ("../php/classes/store.php");

require_once("../php/misc/load-config.php");
/**
 * Unit test for the Store class
 *
 * This is a SimpleTest test case for the CRUD methods of the Store class.
 *
 * @see Tweet
 * @author Dylan McDonald <dmcdonald21@cnm.edu>
 **/
class StoreTest extends UnitTestCase {
	/**
	 * mysqli object shared amongst all tests
	 **/
	private $mysqli = null;
	/**
	 * instance of the object we are testing with
	 **/
	private $store = null;

// this section contains member variables with constants needed for creating a new tweet
	/**
	 * profile id of the person who is inserting the test Tweet
	 * @deprecated a parent class of type Profile should be used here instead
	 **/
	private $profileId = 1;
	/**
	 * date the Tweet was created
	 **/
	private $creationDate = null;
	/**
	 * content of the test Tweet
	 **/
	private $storeName = "Pass Farms";
	/**
	 * content of the test Tweet
	 **/
	private $imagePath = "http://www.google.com";

	/**
	 * sets up the mySQL connection for this test
	 **/
	public function setUp() {
// first, connect to mysqli
		mysqli_report(MYSQLI_REPORT_STRICT);
		/** @var TYPE_NAME $configArray */
		$this->mysqli = new mysqli($configArray["hostname"], $configArray["username"], $configArray["password"], $configArray["database"]);

// second, create an instance of the object under scrutiny
		$this->creationDate = new DateTime();
		$this->store = new Store(null, $this->profileId, $this->creationDate, $this->storeName, $this->imagePath);
	}

	/**
	 * tears down the connection to mySQL and deletes the test instance object
	 **/
	public function tearDown() {
// destroy the object if it was created
		if($this->store !== null) {
			$this->store->delete($this->mysqli);
			$this->store = null;
		}

// disconnect from mySQL
		if($this->mysqli !== null) {
			$this->mysqli->close();
			$this->mysqli = null;
		}
	}

	/**
	 * test inserting a valid Tweet into mySQL
	 **/
	public function testInsertValidStore() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->store);
		$this->assertNotNull($this->mysqli);

// first, insert the Tweet into mySQL
		$this->store->insert($this->mysqli);

// second, grab a Tweet from mySQL
		$mysqlStore = Store::getStoreByStoreId($this->mysqli, $this->store->getStoreId());

// third, assert the Tweet we have created and mySQL's Tweet are the same object
		$this->assertIdentical($this->store->getStoreId(), $mysqlStore->getStoreId());
		$this->assertIdentical($this->store->getProfileId(), $mysqlStore->getProfileId());
		$this->assertIdentical($this->store->getCreationDate(), $mysqlStore->getCreationDate());
		$this->assertIdentical($this->store->getStoreName(), $mysqlStore->getStoreName());
		$this->assertIdentical($this->store->getImagePath(), $mysqlStore->getImagePath());

	}

	/**
	 * test inserting an invalid Tweet into mySQL
	 **/
	public function testInsertInvalidStore() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->store);
		$this->assertNotNull($this->mysqli);

// first, set the tweet id to an invented value that should never insert in the first place
		$this->store->setStoreId(1042);

// second, try to insert the Tweet and ensure the exception is thrown
		$this->expectException("mysqli_sql_exception");
		$this->store->insert($this->mysqli);

// third, set the Tweet to null to prevent tearDown() from deleting a Tweet that never existed
		$this->store = null;
	}

	/**
	 * test deleting a Tweet from mySQL
	 **/
	public function testDeleteValidStore() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->store);
		$this->assertNotNull($this->mysqli);

// first, assert the Tweet is inserted into mySQL by grabbing it from mySQL and asserting the primary key
		$this->store->insert($this->mysqli);
		$mysqlStore = Store::getStoreByStoreId($this->mysqli, $this->store->getStoreId());
		$this->assertIdentical($this->store->getStoreId(), $mysqlStore->getStoreId());

// second, delete the Tweet from mySQL and re-grab it from mySQL and assert it does not exist
		$this->store->delete($this->mysqli);
		$mysqlStore = Store::getStoreByStoreId($this->mysqli, $this->store->getStoreId());
		$this->assertNull($mysqlStore);

// third, set the Tweet to null to prevent tearDown() from deleting a Tweet that has already been deleted
		$this->store = null;
	}

	/**
	 * test deleting a Tweet from mySQL that does not exist
	 **/
	public function testDeleteInvalidStore() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->store);
		$this->assertNotNull($this->mysqli);

// first, try to delete the Tweet before inserting it and ensure the exception is thrown
		$this->expectException("mysqli_sql_exception");
		$this->store->delete($this->mysqli);

// second, set the Tweet to null to prevent tearDown() from deleting a Tweet that has already been deleted
		$this->store = null;
	}

	/**
	 * test updating a Tweet from mySQL
	 **/
	public function testUpdateValidStore() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->store);
		$this->assertNotNull($this->mysqli);

// first, assert the Tweet is inserted into mySQL by grabbing it from mySQL and asserting the primary key
		$this->store->insert($this->mysqli);
		$mysqlStore = Store::getStoreByStoreId($this->mysqli, $this->store->getStoreId());
		$this->assertIdentical($this->store->getStoreId(), $mysqlStore->getStoreId());

// second, change the Tweet, update it mySQL
		$newContent = "Updated Farms";
		$this->store->setStoreName($newContent);
		$this->store->update($this->mysqli);

// third, re-grab the Tweet from mySQL
		$mysqlStore = Store::getStoreByStoreId($this->mysqli, $this->store->getStoreId());
		$this->assertNotNull($mysqlStore);

// fourth, assert the Tweet we have updated and mySQL's Tweet are the same object
		$this->assertIdentical($this->store->getStoreId(), $mysqlStore->getStoreId());
		$this->assertIdentical($this->store->getProfileId(), $mysqlStore->getProfileId());
		$this->assertIdentical($this->store->getCreationDate(), $mysqlStore->getCreationDate());
		$this->assertIdentical($this->store->getStoreName(), $mysqlStore->getStoreName());
		$this->assertIdentical($this->store->getImagePath(), $mysqlStore->getImagePath());
	}

	/**
	 * test updating a Tweet from mySQL that does not exist
	 **/
	public function testUpdateInvalidStore() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->store);
		$this->assertNotNull($this->mysqli);

// first, try to update the Tweet before inserting it and ensure the exception is thrown
		$this->expectException("mysqli_sql_exception");
		$this->store->update($this->mysqli);

// second, set the Tweet to null to prevent tearDown() from deleting a Tweet that has already been deleted
		$this->store = null;
	}
}
?>