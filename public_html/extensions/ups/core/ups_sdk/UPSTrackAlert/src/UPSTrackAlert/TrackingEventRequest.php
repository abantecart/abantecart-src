<?php
/**
 * TrackingEventRequest
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
 * # Product Info  The UPS Track Alert API provides best in-class package tracking visibility with near real time event updates for an improved customer experience and stream line logistic management. Updates are pushed to the user as soon as available with no constant polling required, thereby improving operational efficiency. For more information on the UPS Track Alert API, please visit the <a href=\"https://developer.ups.com/api/reference/trackalertenhanced/product-info\" target=\"_blank\" rel=\"noopener\">Product Info</a> page. <br/><p>Try out UPS APIs with example requests using Postman. Explore API documentation and sample applications through GitHub.</p>  <a href=\"https://god.gw.postman.com/run-collection/29542085-7513df2c-af1b-4e5c-8b5d-5797d03a6a44?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D29542085-7513df2c-af1b-4e5c-8b5d-5797d03a6a44%26entityType%3Dcollection%26workspaceId%3D7e7595f0-4829-4f9a-aee1-75c126b9d417\" target=\"_blank\" rel=\"noopener noreferrer\">   <img src=\"https://run.pstmn.io/button.svg\" alt=\"Run In Postman\" style=\"width: 128px; height: 32px;\"></a> <a href=\"https://github.com/UPS-API\" target=\"_blank\" rel=\"noopener noreferrer\">   <img src=\"https://www.ups.com/assets/resources/webcontent/images/gitHubButton.svg\" alt=\"Open in GitHub\" style=\"width: 128px; height: 32px;\"> </a>  # Business Values  - **Enhanced Customer Experience**: Near real-time tracking information increases transparency, leading to higher customer satisfaction and trust. - **Operational Efficiency**: Eliminates the necessity for continuous polling, thus saving resources and improving system responsiveness. - **Data-Driven Decision Making**: Access to near real-time data can help businesses optimize their supply chain and make informed logistics decisions. - **Optimizing Cash Flow Through Near Real-Time Delivery Tracking**: Improve cash flow by knowing that deliveries occurred in near real time. - **Mitigating Fraud and Theft through Near Real-Time Package Status Monitoring**: Reduce fraud and theft by knowing any time something happens to your packages.  # How does it work once I am setup as a user? You submit up to 100 UPS 1Z package tracking numbers to the API at a time, using OAUTH in a JSON format.  Your submission needs to include the URL where Track Alert will send a message with any events that occur to your 1Z package for the next 14 days.  This saves you the effort of polling UPS to determine what is the current status of your package.  # FAQs - **How do I check if a subscription to a 1Z was successful?** >  Within 8 seconds of submitting a 1Z package tracking number to Track Alert, you will receive a message confirming successful and un-successful 1Z's.    - **I stopped receiving event messages after 2 weeks and my package hasn’t been delivered. Why?** >  Each 1Z subscription is valid for 14 days.  If the package has not been delivered within 14 days, you must resubscribe to the 1Z to continue receiving updates/events.  - **How do I get events that occurred prior to subscription?** >  Track Alert does not retain any history.  You should use the UPS Track API to receive history about your package.    - **How many 1Z tracking numbers can a subscriber subscribe to in one request?** >  You can subscribe to up to 100 1Z in each submission to the API.  A reply message will be sent via the API with details showing successful and unsuccessful 1Z's submissions.  - **What types of event data does Track Alert provide?** >  In addition to the expected local dates and times when the event occurred (including GMT date and time), you will receive details about the event that include status-type, status-code, status-description and status-description code.   Status types are:       M and MV = manifest information,        X = exception information (something out of the normal happened to your package, but it may still be delivered on time),        I = in-progress (on the way or moving normally through the UPS network),        U = update (there is an update to your package, normally the scheduled delivery has been updated, but it may still be delivered on time)       D = delivery information (loaded on delivery vehicle, out for delivery, delivered)   Status codes are a 2-character code that provide details about the event.  There is a list of these codes and their translations elsewhere on this portal.   Status descriptions are a very brief (a few words) describing the status code.   Status-description code is a overly simplified description of the event.  This description is intended for those who do not understand transportation.  - **What does the message look like?** >  This is what a message looks like for an event that is sent to your URL.  Not every field will have a value for every message.  We have converted the JSON format message to text format for clarity.       Those fields are:      1Z package tracking number      scheduled delivery date (this field maybe updated, example '20240905')     actual delivery date (this field is blank until the delivery event occurs)     actual delivery time (this field is blank until the delivery event occurs)     activity location city     activity location state/province     activity location postal code (this field is blank until the delivery event occurs)     activity location country     activity status type     activity status code     activity status description     activity status description code     local activity date     local activity time     GMT activity date     GMT activity time     delivery start time (example '150000')     delivery end time (example '170000')     delivery time description (example 'estimated delivery window' or 'end of day')     delivery photo (this field is only available for enhanced users)  - **Can I test this process?** >  Yes, there are two test 1Z's that you can submit, and resubmit that will send several events spaced 1 second apart.  Those two test 1Z's are 1ZCIETST0111111114 and 1ZCIETST0422222228.  Please ensure to use UPS Production CIE(https://wwwcie.ups.com/api/track/{version}). You can submit these 1Z's as often as you like. (no stress testing please.)    # Error Codes  | Error Code | HTTP Status | Description                                                                                                                                                                                              | |------------|-------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------| | VSS000     | 400         | Invalid request and The Subscription request has been rejected.                                                                                                                                          | | VSS002     | 400         | Missing transId.                                                                                                                                                                                         | | VSS003     | 400         | Please enter a valid Transaction ID, The Subscription request has been rejected.                                                                                                                         | | VSS004     | 400         | Missing transactionSrc.                                                                                                                                                                                  | | VSS006     | 400         | Please enter a valid Transaction SRC, The Subscription request has been rejected.                                                                                                                        | | VSS100     | 500         | We're sorry, the system is temporarily unavailable. Please try again later.                                                                                                                              | | VSS110     | 400         | Subscription request is empty or not present. The Subscription request has been rejected.                                                                                                                | | VSS200     | 400         | Tracking Number List is required. The Subscription request has been rejected.                                                                                                                            | | VSS210     | 400         | The Subscription request should have at least one valid tracking number. The Subscription request has been rejected.                                                                                     | | VSS215     | 400         | The 1Z tracking number that was submitted is not a valid CIE 1Z and has been rejected.                                                                                                                   | | VSS220     | 400         | You have submitted over 100 1Z numbers which is not allowed. The entire submission of 1Z numbers has been rejected. Please resubmit your request again using groups of no more than 100 1Z numbers.      | | VSS300     | 400         | Locale is required. The Subscription request has been rejected.                                                                                                                                          | | VSS310     | 400         | Please enter a valid locale. The Subscription request has been rejected.                                                                                                                                 | | VSS400     | 400         | Please enter a valid country code. The Subscription request has been rejected.                                                                                                                           | | VSS500     | 400         | Destination is required. The Subscription request has been rejected.                                                                                                                                     | | VSS600     | 400         | URL is empty or not present. The Subscription request has been rejected.                                                                                                                                 | | VSS610     | 400         | URL is too long. The Subscription request has been rejected.                                                                                                                                             | | VSS700     | 400         | Credential is empty or not present. The Subscription request has been rejected.                                                                                                                          | | VSS800     | 400         | CredentialType is empty or not present. The Subscription request has been rejected.                                                                                                                      | | VSS930     | 400         | Type is missing or invalid, The Subscription request has been rejected.
 *
 * OpenAPI spec version: 1.0.0
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.71
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
 * TrackingEventRequest Class Doc Comment
 *
 * @category Class
 * @description Package event update payload.
 * @package  UPS\UPSTrackAlert
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class TrackingEventRequest implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'TrackingEventRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'tracking_number' => '',
        'local_activity_date' => '',
        'local_activity_time' => '',
        'activity_location' => '\UPS\UPSTrackAlert\UPSTrackAlert\ActivityLocation',
        'activity_status' => '\UPS\UPSTrackAlert\UPSTrackAlert\ActivityStatus',
        'scheduled_delivery_date' => '',
        'actual_delivery_date' => '',
        'actual_delivery_time' => '',
        'gmt_activity_date' => '',
        'gmt_activity_time' => '',
        'delivery_start_time' => '',
        'delivery_end_time' => '',
        'delivery_time_description' => ''
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'tracking_number' => null,
        'local_activity_date' => null,
        'local_activity_time' => null,
        'activity_location' => null,
        'activity_status' => null,
        'scheduled_delivery_date' => null,
        'actual_delivery_date' => null,
        'actual_delivery_time' => null,
        'gmt_activity_date' => null,
        'gmt_activity_time' => null,
        'delivery_start_time' => null,
        'delivery_end_time' => null,
        'delivery_time_description' => null
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
        'tracking_number' => 'trackingNumber',
        'local_activity_date' => 'localActivityDate',
        'local_activity_time' => 'localActivityTime',
        'activity_location' => 'activityLocation',
        'activity_status' => 'activityStatus',
        'scheduled_delivery_date' => 'scheduledDeliveryDate',
        'actual_delivery_date' => 'actualDeliveryDate',
        'actual_delivery_time' => 'actualDeliveryTime',
        'gmt_activity_date' => 'gmtActivityDate',
        'gmt_activity_time' => 'gmtActivityTime',
        'delivery_start_time' => 'deliveryStartTime',
        'delivery_end_time' => 'deliveryEndTime',
        'delivery_time_description' => 'deliveryTimeDescription'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'tracking_number' => 'setTrackingNumber',
        'local_activity_date' => 'setLocalActivityDate',
        'local_activity_time' => 'setLocalActivityTime',
        'activity_location' => 'setActivityLocation',
        'activity_status' => 'setActivityStatus',
        'scheduled_delivery_date' => 'setScheduledDeliveryDate',
        'actual_delivery_date' => 'setActualDeliveryDate',
        'actual_delivery_time' => 'setActualDeliveryTime',
        'gmt_activity_date' => 'setGmtActivityDate',
        'gmt_activity_time' => 'setGmtActivityTime',
        'delivery_start_time' => 'setDeliveryStartTime',
        'delivery_end_time' => 'setDeliveryEndTime',
        'delivery_time_description' => 'setDeliveryTimeDescription'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'tracking_number' => 'getTrackingNumber',
        'local_activity_date' => 'getLocalActivityDate',
        'local_activity_time' => 'getLocalActivityTime',
        'activity_location' => 'getActivityLocation',
        'activity_status' => 'getActivityStatus',
        'scheduled_delivery_date' => 'getScheduledDeliveryDate',
        'actual_delivery_date' => 'getActualDeliveryDate',
        'actual_delivery_time' => 'getActualDeliveryTime',
        'gmt_activity_date' => 'getGmtActivityDate',
        'gmt_activity_time' => 'getGmtActivityTime',
        'delivery_start_time' => 'getDeliveryStartTime',
        'delivery_end_time' => 'getDeliveryEndTime',
        'delivery_time_description' => 'getDeliveryTimeDescription'
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
        $this->container['tracking_number'] = isset($data['tracking_number']) ? $data['tracking_number'] : null;
        $this->container['local_activity_date'] = isset($data['local_activity_date']) ? $data['local_activity_date'] : null;
        $this->container['local_activity_time'] = isset($data['local_activity_time']) ? $data['local_activity_time'] : null;
        $this->container['activity_location'] = isset($data['activity_location']) ? $data['activity_location'] : null;
        $this->container['activity_status'] = isset($data['activity_status']) ? $data['activity_status'] : null;
        $this->container['scheduled_delivery_date'] = isset($data['scheduled_delivery_date']) ? $data['scheduled_delivery_date'] : null;
        $this->container['actual_delivery_date'] = isset($data['actual_delivery_date']) ? $data['actual_delivery_date'] : null;
        $this->container['actual_delivery_time'] = isset($data['actual_delivery_time']) ? $data['actual_delivery_time'] : null;
        $this->container['gmt_activity_date'] = isset($data['gmt_activity_date']) ? $data['gmt_activity_date'] : null;
        $this->container['gmt_activity_time'] = isset($data['gmt_activity_time']) ? $data['gmt_activity_time'] : null;
        $this->container['delivery_start_time'] = isset($data['delivery_start_time']) ? $data['delivery_start_time'] : null;
        $this->container['delivery_end_time'] = isset($data['delivery_end_time']) ? $data['delivery_end_time'] : null;
        $this->container['delivery_time_description'] = isset($data['delivery_time_description']) ? $data['delivery_time_description'] : null;
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
     * Gets tracking_number
     *
     * @return 
     */
    public function getTrackingNumber()
    {
        return $this->container['tracking_number'];
    }

    /**
     * Sets tracking_number
     *
     * @param  $tracking_number The package's tracking number.
     *
     * @return $this
     */
    public function setTrackingNumber($tracking_number)
    {
        $this->container['tracking_number'] = $tracking_number;

        return $this;
    }

    /**
     * Gets local_activity_date
     *
     * @return 
     */
    public function getLocalActivityDate()
    {
        return $this->container['local_activity_date'];
    }

    /**
     * Sets local_activity_date
     *
     * @param  $local_activity_date The localized date of the activity. Format: YYYYMMDD
     *
     * @return $this
     */
    public function setLocalActivityDate($local_activity_date)
    {
        $this->container['local_activity_date'] = $local_activity_date;

        return $this;
    }

    /**
     * Gets local_activity_time
     *
     * @return 
     */
    public function getLocalActivityTime()
    {
        return $this->container['local_activity_time'];
    }

    /**
     * Sets local_activity_time
     *
     * @param  $local_activity_time The localized time of the activity. Format: HHMMSS (24 hr)
     *
     * @return $this
     */
    public function setLocalActivityTime($local_activity_time)
    {
        $this->container['local_activity_time'] = $local_activity_time;

        return $this;
    }

    /**
     * Gets activity_location
     *
     * @return \UPS\UPSTrackAlert\UPSTrackAlert\ActivityLocation
     */
    public function getActivityLocation()
    {
        return $this->container['activity_location'];
    }

    /**
     * Sets activity_location
     *
     * @param \UPS\UPSTrackAlert\UPSTrackAlert\ActivityLocation $activity_location activity_location
     *
     * @return $this
     */
    public function setActivityLocation($activity_location)
    {
        $this->container['activity_location'] = $activity_location;

        return $this;
    }

    /**
     * Gets activity_status
     *
     * @return \UPS\UPSTrackAlert\UPSTrackAlert\ActivityStatus
     */
    public function getActivityStatus()
    {
        return $this->container['activity_status'];
    }

    /**
     * Sets activity_status
     *
     * @param \UPS\UPSTrackAlert\UPSTrackAlert\ActivityStatus $activity_status activity_status
     *
     * @return $this
     */
    public function setActivityStatus($activity_status)
    {
        $this->container['activity_status'] = $activity_status;

        return $this;
    }

    /**
     * Gets scheduled_delivery_date
     *
     * @return 
     */
    public function getScheduledDeliveryDate()
    {
        return $this->container['scheduled_delivery_date'];
    }

    /**
     * Sets scheduled_delivery_date
     *
     * @param  $scheduled_delivery_date Original scheduled delivery date of the package. Format: YYYYMMDD
     *
     * @return $this
     */
    public function setScheduledDeliveryDate($scheduled_delivery_date)
    {
        $this->container['scheduled_delivery_date'] = $scheduled_delivery_date;

        return $this;
    }

    /**
     * Gets actual_delivery_date
     *
     * @return 
     */
    public function getActualDeliveryDate()
    {
        return $this->container['actual_delivery_date'];
    }

    /**
     * Sets actual_delivery_date
     *
     * @param  $actual_delivery_date Actual delivery date of the package. Format: YYYYMMDD (This field is blank until the delivery event occurs)
     *
     * @return $this
     */
    public function setActualDeliveryDate($actual_delivery_date)
    {
        $this->container['actual_delivery_date'] = $actual_delivery_date;

        return $this;
    }

    /**
     * Gets actual_delivery_time
     *
     * @return 
     */
    public function getActualDeliveryTime()
    {
        return $this->container['actual_delivery_time'];
    }

    /**
     * Sets actual_delivery_time
     *
     * @param  $actual_delivery_time Actual delivery time of the package. Format: HHMMSS (24 hr) (This field is blank until the delivery event occurs)
     *
     * @return $this
     */
    public function setActualDeliveryTime($actual_delivery_time)
    {
        $this->container['actual_delivery_time'] = $actual_delivery_time;

        return $this;
    }

    /**
     * Gets gmt_activity_date
     *
     * @return 
     */
    public function getGmtActivityDate()
    {
        return $this->container['gmt_activity_date'];
    }

    /**
     * Sets gmt_activity_date
     *
     * @param  $gmt_activity_date The GMT date of the activity. Format: YYYYMMDD
     *
     * @return $this
     */
    public function setGmtActivityDate($gmt_activity_date)
    {
        $this->container['gmt_activity_date'] = $gmt_activity_date;

        return $this;
    }

    /**
     * Gets gmt_activity_time
     *
     * @return 
     */
    public function getGmtActivityTime()
    {
        return $this->container['gmt_activity_time'];
    }

    /**
     * Sets gmt_activity_time
     *
     * @param  $gmt_activity_time The GMT time of the activity. Format: HHMMSS (24 hr)
     *
     * @return $this
     */
    public function setGmtActivityTime($gmt_activity_time)
    {
        $this->container['gmt_activity_time'] = $gmt_activity_time;

        return $this;
    }

    /**
     * Gets delivery_start_time
     *
     * @return 
     */
    public function getDeliveryStartTime()
    {
        return $this->container['delivery_start_time'];
    }

    /**
     * Sets delivery_start_time
     *
     * @param  $delivery_start_time The start time of a delivery. Format: HHMMSS (24 hr).
     *
     * @return $this
     */
    public function setDeliveryStartTime($delivery_start_time)
    {
        $this->container['delivery_start_time'] = $delivery_start_time;

        return $this;
    }

    /**
     * Gets delivery_end_time
     *
     * @return 
     */
    public function getDeliveryEndTime()
    {
        return $this->container['delivery_end_time'];
    }

    /**
     * Sets delivery_end_time
     *
     * @param  $delivery_end_time The end time of a window or the committed time or the delivered time. Format: HHMMSS (24 hr)
     *
     * @return $this
     */
    public function setDeliveryEndTime($delivery_end_time)
    {
        $this->container['delivery_end_time'] = $delivery_end_time;

        return $this;
    }

    /**
     * Gets delivery_time_description
     *
     * @return 
     */
    public function getDeliveryTimeDescription()
    {
        return $this->container['delivery_time_description'];
    }

    /**
     * Sets delivery_time_description
     *
     * @param  $delivery_time_description The date of this delivery detail.
     *
     * @return $this
     */
    public function setDeliveryTimeDescription($delivery_time_description)
    {
        $this->container['delivery_time_description'] = $delivery_time_description;

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
