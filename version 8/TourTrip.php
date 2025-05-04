<?php
class TourTrip {
    // Price ranges for different trip categories
    const BUDGET_MAX = 500;      // Maximum price for budget trips
    const STANDARD_MAX = 1000;    // Maximum price for standard trips
    const LUXURY_MAX = 2000;      // Maximum price for luxury trips
    
    public static function validatePrice($price) {
        if (!is_numeric($price)) {
            return [false, 'Price must be a number'];
        }
        
        if ($price <= 0) {
            return [false, 'Price must be greater than 0'];
        }
        
        if ($price > self::LUXURY_MAX) {
            return [false, 'Price exceeds maximum allowed (€2000)'];
        }
        
        // Categorize the trip
        if ($price <= self::BUDGET_MAX) {
            return [true, 'Budget Trip'];
        } elseif ($price <= self::STANDARD_MAX) {
            return [true, 'Standard Trip'];
        } else {
            return [true, 'Luxury Trip'];
        }
    }
    private $id;
    private $title;
    private $description;
    private $price;
    private $startDate;
    private $endDate;
    private $capacity;
    private $bookedSpots;
    private $imagePath;

    public function __construct($id, $title, $description, $price, $startDate, $endDate, $capacity = 20, $imagePath = '') {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->capacity = $capacity;
        $this->bookedSpots = 0;
        $this->imagePath = $imagePath;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        if (empty(trim($title))) {
            throw new InvalidArgumentException('Title cannot be empty');
        }
        $this->title = trim($title);
        return true;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        if (empty(trim($description))) {
            throw new InvalidArgumentException('Description cannot be empty');
        }
        $this->description = trim($description);
        return true;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        if (!is_numeric($price)) {
            throw new InvalidArgumentException('Price must be a number');
        }
        if ($price <= 0) {
            throw new InvalidArgumentException('Price must be positive');
        }
        if ($price > self::LUXURY_MAX) {
            throw new InvalidArgumentException('Price cannot exceed ' . self::LUXURY_MAX);
        }
        $this->price = $price;
        return true;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function getCapacity() {
        return $this->capacity;
    }

    public function getBookedSpots() {
        return (int)$this->bookedSpots;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function setImagePath($imagePath) {
        if (empty(trim($imagePath))) {
            throw new InvalidArgumentException('Image path cannot be empty');
        }
        $this->imagePath = trim($imagePath);
        return true;
    }

    public function setCapacity($capacity) {
        if (!is_numeric($capacity)) {
            throw new InvalidArgumentException('Capacity must be a number');
        }
        if ($capacity <= 0) {
            throw new InvalidArgumentException('Capacity must be positive');
        }
        if ($capacity < $this->bookedSpots) {
            throw new InvalidArgumentException('Cannot set capacity below number of booked spots');
        }
        $this->capacity = $capacity;
        return true;
    }

    public function getAvailableSpots() {
        return $this->capacity - $this->bookedSpots;
    }

    public function bookSpots($numberOfSpots) {
        if (!is_numeric($numberOfSpots)) {
            throw new InvalidArgumentException('Number of spots must be a number');
        }
        if ($numberOfSpots <= 0) {
            throw new InvalidArgumentException('Number of spots must be positive');
        }
        
        $numberOfSpots = (int)$numberOfSpots;
        if ($this->bookedSpots + $numberOfSpots > $this->capacity) {
            throw new InvalidArgumentException('Insufficient available spots');
        }
        
        $this->bookedSpots += $numberOfSpots;
        return true;
    }

    public function cancelSpots($numberOfSpots) {
        if ($numberOfSpots <= 0) {
            throw new InvalidArgumentException('Number of spots must be positive');
        }
        
        if ($numberOfSpots > $this->bookedSpots) {
            throw new InvalidArgumentException('Cannot cancel more spots than booked');
        }
        
        $this->bookedSpots -= $numberOfSpots;
        return true;
    }

    // Discount constants
    const EARLY_BIRD_DISCOUNT = 0.15;  // 15% off for early bookings
    const GROUP_DISCOUNT = 0.10;       // 10% off for groups
    const MIN_GROUP_SIZE = 5;          // Minimum size for group discount
    const MIN_TRIP_DURATION = 1;       // Minimum trip duration in days
    const MAX_TRIP_DURATION = 30;      // Maximum trip duration in days
    const BOOKING_WINDOW_DAYS = 365;   // How far in advance trips can be booked

    public function calculatePrice($isEarlyBird = false, $groupSize = 1) {
        if ($groupSize < 1) {
            throw new InvalidArgumentException('Invalid group size');
        }

        $basePrice = $this->price * $groupSize;
        $discount = 0;

        // Apply early bird discount if applicable
        if ($isEarlyBird) {
            $discount += self::EARLY_BIRD_DISCOUNT;
        }

        // Apply group discount if applicable
        if ($groupSize >= self::MIN_GROUP_SIZE) {
            $discount += self::GROUP_DISCOUNT;
        }

        // Calculate final price with combined discounts
        return $basePrice * (1 - $discount);
    }

    public function setDates($startDate, $endDate) {
        try {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $now = new DateTime();

            // Check if dates are in the future
            if ($start < $now) {
                throw new InvalidArgumentException('Trip dates must be in the future');
            }

            // Check if within booking window
            $maxBookingDate = (new DateTime())->modify('+' . self::BOOKING_WINDOW_DAYS . ' days');
            if ($start > $maxBookingDate) {
                throw new InvalidArgumentException('Trip cannot be booked more than ' . self::BOOKING_WINDOW_DAYS . ' days in advance');
            }

            // Check if end date is after start date
            if ($end <= $start) {
                throw new InvalidArgumentException('End date must be after start date');
            }

            // Check trip duration
            $duration = $start->diff($end)->days;
            if ($duration < self::MIN_TRIP_DURATION || $duration > self::MAX_TRIP_DURATION) {
                throw new InvalidArgumentException('Trip duration must be between ' . self::MIN_TRIP_DURATION . ' and ' . self::MAX_TRIP_DURATION . ' days');
            }

            $this->startDate = $startDate;
            $this->endDate = $endDate;
            return true;

        } catch (Exception $e) {
            if ($e instanceof InvalidArgumentException) {
                throw $e;
            }
            throw new InvalidArgumentException('Invalid date format');
        }
    }

    public function isValidDateRange($startDate, $endDate) {
        try {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $now = new DateTime();

            // Check if dates are in the future
            if ($start < $now) {
                return false;
            }

            // Check if within booking window
            $maxBookingDate = (new DateTime())->modify('+' . self::BOOKING_WINDOW_DAYS . ' days');
            if ($start > $maxBookingDate) {
                return false;
            }

            // Check if end date is after start date
            if ($end <= $start) {
                return false;
            }

            // Check trip duration
            $duration = $start->diff($end)->days;
            if ($duration < self::MIN_TRIP_DURATION || $duration > self::MAX_TRIP_DURATION) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false; // Invalid date format
        }
    }

    public function getDetails() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'departure_date' => $this->startDate,
            'arrival_time' => $this->endDate,
            'capacity' => $this->capacity,
            'booked_spots' => $this->bookedSpots,
            'image' => $this->imagePath
        ];
    }

    public function applyDiscount($percentage) {
        if ($percentage > 0 && $percentage < 100) {
            $this->cost -= $this->cost * ($percentage / 100);
        }
    }
}
