<?php
/**
 * TrackSubsServiceResponse
 *
 * PHP version 5
 *
 * @category Class
 * @package  UPS\UPSTrackAlert
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * UPS Track Alert API
 *
 * # Product Info  The UPS Track Alert API provides best in-class package tracking visibility with near real time event updates for an improved customer experience and stream line logistic management. Updates are pushed to the user as soon as available with no constant polling required, thereby improving operational efficiency.<br /><a href=\"https://developer.ups.com/api/reference/trackalert/product-info\" target=\"_blank\" rel=\"noopener\">Product Info</a>  # Business Values  - **Enhanced Customer Experience**: Near Real-time tracking information increases transparency, leading to higher customer satisfaction and trust. - **Operational Efficiency**: Eliminates the necessity for continuous polling, thus saving computational resources and improving system responsiveness. - **Data-Driven Decision Making**: Access to timely data can help businesses optimize their supply chain and make informed logistics decisions. - **Optimizing Cash Flow Through Near Real-Time Delivery Tracking**: Improve cash flow by knowing the deliveries occurred in near real time. - **Mitigating Fraud and Theft through Near Real-Time Package Status Monitoring**: Reduce fraud and theft by knowing the status of the package.  # Error Codes  | Error Code | HTTP Status | Description                                                                                                                                                                                              | |------------|-------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------| | VSS000     | 400         | Invalid request and The Subscription request has been rejected.                                                                                                                                          | | VSS002     | 400         | Missing transId.                                                                                                                                                                                         | | VSS003     | 400         | Please enter a valid Transaction ID, The Subscription request has been rejected.                                                                                                                         | | VSS004     | 400         | Missing transactionSrc.                                                                                                                                                                                  | | VSS006     | 400         | Please enter a valid Transaction SRC, The Subscription request has been rejected.                                                                                                                        | | VSS100     | 500         | We're sorry, the system is temporarily unavailable. Please try again later.                                                                                                                              | | VSS110     | 400         | Subscription request is empty or not present. The Subscription request has been rejected.                                                                                                                | | VSS200     | 400         | Tracking Number List is required. The Subscription request has been rejected.                                                                                                                            | | VSS210     | 400         | The Subscription request should have at least one valid tracking number. The Subscription request has been rejected.                                                                                     | | VSS215     | 400         | The 1Z tracking number that was submitted is not a valid CIE 1Z and has been rejected.                                                                                                                   | | VSS220     | 400         | You have submitted over 100 1Z numbers which is not allowed. The entire submission of 1Z numbers has been rejected. Please resubmit your request again using groups of no more than 100 1Z numbers.      | | VSS300     | 400         | Locale is required. The Subscription request has been rejected.                                                                                                                                          | | VSS310     | 400         | Please enter a valid locale. The Subscription request has been rejected.                                                                                                                                 | | VSS400     | 400         | Please enter a valid country code. The Subscription request has been rejected.                                                                                                                           | | VSS500     | 400         | Destination is required. The Subscription request has been rejected.                                                                                                                                     | | VSS600     | 400         | URL is empty or not present. The Subscription request has been rejected.                                                                                                                                 | | VSS610     | 400         | URL is too long. The Subscription request has been rejected.                                                                                                                                             | | VSS700     | 400         | Credential is empty or not present. The Subscription request has been rejected.                                                                                                                          | | VSS800     | 400         | CredentialType is empty or not present. The Subscription request has been rejected.                                                                                                                      | | VSS930     | 400         | Type is missing or invalid, The Subscription request has been rejected.                                                                                                                                  |   # FAQs - **How do I check if a subscription to a 1Z was successful?** >  A message will be sent via the API call with details about any successful and failed subscriptions.  - **I stopped receiving event messages after 2 weeks and my package hasn’t been delivered. Why?** >  Each 1Z subscription is valid for 14 days If the package has not been delivered within 14 days, you must resubscribe to the 1Z to continue receiving updates/events.  - **How do I get events that occurred prior to subscription?** >  Each 1Z subscription is valid for 14 days If the package has not been delivered within 14 days, you must resubscribe to the 1Z to continue receiving updates/events.  - **How many 1Z tracking numbers can a subscriber subscribe to in one request?** >  A message will be sent via the API call with details about any successful and failed subscriptions.  - **What does this Track Alert code mean?** >  look up all codes on the Track Alert Codes file \"X\" type: exception codes \"I\" type: in transit codes \"M\" and \"MV\" type\": manifest codes \"U\" type: delivery update codes \"D\" type: delivery codes These codes can appear in more than one type (for example, a type X code may also appear with a type D code in the Track Alert message These codes typically appear with a different type code when a package is delivered to a UPS access point, returned to sender, or rerouted. The codes are updated monthly Ask for an updated listed if you discover a new code that does not have a description on the list. The primary or more common codes rarely change.
 *
 * OpenAPI spec version: 1.0.0
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.50
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace UPS\UPSTrackAlert\UPSTrackAlert;

use \ArrayAccess;
use \UPS\UPSTrackAlert\ObjectSerializer;

/**
 * TrackSubsServiceResponse Class Doc Comment
 *
 * @category Class
 * @package  UPS\UPSTrackAlert
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class TrackSubsServiceResponse implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'TrackSubsServiceResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'valid_tracking_numbers' => 'string[]',
        'invalid_tracking_numbers' => 'string[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'valid_tracking_numbers' => null,
        'invalid_tracking_numbers' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'valid_tracking_numbers' => 'validTrackingNumbers',
        'invalid_tracking_numbers' => 'invalidTrackingNumbers'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'valid_tracking_numbers' => 'setValidTrackingNumbers',
        'invalid_tracking_numbers' => 'setInvalidTrackingNumbers'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'valid_tracking_numbers' => 'getValidTrackingNumbers',
        'invalid_tracking_numbers' => 'getInvalidTrackingNumbers'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }



    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['valid_tracking_numbers'] = isset($data['valid_tracking_numbers']) ? $data['valid_tracking_numbers'] : null;
        $this->container['invalid_tracking_numbers'] = isset($data['invalid_tracking_numbers']) ? $data['invalid_tracking_numbers'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets valid_tracking_numbers
     *
     * @return string[]
     */
    public function getValidTrackingNumbers()
    {
        return $this->container['valid_tracking_numbers'];
    }

    /**
     * Sets valid_tracking_numbers
     *
     * @param string[] $valid_tracking_numbers List of tracking numbers with successful subscription created.
     *
     * @return $this
     */
    public function setValidTrackingNumbers($valid_tracking_numbers)
    {
        $this->container['valid_tracking_numbers'] = $valid_tracking_numbers;

        return $this;
    }

    /**
     * Gets invalid_tracking_numbers
     *
     * @return string[]
     */
    public function getInvalidTrackingNumbers()
    {
        return $this->container['invalid_tracking_numbers'];
    }

    /**
     * Sets invalid_tracking_numbers
     *
     * @param string[] $invalid_tracking_numbers List of tracking numbers associated with errors preventing subscription creation.
     *
     * @return $this
     */
    public function setInvalidTrackingNumbers($invalid_tracking_numbers)
    {
        $this->container['invalid_tracking_numbers'] = $invalid_tracking_numbers;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}
