import 'dart:io';

import 'package:device_info_plus/device_info_plus.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:logger/logger.dart';
import 'package:open_file/open_file.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/formdata/global_kyc_form_data.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:ovopay/environment.dart';
import 'package:path_provider/path_provider.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:url_launcher/url_launcher.dart';

class MyUtils {
  static void clearAllTypeFocusNodes() {
    try {
      FocusManager.instance.primaryFocus?.unfocus();
    } catch (e) {
      printE(e);
    }
  }

  static void splashScreen() {
    SystemChrome.setSystemUIOverlayStyle(
      SystemUiOverlayStyle(
        statusBarColor: MyColor.white,
        statusBarIconBrightness: Brightness.dark,
        systemNavigationBarColor: MyColor.white,
        systemNavigationBarIconBrightness: Brightness.light,
      ),
    );
  }

  static void allScreen() {
    SystemChrome.setSystemUIOverlayStyle(
      SystemUiOverlayStyle(
        statusBarColor: MyColor.white,
        statusBarIconBrightness: Brightness.dark,
        systemNavigationBarColor: MyColor.white,
        systemNavigationBarIconBrightness: Brightness.dark,
      ),
    );
  }

  static Brightness getOppositeBrightness(Color color) {
    // Calculate the relative luminance of the color
    double luminance = color.computeLuminance();

    // Return the opposite brightness
    return luminance > 0.5 ? Brightness.dark : Brightness.light;
  }

  static Color colorFromHex(String hexColor, {required Color defColor}) {
    try {
      final buffer = StringBuffer();
      if (hexColor.length == 6 || hexColor.length == 7) buffer.write('ff');
      buffer.write(hexColor.replaceFirst('#', ''));
      return Color(int.parse(buffer.toString(), radix: 16));
    } catch (e) {
      return defColor;
    }
  }

  String colorToHex(Color color, {bool includeHash = true}) {
    int alpha = (color.a * 255).round();
    int red = (color.r * 255).round();
    int green = (color.g * 255).round();
    int blue = (color.b * 255).round();

    return '${includeHash ? '#' : ''}'
        '${alpha.toRadixString(16).padLeft(2, '0')}'
        '${red.toRadixString(16).padLeft(2, '0')}'
        '${green.toRadixString(16).padLeft(2, '0')}'
        '${blue.toRadixString(16).padLeft(2, '0')}';
  }

  static String getOperationTitle(String value) {
    String number = value;
    RegExp regExp = RegExp(r'^(\d+)(\w+)$');
    Match? match = regExp.firstMatch(number);
    if (match != null) {
      String? num = match.group(1) ?? '';
      String? unit = match.group(2) ?? '';
      String title = '${MyStrings.last.tr} $num ${unit.capitalizeFirst}';
      return title.tr;
    } else {
      return value.tr;
    }
  }

  String maskSensitiveInformation(String input) {
    if (input.isEmpty) {
      return '';
    }

    final int maskLength = input.length ~/ 2; // Mask half of the characters.
    final String mask = '*' * maskLength;
    final String maskedInput = maskLength > 4 ? input.replaceRange(5, maskLength, mask) : input.replaceRange(0, maskLength, mask);
    return maskedInput;
  }

  static List<GlobalFormModel> dynamicFormSelectValueFormatter(
    List<GlobalFormModel>? dynamicFormList,
  ) {
    List<GlobalFormModel> mainFormList = [];

    if (dynamicFormList != null && dynamicFormList.isNotEmpty) {
      mainFormList.clear();

      for (var element in dynamicFormList) {
        if (element.type == 'select') {
          bool? isEmpty = element.options?.isEmpty;
          bool empty = isEmpty ?? true;
          if (element.options != null && empty != true) {
            if (!element.options!.contains(MyStrings.selectOne)) {
              element.options?.insert(0, MyStrings.selectOne);
            }

            element.selectedValue = element.options?.first;
            mainFormList.add(element);
          }
        } else {
          mainFormList.add(element);
        }
      }
    }
    return mainFormList;
  }

  List<Row> makeTwoPairWidget({required List<Widget> widgets}) {
    List<Row> pairs = [];
    for (int i = 0; i < widgets.length; i += 2) {
      Widget first = widgets[i];
      Widget? second = (i + 1 < widgets.length) ? widgets[i + 1] : const SizedBox();

      pairs.add(
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Expanded(child: first),
            const SizedBox(width: Dimensions.space15),
            Expanded(child: second),
          ],
        ),
      );
    }

    return pairs;
  }

  void stopLandscape() {
    SystemChrome.setPreferredOrientations([
      DeviceOrientation.portraitUp,
      DeviceOrientation.portraitDown,
    ]);
  }

  static Future<void> launchUrlToBrowser(String downloadUrl) async {
    try {
      final Uri url = Uri.parse(downloadUrl);
      if (!await launchUrl(url, mode: LaunchMode.externalApplication)) {
        throw Exception('Could not launch $url');
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  static bool isImage(String path) {
    if (path.contains('.jpg')) {
      return true;
    }
    if (path.contains('.png')) {
      return true;
    }
    if (path.contains('.jpeg')) {
      return true;
    }
    return false;
  }

  static bool isXlsx(String path) {
    if (path.contains('.xlsx')) {
      return true;
    }
    if (path.contains('.xls')) {
      return true;
    }
    if (path.contains('.xlx')) {
      return true;
    }
    return false;
  }

  static bool isDoc(String path) {
    // Define a list of supported file extensions
    const List<String> fileTypes = ['.pdf', '.doc', '.docx', '.xls', '.xlsx'];

    // Check if the path contains any of the file extensions
    for (String fileType in fileTypes) {
      if (path.contains(fileType)) {
        return true;
      }
    }
    return false;
  }

  static Icon getFileIcon(String path, {double size = 24.0, Color? color}) {
    // Map file types to specific icons and default colors
    if (path.endsWith('.pdf')) {
      return Icon(Icons.picture_as_pdf, color: color ?? Colors.red, size: size);
    } else if (path.endsWith('.doc') || path.endsWith('.docx')) {
      return Icon(Icons.description, color: color ?? Colors.blue, size: size);
    } else if (path.endsWith('.xls') || path.endsWith('.xlsx')) {
      return Icon(Icons.table_chart, color: color ?? Colors.green, size: size);
    } else if (path.endsWith('.jpg') || path.endsWith('.jpeg') || path.endsWith('.png')) {
      return Icon(Icons.image, color: color ?? Colors.orange, size: size);
    } else if (path.endsWith('.mp4') || path.endsWith('.mov')) {
      return Icon(Icons.videocam, color: color ?? Colors.purple, size: size);
    } else if (path.endsWith('.mp3') || path.endsWith('.wav')) {
      return Icon(Icons.audiotrack, color: color ?? Colors.teal, size: size);
    } else {
      // Default icon for other file types
      return Icon(
        Icons.insert_drive_file,
        color: color ?? Colors.grey,
        size: size,
      );
    }
  }

  static bool isURL(String urlString) {
    Uri? uri = Uri.tryParse(urlString);
    return uri != null && uri.hasScheme && uri.hasAuthority;
  }

  static TextInputType getInputTextFieldType(String type) {
    if (type == "email") {
      return TextInputType.emailAddress;
    } else if (type == "number") {
      return TextInputType.number;
    } else if (type == "url") {
      return TextInputType.url;
    }
    return TextInputType.text;
  }

  static bool getTextInputType(String type) {
    if (type == "text") {
      return true;
    } else if (type == "email") {
      return true;
    } else if (type == "number") {
      return true;
    } else if (type == "url") {
      return true;
    } else if (type == "textarea") {
      return true;
    }
    return false;
  }

  /// Get the default download directory for the platform
  static Future<Directory> getDefaultDownloadDirectory() async {
    try {
      if (Platform.isAndroid) {
        // Android: Use public Download directory
        Directory? directory = Directory('/storage/emulated/0/Download');
        if (await directory.exists()) {
          return directory;
        } else {
          throw Exception("Download directory not found");
        }
      } else if (Platform.isIOS) {
        // iOS: Use the app's document directory
        return await getApplicationDocumentsDirectory();
      } else {
        throw UnsupportedError("Platform not supported");
      }
    } catch (e) {
      // Fallback to a temporary directory in case of failure
      return await getTemporaryDirectory();
    }
  }
  // Helper function to check and request permissions

  Future<bool> checkAndRequestStoragePermission() async {
    if (Platform.isAndroid) {
      // Use device_info_plus to check Android version
      final deviceInfo = DeviceInfoPlugin();
      final androidInfo = await deviceInfo.androidInfo;
      int androidVersion = androidInfo.version.sdkInt;

      if (androidVersion >= 30) {
        // For Android 11 and higher (SDK 30+)
        var status = await Permission.mediaLibrary.status;
        if (!status.isGranted) {
          status = await Permission.mediaLibrary.request();
        }
        return status.isGranted;
      } else {
        // For Android 10 and below
        var status = await Permission.storage.status;
        if (!status.isGranted) {
          status = await Permission.storage.request();
        }
        return status.isGranted;
      }
    } else if (Platform.isIOS) {
      // On iOS, check photo library or media permissions
      var status = await Permission.storage.status;
      if (!status.isGranted) {
        status = await Permission.storage.request();
      }
      return status.isGranted;
    } else {
      // For other platforms (e.g., web, desktop), return true
      return true;
    }
  }

  Future<void> openFile(String path, String extension) async {
    final file = File(path);
    if (await file.exists()) {
      final result = await OpenFile.open(path);
      if (result.type == ResultType.permissionDenied) {
        CustomSnackBar.success(successList: ['File saved at: $path']);
      }
      if (result.type != ResultType.done) {
        if (result.type == ResultType.noAppToOpen) {
          // CustomSnackBar.error(errorList: [MyStrings.noDocOpenerApp, 'File saved at: $path']);
        }
      }
    } else {
      CustomSnackBar.error(errorList: [MyStrings.fileNotFound]);
    }
  }

  static String getUserAmount(
    String balance, {
    bool showFullText = false,
    bool showPercent = false,
    int? precision,
  }) {
    final currencySymbol = SharedPreferenceService.getCurrencySymbol().toLowerCase();
    // isFullText returns the ISO currency code (e.g. "XAF"), kept uppercase by convention.
    final fullTextCurrencySymbol = SharedPreferenceService.getCurrencySymbol(
      isFullText: true,
    );

    // Convert balance to double and apply precision
    final double parsedBalance = double.tryParse(balance) ?? 0.0;
    final String formattedBalance = _groupThousandsWithSpace(
      parsedBalance,
      precision ?? SharedPreferenceService.getAllowPrecision(),
    );

    return showPercent
        ? "$formattedBalance%"
        : showFullText
            ? "$formattedBalance $currencySymbol $fullTextCurrencySymbol"
            : "$formattedBalance $currencySymbol";
  }

  // Formats a number as "1 000" (space thousand separator, no comma) instead
  // of the locale-independent "1000.00" default, e.g. for FCFA display.
  static String _groupThousandsWithSpace(double value, int precision) {
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

  static bool checkTextIsOnlyNumber(String text) {
    try {
      if (RegExp(r'^[0-9]+$').hasMatch(text)) {
        return true;
      } else if (RegExp(r'^[a-zA-Z]+$').hasMatch(text)) {
        return false;
      } else {
        return false;
      }
    } catch (e) {
      return false;
    }
  }

  static bool checkPhoneNumberIsNumberAndValidLength(String text) {
    try {
      var minNumber = SharedPreferenceService.getMaxMobileNumberDigit();
      var maxNumber = 15;
      if (checkTextIsOnlyNumber(text)) {
        if (text.length >= minNumber && text.length <= maxNumber) {
          return true;
        } else {
          return false;
        }
      }
    } catch (e) {
      return false;
    }
    return false;
  }

  String? validateAmountForm({
    required String value,
    required double userCurrentBalance,
    required double minLimit,
    required double maxLimit,
    bool showCurrentBalance = true,
  }) {
    // Check if the value is empty
    if (value.isEmpty) {
      return MyStrings.kAmountNumberError.tr;
    }

    // Convert the value to a double for comparison
    double amount = AppConverter.formatNumberDouble(value);

    if (showCurrentBalance) {
      // Check if the amount exceeds the user current balance
      if (amount > userCurrentBalance) {
        return MyStrings.kAmountHigherError.tr;
      }
    }

    // Check if the amount exceeds the maximum limit
    if (amount > maxLimit) {
      return MyStrings.kAmountExceedMaxLimitError.tr;
    }

    // Check if the amount is less than the minimum limit
    if (amount < minLimit) {
      return MyStrings.kAmountExceedMinLimitError.tr;
    }

    // Return null if all validations pass
    return null;
  }

  List<String> quickMoneyStringList(
    double minLimit,
    double maxLimit, {
    double? step,
    int maxListLength = 5,
  }) {
    List<String> moneyList = [];

    // Determine the step size dynamically if step is null
    if (step == null) {
      // Determine the step size based on maxLimit if step is not provided
      if (maxLimit > 1000) {
        // For large maxLimit, use a larger step
        step = 500.00;
      } else if (maxLimit > 500) {
        // For medium maxLimit, use a medium step
        step = 100.00;
      } else {
        // For smaller maxLimit, use a smaller step
        step = 50.00;
      }
    }

    // Loop through the range from minLimit to maxLimit using the determined step
    for (double amount = minLimit; amount <= maxLimit; amount += step) {
      // Ensure the value is rounded to the nearest valid step value
      double roundedAmount = (amount / step).round() * step;

      // Skip adding 0
      if (roundedAmount > 0) {
        moneyList.add(
          roundedAmount.toStringAsFixed(0),
        ); // Format as integer string
      }

      // If the list reaches the maxListLength, break out of the loop
      if (moneyList.length >= maxListLength) {
        break;
      }
    }

    return moneyList;
  }
}

void printX(Object? object) {
  if (Environment.DEV_MODE) {
    // print(object);
    var logger = Logger();
    logger.i("$object");
  }
}

void printE(Object? object) {
  if (Environment.DEV_MODE) {
    // print(object);
    var logger = Logger();
    logger.e("$object");
  }
}

void printW(Object? object) {
  if (Environment.DEV_MODE) {
    // print(object);
    var logger = Logger();
    logger.w("$object");
  }
}
