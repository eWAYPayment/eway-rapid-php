# Changelog

All notable changes will be documented in this file
## 1.4.1
- Support Apple Pay/ Google Pay Tokenization

## 1.4.0
 - Update PHP SDK - 3DS 2.0 - Add new error code support
 - Update PHP SDK - 3DS 2.0 - Increase HTTP timeout to 120 seconds

## 1.3.4
 - Added extra fields to support customising the Responsive Shared Page when creating and updating token customers.

## 1.3.3

 - Fix so that `SecuredCardData` is passed when doing a token customer update

## 1.3.2

 - Added `SecuredCardData` field to Customer object for token creation with Secure Fields etc.

## 1.3.1

 - Added `SecuredCardData` field to support Secure Fields, Visa Checkout, AMEX Express Checkout and Android Pay
 - `ThirdPartyWalletID` marked as deprecated, SecuredCardData should be used instead.

## 1.3.0

 - Added support for setting a Rapid version

## 1.2.2

 - Added support for a PSR-3 logger to log errors
 - Very basic input validation for some functions
 - Added handling of extra HTTP headers from proxies

## 1.2.1

 - Changed create and update customer to use MOTO for TransactionType to support not sending the CVN

## 1.2.0

 - Added support for Settlement Search
 - Now PSR2 compliant (added `composer phpcs` command to check)

## 1.1.3

 - Added `SaveCustomer` flag to Transactions to create a token when a transaction is completed

## 1.1.2

 - Removed exception thrown when Rapid field is not in SDK Model

## 1.1.1

 - Added update token customer function

## 1.0.1

 - Added manual install option and basic autoloader
 - Improved error handling of connection errors

## 1.0.0

 - First release
