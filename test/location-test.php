<?php

// first, require SimpleTest framework <http://www.simpletest.org/>
// this path is *NOT* universal, but deployed on the bootcamp-coders server
require_once ("/usr/lib/php5/simpletest/autorun.php");

// next, require the class from the project under scrutiny

require_once ("../php/classes/location.php");

require_once("/etc/apache2/capstone-mysql/encrypted-config.php");
/**
 * Unit test for the Store class
 *
 * This is a SimpleTest test case for the CRUD methods of the Store class.
 *
 * @see Tweet
 * @author Dylan McDonald <dmcdonald21@cnm.edu>
 **/
class LocationTest extends UnitTestCase {
	/**
	 * mysqli object shared amongst all tests
	 **/
	private $mysqli = null;
	/**
	 * instance of the object we are testing with
	 **/
	private $location = null;
	/**
	 * instance of the object we are testing with
	 **/
	private $location2 = null;

// this section contains member variables with constants needed for creating a new tweet
	/**
	 * profile id of the person who is inserting the test Tweet
	 * @deprecated a parent class of type Profile should be used here instead
	 **/
	private $country = "USA";
	/**
	 * date the Tweet was created
	 **/
	private $state = "NM";
	/**
	 * content of the test Tweet
	 **/
	private $city = "Corrales";
	/**
	 * content of the test Tweet
	 **/
	private $zipCode = "87048";
	/**
	 * content of the test Tweet
	 **/
	private $address1 = "1228 W La Entrada";
	/**
	 * content of the test Tweet
	 **/
	private $address2 = null;

	/**
	 * sets up the mySQL connection for this test
	 **/
	public function setUp() {
// first, connect to mysqli
		mysqli_report(MYSQLI_REPORT_STRICT);
		$configFile = "/etc/apache2/capstone-mysql/farmtoyou.ini";
		$configArray = readConfig($configFile);
		$this->mysqli = new mysqli($configArray["hostname"], $configArray["username"], $configArray["password"], $configArray["database"]);

// second, create an instance of the object under scrutiny
		$this->location = new Location(null, $this->country, $this->state, $this->city, $this->zipCode, $this->address1, $this->address2);
		$this->location2 = new Location(null, $this->country, $this->state, $this->city, $this->zipCode, $this->address1, $this->address2);
	}

	/**
	 * tears down the connection to mySQL and deletes the test instance object
	 **/
	public function tearDown() {
// destroy the object if it was created
		if($this->location !== null) {
			$this->location->delete($this->mysqli);
			$this->location = null;
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
	public function testInsertValidLocation() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->location);
		$this->assertNotNull($this->mysqli);

// first, insert the Tweet into mySQL
		$this->location->insert($this->mysqli);

// second, grab a Tweet from mySQL
		$mysqlLocation = Location::getLocationByLocationId($this->mysqli, $this->location->getLocationId());

// third, assert the Tweet we have created and mySQL's Tweet are the same object
		$this->assertIdentical($this->location->getLocationId(), $mysqlLocation->getLocationId());
		$this->assertIdentical($this->location->getCountry(), $mysqlLocation->getCountry());
		$this->assertIdentical($this->location->getState(), $mysqlLocation->getState());
		$this->assertIdentical($this->location->getCity(), $mysqlLocation->getCity());
		$this->assertIdentical($this->location->getZipCode(), $mysqlLocation->getZipCode());
		$this->assertIdentical($this->location->getAddress1(), $mysqlLocation->getAddress1());
		$this->assertIdentical($this->location->getAddress2(), $mysqlLocation->getAddress2());

	}

	/**
	 * test inserting an invalid Tweet into mySQL
	 **/
	public function testInsertInvalidLocation() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->location);
		$this->assertNotNull($this->mysqli);

// first, set the tweet id to an invented value that should never insert in the first place
		$this->location->setLocationId(1042);

// second, try to insert the Tweet and ensure the exception is thrown
		$this->expectException("mysqli_sql_exception");
		$this->location->insert($this->mysqli);

// third, set the Tweet to null to prevent tearDown() from deleting a Tweet that never existed
		$this->location = null;
	}

	/**
	 * test deleting a Tweet from mySQL
	 **/
	public function testDeleteValidLocation() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->location);
		$this->assertNotNull($this->mysqli);

// first, assert the Tweet is inserted into mySQL by grabbing it from mySQL and asserting the primary key
		$this->location->insert($this->mysqli);
		$mysqlLocation = Location::getLocationByLocationId($this->mysqli, $this->location->getLocationId());
		$this->assertIdentical($this->location->getLocationId(), $mysqlLocation->getLocationId());

// second, delete the Tweet from mySQL and re-grab it from mySQL and assert it does not exist
		$this->location->delete($this->mysqli);
		$mysqlLocation = Location::getLocationByLocationId($this->mysqli, $this->location->getLocationId());
		$this->assertNull($mysqlLocation);

// third, set the Tweet to null to prevent tearDown() from deleting a Tweet that has already been deleted
		$this->location = null;
	}

	/**
	 * test deleting a Tweet from mySQL that does not exist
	 **/
	public function testDeleteInvalidLocation() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->location);
		$this->assertNotNull($this->mysqli);

// first, try to delete the Tweet before inserting it and ensure the exception is thrown
		$this->expectException("mysqli_sql_exception");
		$this->location->delete($this->mysqli);

// second, set the Tweet to null to prevent tearDown() from deleting a Tweet that has already been deleted
		$this->location = null;
	}

	/**
	 * test updating a Tweet from mySQL
	 **/
	public function testUpdateValidLocation() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->location);
		$this->assertNotNull($this->mysqli);

// first, assert the Tweet is inserted into mySQL by grabbing it from mySQL and asserting the primary key
		$this->location->insert($this->mysqli);
		$mysqlLocation = Location::getLocationByLocationId($this->mysqli, $this->location->getLocationId());
		$this->assertIdentical($this->location->getLocationId(), $mysqlLocation->getLocationId());

// second, change the Tweet, update it mySQL
		$newCountry = "CA";
		$this->location->setCountry($newCountry);
		$this->location->update($this->mysqli);

// third, re-grab the Tweet from mySQL
		$mysqlLocation = Location::getLocationByLocationId($this->mysqli, $this->location->getLocationId());
		$this->assertNotNull($mysqlLocation);

// fourth, assert the Tweet we have updated and mySQL's Tweet are the same object
		$this->assertIdentical($this->location->getLocationId(), $mysqlLocation->getLocationId());
		$this->assertIdentical($this->location->getCountry(), $mysqlLocation->getCountry());
		$this->assertIdentical($this->location->getState(), $mysqlLocation->getState());
		$this->assertIdentical($this->location->getCity(), $mysqlLocation->getCity());
		$this->assertIdentical($this->location->getZipCode(), $mysqlLocation->getZipCode());
		$this->assertIdentical($this->location->getAddress1(), $mysqlLocation->getAddress1());
		$this->assertIdentical($this->location->getAddress2(), $mysqlLocation->getAddress2());
	}

	/**
	 * test updating a Tweet from mySQL that does not exist
	 **/
	public function testUpdateInvalidLocation() {
// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->location);
		$this->assertNotNull($this->mysqli);

// first, try to update the Tweet before inserting it and ensure the exception is thrown
		$this->expectException("mysqli_sql_exception");
		$this->location->update($this->mysqli);

// second, set the Tweet to null to prevent tearDown() from deleting a Tweet that has already been deleted
		$this->location = null;
	}

	public function testValidInsertCity() {
		// zeroth, ensure the Tweet and mySQL class are sane
		$this->assertNotNull($this->location);
		$this->assertNotNull($this->location2);
		$this->assertNotNull($this->mysqli);
// first, insert the Tweet into mySQL
		$this->location->insert($this->mysqli);
		$this->location2->insert($this->mysqli);
// second, grab a Tweet from mySQL
		$mysqlLocations = Location::getLocationByCity($this->mysqli, $this->location->getCity(), $this->location2->getCity());
// third, assert the Tweet we have created and mySQL's Tweet are the same object
		foreach($mysqlLocations as $mysqlLocation) {

			$this->assertIdentical($this->location->getLocationId(), $mysqlLocation->getLocationId());
			$this->assertIdentical($this->location->getCity(), $mysqlLocation->getCity());

		}


	}
}


?>