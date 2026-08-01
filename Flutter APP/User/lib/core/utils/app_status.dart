// ignore_for_file: constant_identifier_names

class AppStatus {
  //status
  static const String SUCCESS = "success";

  static const String PENDING = "0";
  static const String APPROVE = "1";
  //attention: write all status here

  //KYC
  static const String KYC_REQUIRED = "0";
  static const String KYC_APPROVED = "1";
  static const String KYC_PENDING = "2";
  static const String KYC_REJECTED = "5";
  // KYC END

  //Virtual card
  static const String VIRTUAL_CARD_ACTIVE = "1";
  static const String VIRTUAL_CARD_INACTIVE = "0";
  static const String VIRTUAL_CARD_CANCELED = "9";

  //User type
  static const String USER_TYPE_USER = "USER";
  static const String USER_TYPE_AGENT = "AGENT";
  static const String USER_TYPE_MERCHANT = "MERCHANT";
  //User type end

  //Bill pay status
  static const String UTILITY_BILL_PENDING = "0";
  static const String UTILITY_BILL_SUCCESSFUL = "1";
  static const String UTILITY_BILL_PROCESSING = "2";
  static const String UTILITY_BILL_REJECTED = "9";

  //Gift Card status
  static const String range = "RANGE";
  static const String fixed = "FIXED";

  //Invetment type
  static const String INVESTMENT_TYPE_FIXED = "1";
  static const String INVESTMENT_TYPE_RANGE = "0";
}
