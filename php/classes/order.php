<?php
/**
 * Model to connect with the order entity
 *
 * @author <fgoussin@cnm.edu>
 */
class Order {

	/**
	 * @var int $orderId id for the order. This is the primary key of the order entity.
	 */
	private $orderId;

	/**
	 * @var int $profileId id for the profile. This is a foreign key to the profile entity.
	 */
	private $profileId;

	/**
	 * @var string $imagePath image path of the order
	 */
	private $orderDate;


	/**
	 * constructor of this order
	 *
	 * @param int $newOrderId
	 * @param int $newProfileId
	 * @param string $newOrderDate
	 *
	 * @throws InvalidArgumentException if data types are not valid
	 * @throws RangeException if data values are out of bounds
	 */
	public function __construct($newOrderId, $newProfileId, $newOrderDate) {
		try {
			$this->setOrderId($newOrderId);
			$this->setProfileId($newProfileId);
			$this->setOrderDate($newOrderDate);
		} catch(InvalidArgumentException $invalidArgument) {
			throw(new InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));
		} catch(RangeException $range) {
			throw(new RangeException($range->getMessage(), 0, $range));
		}
	}

	/**
	 * accessor for the order id
	 *
	 * @return int value for the order id
	 */
	public function getOrderId() {
		return $this->orderId;
	}

	/**
	 * mutator for the order id
	 *
	 * @param int $newOrderId for the order id
	 * @throws InvalidArgumentException if data types are not valid
	 * @throws RangeException if $newOrderId is less than 0
	 */
	public function setOrderId($newOrderId) {
		if($newOrderId === null) {
			$this->orderId = null;
			return;
		}

		$newOrderId = filter_var($newOrderId, FILTER_VALIDATE_INT);
		if($newOrderId === false) {
			throw(new InvalidArgumentException("order id is not a valid integer"));
		}

		if($newOrderId <= 0) {
			throw(new RangeException("order id must be positive"));
		}

		$this->orderId = intval($newOrderId);
	}

	/**
	 * accessor for the profile id
	 *
	 * @return int value for the profile id
	 */
	public function getProfileId() {
		return $this->profileId;
	}

	/**
	 * mutator for the profile id
	 *
	 * @param int $newOrderId for the order id
	 * @throws InvalidArgumentException if data types are not valid
	 * @throws RangeException if $newProfileId is less than 0
	 */
	public function setProfileId($newProfileId) {
		if($newProfileId === null) {
			$this->profileId = null;
			return;
		}

		$newProfileId = filter_var($newProfileId, FILTER_VALIDATE_INT);
		if($newProfileId === false) {
			throw(new InvalidArgumentException("order id is not a valid integer"));
		}

		if($newProfileId <= 0) {
			throw(new RangeException("order id must be positive"));
		}

		$this->profileId = intval($newProfileId);
	}

	/**
	 * accessor method for order date
	 *
	 * @return DateTime value of order date
	 **/
	public function getOrderDate() {
		return ($this->orderDate);
	}
	/**
	 * mutator method for order date
	 *
	 * @param mixed $newOrderDate order date as a DateTime object or string (or null to load current time)
	 * @throws InvalidArgumentException if $newOrderDate is not a valid object or string
	 * @throws RangeException if $newOrderDate is a date that does not exist
	 **/
	public function setOrderDate($newOrderDate) {
		if($newOrderDate === null) {
			$this->orderDate = new DateTime();
			return;
		}
		// base case: if the date is a DateTime object, there's no work to be done
		if(is_object($newOrderDate) === true && get_class($newOrderDate) === "DateTime") {
			$this->orderDate = $newOrderDate;
			return;
		}
		// treat the date as a mySQL date string: Y-m-d H:i:s
		$newOrderDate = trim($newOrderDate);
		if((preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $newOrderDate, $matches)) !== 1) {
			throw(new InvalidArgumentException("order date is not a valid date"));
		}
		// verify the date is a valid calendar date
		$year = intval($matches[1]);
		$month = intval($matches[2]);
		$day = intval($matches[3]);
		$hour = intval($matches[4]);
		$minute = intval($matches[5]);
		$second = intval($matches[6]);
		if(checkdate($month, $day, $year) === false) {
			throw(new RangeException("order date $newOrderDate is not a Gregorian date"));
		}
		// verify the time is really a valid wall clock time
		if($hour < 0 || $hour >= 24 || $minute < 0 || $minute >= 60 || $second < 0 || $second >= 60) {
			throw(new RangeException("order date $newOrderDate is not a valid time"));
		}
		// store the order date
		$newOrderDate = DateTime::createFromFormat("Y-m-d H:i:s", $newOrderDate);
		$this->orderDate = $newOrderDate;
	}

	/**
	 * insert this order id into mySQL
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 */
	public function insert(&$mysqli) {
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}

		if($this->orderId !== null) {
			throw(new mysqli_sql_exception("not a new order"));
		}

		$query	 = "INSERT INTO order(profileId, orderDate) VALUES(?, ?)";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}

		$wasClean	  = $statement->bind_param("is", $this->profileId, $this->orderDate);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}

		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement: " . $statement->error));
		}

		$this->orderId = $mysqli->insert_id;
		$statement->close();
	}

	/**
	 * deletes this order from mySQL
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public function delete(&$mysqli) {
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}

		if($this->orderId === null) {
			throw(new mysqli_sql_exception("unable to delete a order that does not exist"));
		}

		$query	 = "DELETE FROM order WHERE orderId = ?";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}

		$wasClean = $statement->bind_param("i", $this->orderId);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}

		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement: " . $statement->error));
		}

		$statement->close();
	}

	/**
	 * updates this order in mySQL
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public function update(&$mysqli) {
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}

		if($this->orderId === null) {
			throw(new mysqli_sql_exception("unable to update a order that does not exist"));
		}

		$query	 = "UPDATE order SET profileId = ?, orderDate = ? WHERE orderId = ?";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}

		$wasClean	  = $statement->bind_param("isi", $this->profileId, $this->orderDate, $this->orderId);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}

		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement: " . $statement->error));
		}

		$statement->close();
	}

	/**
	 * gets the Order by name
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @param string $orderName order name to search for
	 * @return mixed array of Orders found, Orders found, or null if not found
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public static function getOrderByOrderName(&$mysqli, $orderName) {
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}

		$orderName = trim($orderName);
		$orderName = filter_var($orderName, FILTER_SANITIZE_STRING);

		$query	 = "SELECT orderId, profileId, imagePath, orderName, orderPrice, orderType, orderWeight FROM order WHERE orderName LIKE ?";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}

		// bind the order name to the place holder in the template
		$orderName = "%$orderName%";
		$wasClean = $statement->bind_param("s", $orderName);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}

		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement: " . $statement->error));
		}

		$result = $statement->get_result();
		if($result === false) {
			throw(new mysqli_sql_exception("unable to get result set"));
		}

		// build an array of order
		$orders = array();
		while(($row = $result->fetch_assoc()) !== null) {
			try {
				$order	= new Order(null, $row["profileId"], $row["imagePath"], $row["orderName"], $row["orderPrice"],
					$row["orderType"], $row["orderWeight"]);
				$orders[] = $order;
			}
			catch(Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new mysqli_sql_exception($exception->getMessage(), 0, $exception));
			}
		}

		$numberOfOrders = count($orders);
		if($numberOfOrders === 0) {
			return(null);
		} else if($numberOfOrders === 1) {
			return($orders[0]);
		} else {
			return($orders);
		}
	}

	/**
	 * gets the Order by type
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @param string $orderType order type to search for
	 * @return mixed array of Orders found, Orders found, or null if not found
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public static function getOrderByOrderType(&$mysqli, $orderType) {
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}

		$orderType = trim($orderType);
		$orderType = filter_var($orderType, FILTER_SANITIZE_STRING);

		$query	 = "SELECT orderId, profileId, imagePath, orderType, orderPrice, orderType, orderWeight FROM order WHERE orderType LIKE ?";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}

		// bind the order type to the place holder in the template
		$orderType = "%$orderType%";
		$wasClean = $statement->bind_param("s", $orderType);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}

		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement: " . $statement->error));
		}

		$result = $statement->get_result();
		if($result === false) {
			throw(new mysqli_sql_exception("unable to get result set"));
		}

		// build an array of order
		$orders = array();
		while(($row = $result->fetch_assoc()) !== null) {
			try {
				$order	= new Order(null, $row["profileId"], $row["imagePath"], $row["orderName"], $row["orderPrice"],
					$row["orderType"], $row["orderWeight"]);
			}
			catch(Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new mysqli_sql_exception($exception->getMessage(), 0, $exception));
			}
		}

		$numberOfOrders = count($orders);
		if($numberOfOrders === 0) {
			return(null);
		} else if($numberOfOrders === 1) {
			return($orders[0]);
		} else {
			return($orders);
		}
	}

	/**
	 * gets the Order by orderId
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @param int $orderId tweet content to search for
	 * @return mixed Order found or null if not found
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public static function getOrderByOrderId(&$mysqli, $orderId) {
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}

		// sanitize the orderId before searching
		$orderId = filter_var($orderId, FILTER_VALIDATE_INT);
		if($orderId === false) {
			throw(new mysqli_sql_exception("tweet id is not an integer"));
		}
		if($orderId <= 0) {
			throw(new mysqli_sql_exception("tweet id is not positive"));
		}

		$query	 = "SELECT orderId, profileId, tweetContent, tweetDate FROM tweet WHERE orderId = ?";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}

		$wasClean = $statement->bind_param("i", $orderId);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}

		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement: " . $statement->error));
		}

		// get result from the SELECT query
		$result = $statement->get_result();
		if($result === false) {
			throw(new mysqli_sql_exception("unable to get result set"));
		}

		// grab the tweet from mySQL
		try {
			$tweet = null;
			$row   = $result->fetch_assoc();
			if($row !== null) {
				$order	= new Order(null, $row["profileId"], $row["imagePath"], $row["orderName"], $row["orderPrice"],
					$row["orderType"], $row["orderWeight"]);
			}
		} catch(Exception $exception) {
			// if the row couldn't be converted, rethrow it
			throw(new mysqli_sql_exception($exception->getMessage(), 0, $exception));
		}

		// free up memory and return the result
		$result->free();
		$statement->close();
		return($order);
	}

	/**
	 * gets all Orders
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @return mixed array of Orders found or null if not found
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public static function getAllOrders(&$mysqli) {
		// handle degenerate cases
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}

		// create query template
		$query	 = "SELECT orderId, profileId, tweetContent, tweetDate FROM tweet";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
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

		// build an array of tweet
		$orders = array();
		while(($row = $result->fetch_assoc()) !== null) {
			try {
				$order	= new Order(null, $row["profileId"], $row["imagePath"], $row["orderName"], $row["orderPrice"],
					$row["orderType"], $row["orderWeight"]);
				$orders[] = $order;
			}
			catch(Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new mysqli_sql_exception($exception->getMessage(), 0, $exception));
			}
		}

		// count the results in the array and return:
		// 1) null if 0 results
		// 2) the entire array if >= 1 result
		$numberOfOrders = count($orders);
		if($numberOfOrders === 0) {
			return(null);
		} else {
			return($orders);
		}
	}
}