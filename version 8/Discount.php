<?php
class Discount {
    const STUDENT_DISCOUNT = 0.15;  // 15% discount for students
    const SENIOR_DISCOUNT = 0.20;   // 20% discount for seniors
    const SENIOR_AGE = 65;         // Age threshold for senior discount
    
    public static function calculateDiscount($price, $age = null, $isStudent = false) {
        $discountMessage = [];
        $totalDiscount = 0;
        
        if ($age !== null && $age >= self::SENIOR_AGE) {
            $totalDiscount = self::SENIOR_DISCOUNT;
            $discountMessage[] = "Senior Discount (20% off)";
        } elseif ($isStudent) {
            $totalDiscount = self::STUDENT_DISCOUNT;
            $discountMessage[] = "Student Discount (15% off)";
        }
        
        $discountAmount = $price * $totalDiscount;
        $finalPrice = $price - $discountAmount;
        
        return [
            'originalPrice' => $price,
            'discountAmount' => $discountAmount,
            'finalPrice' => $finalPrice,
            'discountMessage' => $discountMessage ? implode(", ", $discountMessage) : "No discount applied"
        ];
    }
}
?>
