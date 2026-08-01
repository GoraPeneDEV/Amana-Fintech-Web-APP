import 'package:flutter/services.dart';
import 'package:get/get.dart';
import '../utils/my_strings.dart';

class AppConverter {
  static String toCapitalized(String value) {
    return value.toLowerCase().capitalizeFirst ?? '';
  }

  static String roundDoubleAndRemoveTrailingZero(String value) {
    try {
      double number = double.parse(value);
      String b = number.toStringAsFixed(2).replaceFirst(RegExp(r'\.?0*$'), '');
      return b;
    } catch (e) {
      return value;
    }
  }

  static String twoDecimalPlaceFixedWithoutRounding(
    String value, {
    int precision = 2,
  }) {
    try {
      double number = double.parse(value);
      String b = number.toStringAsFixed(precision);
      return b;
    } catch (e) {
      return value;
    }
  }

  static String removeQuotationAndSpecialCharacterFromString(String value) {
    try {
      String formatedString = value.replaceAll('"', '').replaceAll('[', '').replaceAll(']', '');
      return formatedString;
    } catch (e) {
      return value;
    }
  }

  static String replaceUnderscoreWithSpace(String value) {
    try {
      String formatedString = value.replaceAll('_', ' ');
      String v = formatedString.split(" ").map((str) => str.capitalize).join(" ");
      return v;
    } catch (e) {
      return value;
    }
  }

  static String formatNumber(
    String value, {
    int precision = 2,
    bool forceShowPrecision = false,
  }) {
    try {
      double number = double.parse(value.trim());

      if (number == 0) return _groupThousands(0, 2); // Ensure zero is displayed as "0.00"

      if (forceShowPrecision) {
        return _groupThousands(number, precision);
      }

      // If it's a whole number, return without decimals
      if (number % 1 == 0) {
        return _groupThousands(number, 0);
      }

      // Otherwise, return with the defined precision
      return _groupThousands(number, precision);
    } catch (e) {
      return _groupThousands(0, 2); // Default to "0.00" for invalid input
    }
  }

  // Groups the integer part with spaces ("1 000") instead of commas, e.g. for FCFA display.
  static String _groupThousands(double value, int precision) {
    final bool isNegative = value < 0;
    final List<String> parts = value.abs().toStringAsFixed(precision).split('.');
    final String digits = parts[0];
    final StringBuffer grouped = StringBuffer();
    for (int i = 0; i < digits.length; i++) {
      if (i > 0 && (digits.length - i) % 3 == 0) grouped.write(' ');
      grouped.write(digits[i]);
    }
    final String result = parts.length > 1 ? '${grouped.toString()}.${parts[1]}' : grouped.toString();
    return isNegative ? '-$result' : result;
  }

  static double formatNumberDouble(String value, {int precision = 2}) {
    try {
      double number = double.parse(value);
      double b = number.toPrecision(precision);
      return b;
    } catch (e) {
      return 0.0;
    }
  }

  static String getFormattedDateWithStatus(String inputValue) {
    String value = inputValue;
    try {
      var list = inputValue.split(' ');
      var dateSection = list[0].split('-');
      var timeSection = list[1].split(':');
      int year = int.parse(dateSection[0]);
      int month = int.parse(dateSection[1]);
      int day = int.parse(dateSection[2]);
      int hour = int.parse(timeSection[0]);
      int minute = int.parse(timeSection[1]);
      int second = int.parse(timeSection[2]);
      final startTime = DateTime(year, month, day, hour, minute, second);
      final currentTime = DateTime.now();

      int dayDef = currentTime.difference(startTime).inDays;
      int hourDef = currentTime.difference(startTime).inHours;
      final minDef = currentTime.difference(startTime).inMinutes;
      final secondDef = currentTime.difference(startTime).inSeconds;

      if (dayDef == 0) {
        if (hourDef <= 0) {
          if (minDef <= 0) {
            value = '$secondDef ${MyStrings.secondAgo}'.tr;
          } else {
            value = '$hourDef ${MyStrings.minutesAgo}'.tr;
          }
        } else {
          value = '$hourDef ${MyStrings.hourAgo}'.tr;
        }
      } else {
        value = '$dayDef ${MyStrings.daysAgo}'.tr;
      }
    } catch (e) {
      value = inputValue;
    }

    return value;
  }

  static String getTrailingExtension(int number) {
    List<String> list = [
      'th',
      'st',
      'nd',
      'rd',
      'th',
      'th',
      'th',
      'th',
      'th',
      'th',
    ];
    if (((number % 100) >= 11) && ((number % 100) <= 13)) {
      return '${number}th';
    } else {
      int value = (number % 10).toInt();
      return '$number${list[value]}';
    }
  }

  static String addLeadingZero(String value) {
    return value.padLeft(2, '0');
  }

  static String sum(String first, String last, {int precision = 2}) {
    double firstNum = double.tryParse(first) ?? 0;
    double secondNum = double.tryParse(last) ?? 0;
    double result = firstNum + secondNum;
    String formatedResult = formatNumber(
      result.toString(),
      precision: precision,
    );
    return formatedResult;
  }

  static String showPercent(String curSymbol, String s) {
    double value = 0;
    value = double.tryParse(s) ?? 0;
    if (value > 0) {
      return ' + $curSymbol$value';
    } else {
      return '';
    }
  }

  static String mul(String first, String second) {
    double result = (double.tryParse(first) ?? 0) * (double.tryParse(second) ?? 0);
    return AppConverter.formatNumber(result.toString());
  }

  static String calculateRate(String amount, String rate, {int precision = 2}) {
    double result = (double.tryParse(amount) ?? 0) / (double.tryParse(rate) ?? 0);
    return AppConverter.formatNumber(result.toString(), precision: precision);
  }
}

extension StringCasingExtension on String {
  bool get isEmptyString => trim().isEmpty;

  String toCapitalized() => length > 0 ? '${this[0].toUpperCase()}${substring(1).toLowerCase()}' : '';
  String toTitleCase() => replaceAll(
        RegExp(' +'),
        ' ',
      ).split(' ').map((str) => str.toCapitalized()).join(' ');

  /// Masks a mobile number, keeping the first and last few digits visible.
  /// Example: "1234567890".toMask() -> "123****890"
  String toNumberMask({
    int unmaskedPrefix = 32,
    int unmaskedSuffix = 3,
    String maskChar = '*',
  }) {
    if (length <= unmaskedPrefix + unmaskedSuffix) {
      // If the string is too short, return it as is.
      return this;
    }

    final prefix = substring(0, unmaskedPrefix);
    final suffix = substring(length - unmaskedSuffix);
    final mask = List.filled(length - unmaskedPrefix - unmaskedSuffix, maskChar).join();

    return '$prefix$mask$suffix';
  }

  String removeSpecialCharacters() {
    return replaceAll(RegExp(r'[^a-zA-Z0-9_]'), '');
  }

  String cleanAndTitleCase() {
    return replaceAll('_', ' ') // Step 1: Replace _ with space
        .replaceAll(RegExp(r'[^a-zA-Z0-9 ]'), '') // Step 2: Remove special characters except space
        .toLowerCase() // Lowercase first
        .split(' ') // Split into words
        .map((word) => word.isNotEmpty
            ? word[0].toUpperCase() + word.substring(1) // Capitalize first letter
            : '')
        .join(' '); // Join back to string
  }

  String toFormattedPhoneNumber({int digitsFromEnd = 10}) {
    // Remove unwanted characters (spaces, dashes, etc.)
    final cleanedNumber = replaceAll(RegExp(r'[^\d]'), '');

    // Check if the number has enough digits
    if (cleanedNumber.length <= digitsFromEnd) {
      return cleanedNumber; // Return the full number if it's too short
    }

    // Extract the desired digits from the end
    return cleanedNumber.substring(cleanedNumber.length - digitsFromEnd);
  }

  ///replace key value
  ///from string and get full data
  ///
  /// example: PRODUCT NAME ${product.name} and Price ${product.price}
  String rKv(Map<String, String> replacements) {
    String result = this;
    replacements.forEach((key, value) {
      result = result.replaceAll("{$key}", value);
    });
    return result;
  }

  String removeNull() {
    String text = this;
    String result = text.contains('null') ? text.replaceAll('null', '') : text;
    return result;
  }

  /// Returns true if the string represents a numeric value equal to 0.
  bool get isZero {
    try {
      return double.parse(this) == 0;
    } catch (e) {
      return false;
    }
  }
}

class MaxValueInputFormatter extends TextInputFormatter {
  final double maxValue;

  MaxValueInputFormatter({required this.maxValue});

  @override
  TextEditingValue formatEditUpdate(
    TextEditingValue oldValue,
    TextEditingValue newValue,
  ) {
    String text = newValue.text;

    // Allow only numbers and one decimal point
    if (!RegExp(r'^\d*\.?\d*$').hasMatch(text)) {
      return oldValue;
    }

    // Convert to double for validation
    double? value = double.tryParse(text);
    if (value != null && value > maxValue) {
      return oldValue; // Prevent entering values greater than maxValue
    }

    return newValue;
  }
}

extension JsonMessageToStringList on Object? {
  List<String> toStringList() {
    if (this is List) {
      // Safely cast each element to String
      return (this as List<dynamic>).map((x) => x.toString()).toList();
    }
    return [];
  }
}
