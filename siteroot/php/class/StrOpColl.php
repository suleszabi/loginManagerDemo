<?php

    class StrOpColl {
        public static function isStringContainNumber(string $stringToCheck) {
            return (preg_match('/[0-9]/', $stringToCheck) != false);
        }

        public static function isStringContainLowercase(string $stringToCheck) {
            return (preg_match('/[a-z]/', $stringToCheck) != false);
        }

        public static function isStringContainUppercase(string $stringToCheck) {
            return (preg_match('/[A-Z]/', $stringToCheck) != false);
        }

        public static function checkStringLength(string $stringToCheck, int $minLength, int $maxLength) {
            return ((strlen($stringToCheck) >= $minLength) && (strlen($stringToCheck) <= $maxLength));
        }

        public static function isStringEmail(string $stringToCheck) {
            return (filter_var($stringToCheck, FILTER_VALIDATE_EMAIL) != false);
        }

        public static function isStringsAreEqual() {
            $strings = func_get_args();
            
            if(count($strings) < 2) {
                return false;
            }

            $stringsAreEqual = true;
            $index = 0;
            while($stringsAreEqual && $index < count($strings)) {
                for($i=$index+1; $i<count($strings); $i++) {
                    if(strval($strings[$index]) !== strval($strings[$i])) {
                        $stringsAreEqual = false;
                    }
                }
                $index++;
            }

            return $stringsAreEqual;
        }

        public static function generateRandomString(int $stringLength, bool $caseSensitive = false, bool $specialChars = false) {
            $possibleChars = "0123456789abcdefghijklmnopqrstuvwxyz";
            
            if($caseSensitive) {
                $possibleChars .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            }

            if($specialChars) {
                $possibleChars .= ",?;.:-_/*-+'\"`!%=()|[]<>&@{}";
            }

            $randomString = "";
            for($i=0; $i<$stringLength; $i++) {
                $randomString .= $possibleChars[rand(0, strlen($possibleChars)-1)];
            }

            return $randomString;
        }

        public static function encryptWithSalt(string $stringToEncrypt, string $salt) {
            return hash("sha512", md5($salt.$stringToEncrypt));
        }
    }

?>