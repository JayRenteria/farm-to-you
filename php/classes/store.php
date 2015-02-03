<?php
/**
 * This is the class for the store function of farmtoyou
 *
 * @author Alonso Indacochea <alonso@hermesdevelopment.com>
 **/

class Store {
	/**
	 * id for the store, this is the primary key
	 */
	private $storeId;

	/**
	 * profileId associated with the store; this is a foreign key
	 **/
	private $profileId;

	/**
	 * name of the store
	 **/
	private $storeName;

	/**
	 * path for the image of the store
	 **/
	private $imagePath;

	/**
	 * creation date of the store
	 **/
	private $creationDate;


	/**
	 * constructor for this store class
	 *
	 * @param int $newStoreId id of the store
	 * @param int $newProfileId id of the profile associated with the store
	 * @param mixed $newCreationDate date and time store was created or null if set to current date and time
	 * @param string $newStoreName name of the store
	 * @param string $newImagePath path of image associated with the store or null if none
	 * @throws InvalidArgumentException it data types are not valid
	 * @throws RangeException if data values are out of bounds (e.g. strings too long, negative integers)
	 **/
	public function __construct($newStoreId, $newProfileId, $newStoreName, $newImagePath = null, $newCreationDate = null) {
		try {
			$this->setStoreId($newStoreId);
			$this->setProfileId($newProfileId);
			$this->setCreationDate($newCreationDate);
			$this->setStoreName($newStoreName);
			$this->setImagePath($newImagePath);
		} catch(InvalidArgumentException $invalidArgument) {
			// rethrow the exception to the caller
			throw(new InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));
		} catch(RangeException $range) {
			// rethrow the exception to the caller
			throw(new RangeException($range->getMessage(), 0, $range));
		}
	}

	/**
	 * accessor method for the storeId
	 *
	 * @return int value of storeId
	 **/
	public function getStoreId() {
		return ($this->storeId);
	}

	/**
	 * mutator method for storeId
	 *
	 * @param int $newStoreId new value of $storeId
	 * @throws InvalidArgumentException if the $storeId is not an integer
	 * @throws RangeException if the $storeId is not positive
	 **/
	public function setStoreId($newStoreId) {
		if($newStoreId === null) {
			$this->storeId = null;
			return;
		}

		// verify the store id is valid
		$newStoreId = filter_var($newStoreId, FILTER_VALIDATE_INT);
		if($newStoreId === false) {
			throw(new InvalidArgumentException("store id is not a valid integer"));
		}
		// verify the store id is positive
		if($newStoreId <= 0) {
			throw(new RangeException("store id is not positive"));
		}
		// convert and store the user id
		$this->storeId = intval($newStoreId);
	}

	/**
	 * accessor method for the profile id
	 *
	 * @return int value of profile id
	 **/
	public function getProfileId() {
		return ($this->profileId);
	}

	/**
	 * mutator method for the profile id
	 *
	 * @param int $newProfileId new value of $profileId
	 * @throws InvalidArgumentException if the $profileId is not an integer
	 * @throws RangeException if the $profileId is not positive
	 **/
	public function setProfileId($newProfileId) {
		// verify the store id is valid
		$newProfileId = filter_var($newProfileId, FILTER_VALIDATE_INT);
		if($newProfileId === false) {
			throw(new InvalidArgumentException("profile id is not a valid integer"));
		}
		// verify the store id is positive
		if($newProfileId <= 0) {
			throw(new RangeException("profile id is not positive"));
		}
		// convert and store the user id
		$this->profileId = intval($newProfileId);
	}

	/**
	 * accessor method for creation date
	 *
	 * @return DateTime value of creation date
	 **/
	public function getCreationDate() {
		return ($this->creationDate);
	}

	/**
	 * mutator method for creation date
	 *
	 * @param mixed $newCreationDate creation date as a DateTime object or string (or null to load the current time)
	 * @throws InvalidArgumentException if $newCreationDate is not a valid object or string
	 * @throws RangeException if $newCreationDate is a date that does not exist
	 **/
	public function setCreationDate($newCreationDate) {
// base case: if the date is null, use the current date and time
		if($newCreationDate === null) {
			$this->creationDate = new DateTime();
			return;
		}

// base case: if the date is a DateTime object, there's no work to be done
		if(is_object($newCreationDate) === true && get_class($newCreationDate) === "DateTime") {
			$this->creationDate = $newCreationDate;
			return;
		}

// treat the date as a mySQL date string: Y-m-d H:i:s
		$newCreationDate = trim($newCreationDate);
		if((preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $newCreationDate, $matches)) !== 1) {
			throw(new InvalidArgumentException("creation date is not a valid date"));
		}

// verify the date is really a valid calendar date
		$year = intval($matches[1]);
		$month = intval($matches[2]);
		$day = intval($matches[3]);
		$hour = intval($matches[4]);
		$minute = intval($matches[5]);
		$second = intval($matches[6]);
		if(checkdate($month, $day, $year) === false) {
			throw(new RangeException("creation date $newCreationDate is not a Gregorian date"));
		}

// verify the time is really a valid wall clock time
		if($hour < 0 || $hour >= 24 || $minute < 0 || $minute >= 60 || $second < 0 || $second >= 60) {
			throw(new RangeException("creation date $newCreationDate is not a valid time"));
		}

// store the creation date
		$newCreationDate = DateTime::createFromFormat("Y-m-d H:i:s", $newCreationDate);
		$this->creationDate = $newCreationDate;
	}

	/**
	 * accessor method for store name
	 *
	 * @return string value of store name
	 **/
	public function getStoreName() {
		return ($this->storeName);
	}

	/**
	 * mutator method for tweet content
	 *
	 * @param string $newStoreName new value of tweet content
	 * @throws InvalidArgumentException if $newStoreName is not a string or insecure
	 * @throws RangeException if $newStoreName is > 100 characters
	 **/
	public function setStoreName($newStoreName) {
// verify that the store name is secure
		$newStoreName = trim($newStoreName);
		$newStoreName = filter_var($newStoreName, FILTER_SANITIZE_STRING);
		if(empty($newStoreName) === true) {
			throw(new InvalidArgumentException("store name is empty or insecure"));
		}

// verify the store name will fit in the database
		if(strlen($newStoreName) > 100) {
			throw(new RangeException("store name too large"));
		}

// store the store name
		$this->storeName = $newStoreName;
	}

	/**
	 * accessor method for image path
	 *
	 * @return string value of image path
	 **/
	public function getImagePath() {
		return ($this->imagePath);
	}

	/**
	 * mutator method for image path
	 *
	 * @param string $newImagePath new value of tweet content
	 * @throws InvalidArgumentException if $newImagePath is not a string or insecure
	 * @throws RangeException if $newImagePath is > 100 characters
	 **/
	public function setImagePath($newImagePath) {
// verify that the image path is secure
		$newImagePath = trim($newImagePath);
		$newImagePath = filter_var($newImagePath, FILTER_SANITIZE_STRING);
		if(empty($newImagePath) === true) {
			throw(new InvalidArgumentException("image path is empty or insecure"));
		}

// verify the image path will fit in the database
		if(strlen($newImagePath) > 100) {
			throw(new RangeException("image path too large"));
		}

// store the image path
		$this->imagePath = $newImagePath;
	}

	/**
	 * inserts this store into mySQL
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public function insert(&$mysqli) {
		// handle degenerate cases
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}
		// enforce the storeId is null (i.e., don't insert a category that already exists)
		if($this->storeId !== null) {
			throw(new mysqli_sql_exception("this store already exists"));
		}
		// create query template
		$query = "INSERT INTO store(profileId, creationDate, storeName, imagePath) VALUES (?, ?, ?, ?)";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}
		// bind the member variables to the place holders in the template
		$formattedDate = $this->creationDate->format("Y-m-d H:i:s");
		$wasClean = $statement->bind_param("isss", $this->profileId, $formattedDate, $this->storeName, $this->imagePath);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters:"));
		}
		// execute the statement
		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement"));
		}
		// update the null storeId with what mysql just gave us
		$this->storeId = $mysqli->insert_id;
		// clean up the statement
		$statement->close();
	}

	/**
	 * deletes this store from mysql
	 *
	 * @param resource $mysqli pointer to mysql connection, by reference
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public function delete(&$mysqli) {
		// handle degenerate cases
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}
		// enforce the storeId is not null (i.e., don't delete a store that has not been inserted)
		if($this->storeId === null) {
			throw(new mysqli_sql_exception("unable to delete a store that does not exist"));
		}
		// create query template
		$query = "DELETE FROM store WHERE storeId = ?";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}
		// bind the member variables to the place holder in the template
		$wasClean = $statement->bind_param("i", $this->storeId);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}
		// execute the statement
		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement"));
		}
		// clean up the statement
		$statement->close();
	}

	/**
	 * updates the category in mySQL
	 *
	 * @param resource $mysqli pointer to mysql connection, by reference
	 * @throws mysqli_sql_exception when mysql related errors occur
	 **/
	public function update(&$mysqli) {
		// handle degenerate cases
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}
		// enforce the storeId is not null (i.e., don't update a store that hasn't been inserted)
		if($this->storeId === null) {
			throw(new mysqli_sql_exception("unable to update a store that does not exist"));
		}
		// create a query template
		$query = "UPDATE store SET profileId = ?, creationDate = ?, storeName = ?, imagePath = ? WHERE storeId = ?";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}
		// bind the member variables to the place holders in the template
		$formattedDate = $this->creationDate->format("Y-m-d H:i:s");
		$wasClean = $statement->bind_param("isssi", $this->profileId, $formattedDate, $this->storeName, $this->imagePath, $this->storeId);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}
		// execute the statement
		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mysql statement: " . $statement->error));
		}
		// clean up the statement
		$statement->close();
	}

	/**
	 * gets the store by storeId
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @param int $storeId store id to search for
	 * @return mixed Store found or null if not found
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public static function getStoreByStoreId(&$mysqli, $storeId) {
// handle degenerate cases
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}

// sanitize the storeId before searching
		$storeId = filter_var($storeId, FILTER_VALIDATE_INT);
		if($storeId === false) {
			throw(new mysqli_sql_exception("store id is not an integer"));
		}
		if($storeId <= 0) {
			throw(new mysqli_sql_exception("store id is not positive"));
		}

// create query template
		$query = "SELECT storeId, profileId, creationDate, storeName, imagePath FROM store WHERE storeId = ?";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}

// bind the store id to the place holder in the template
		$wasClean = $statement->bind_param("i", $storeId);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}

// execute the statement
		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement: " . $statement->error));
		}

// get result from the SELECT query
		$result = $statement->get_result();
		if($result === false) {
			throw(new mysqli_sql_exception("unable to get result set"));
		}

// grab the store from mySQL
		try {
			$store = null;
			$row = $result->fetch_assoc();
			if($row !== null) {
				$store = new Store($row["storeId"], $row["profileId"], $row["storeName"], $row["imagePath"], $row["creationDate"]);
			}
		} catch(Exception $exception) {
// if the row couldn't be converted, rethrow it
			throw(new mysqli_sql_exception($exception->getMessage(), 0, $exception));
		}

// free up memory and return the result
		$result->free();
		$statement->close();
		return ($store);
	}
}