# Installation

Download the Telsam.php file and include it into your project like this and instantiate the Telsam class.

```php
require_once "PATH/TO/LIB/Telsam.php";
$telsam = new Telsam;
```
Thats it!

# Usage
## sendSingle() Example
To send a single text message to a single phone number

```php

$telsam->setUsername("XXXXXXXXX); // websms.telsam.com.tr login username
$telsam->setPassword("XXXXXXXXX"); // websms.telsam.com.tr login password
$telsam->setOriginator("XXXXXXXX"); // One of the approved titles (originators)

$send = $telsam->sendSingle("5320000000", "Test message");

print_r($send);
```

## sendBulk() Example
To send a single text message to multiple phone numbers

$telsam->setUsername("XXXXXXXXX); // websms.telsam.com.tr login username
$telsam->setPassword("XXXXXXXXX"); // websms.telsam.com.tr login password
$telsam->setOriginator("XXXXXXXX"); // One of the approved titles (originators)

$numbers = array(
  "5420000000",
  "5320000000",
  "5550000000"
);

$send = $telsam->sendBulk($numbers, "Test bulk message");

print_r($send);

Both sendSingle() and sendBulk() methods returns an **array** with **status**, **message** and **data** keys.

**status** will be **success** if an sms order successfully created on Telsam. **data** parameter will carry **batch_id** (telsam job id), **paid** (paid credits for that job) and **balance** (remaining credits). Otherwise **data** will be an empty array.
